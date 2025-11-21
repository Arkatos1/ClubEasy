<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Xoco70\LaravelTournaments\Exceptions\TreeGenerationException;
use Xoco70\LaravelTournaments\Models\Category;
use Xoco70\LaravelTournaments\Models\Championship;
use Xoco70\LaravelTournaments\Models\ChampionshipSettings;
use Xoco70\LaravelTournaments\Models\Competitor;
use Xoco70\LaravelTournaments\Models\FightersGroup;
use Xoco70\LaravelTournaments\Models\Team;
use Xoco70\LaravelTournaments\Models\Tournament;
use Xoco70\LaravelTournaments\Models\Venue;

class TreeController extends Controller
{
    /**
     * Display a listing of trees.
     */
    public function index()
    {
        $tournaments = Tournament::with([
            'championships.settings',
            'championships.category',
            'championships.competitors.user',
            'championships.teams',
            'championships.fightersGroups.fights'
        ])->latest()->get();

        return view('tournaments.index', compact('tournaments'));
    }

    /**
     * Build Tree.
     */
    public function store(Request $request)
    {
        $request->validate([
            'numFighters' => 'required|integer|min:2|max:128',
            'tree_type' => 'required|integer|in:1,2,3',
            'isTeam' => 'sometimes|boolean',
            'tournament_id' => 'nullable|exists:tournament,id'
        ]);

        // If tournament_id is provided, use existing tournament
        if ($request->tournament_id) {
            $tournament = Tournament::find($request->tournament_id);
        } else {
            // Clean existing data for new tournaments
            $this->cleanChampionshipData();
        }

        $numFighters = $request->numFighters;
        $isTeam = $request->isTeam ?? 0;

        try {
            if (isset($tournament)) {
                $championship = $this->addToExistingTournament($tournament, $request, $isTeam, $numFighters);
            } else {
                $championship = $this->createNewTournament($request, $isTeam, $numFighters);
            }

            $generation = $championship->chooseGenerationStrategy();
            $generation->run();

            \Log::info('Tournament tree generated successfully', [
                'championship_id' => $championship->id,
                'num_fighters' => $numFighters,
                'is_team' => $isTeam,
                'tree_type' => $request->tree_type
            ]);

            return back()->with('success', 'Tournament tree generated successfully!');

        } catch (TreeGenerationException $e) {
            \Log::error('Tree generation failed', ['error' => $e->getMessage()]);
            return redirect()->back()->withErrors(['Tree generation failed: ' . $e->getMessage()]);
        } catch (\Exception $e) {
            \Log::error('Unexpected error during tree generation', ['error' => $e->getMessage()]);
            return redirect()->back()->withErrors(['Unexpected error: ' . $e->getMessage()]);
        }
    }

    /**
     * Create a new tournament with championship
     */
    protected function createNewTournament(Request $request, $isTeam, $numFighters)
    {
        // Clean existing data first
        $this->cleanChampionshipData();

        // Create unique tournament name
        $tournamentName = ($isTeam ? 'Team' : 'Individual') . ' Tournament - ' . now()->format('M j, Y H:i');

        // Create tournament
        $tournament = Tournament::create([
            'name' => $tournamentName,
            'slug' => Str::slug($tournamentName),
            'dateIni' => now(),
            'dateFin' => now()->addDays(3),
            'level_id' => 1,
            'type' => 1,
            'venue_id' => $this->getOrCreateVenue(),
            'user_id' => $this->getOrCreateUser(),
        ]);

        return $this->createChampionship($tournament, $request, $isTeam, $numFighters);
    }

    /**
     * Add championship to existing tournament
     */
    protected function addToExistingTournament(Tournament $tournament, Request $request, $isTeam, $numFighters)
    {
        // Clean only data for this tournament
        $this->cleanTournamentData($tournament->id);

        return $this->createChampionship($tournament, $request, $isTeam, $numFighters);
    }

    /**
     * Create championship for tournament
     */
    protected function createChampionship(Tournament $tournament, Request $request, $isTeam, $numFighters)
    {
        // Get or create category
        $category = Category::firstOrCreate(
            ['isTeam' => $isTeam],
            ['name' => $isTeam ? 'Team Category' : 'Individual Category']
        );

        // Create championship with unique name
        $championshipName = ($isTeam ? 'Team' : 'Individual') . ' Championship - ' . now()->format('H:i:s');

        $championship = Championship::create([
            'tournament_id' => $tournament->id,
            'category_id' => $category->id,
            'name' => $championshipName
        ]);

        // Add fighters/teams
        if ($isTeam) {
            $this->createTeams($championship, $numFighters);
        } else {
            $this->createCompetitors($championship, $numFighters);
        }

        // Create settings
        ChampionshipSettings::create([
            'championship_id' => $championship->id,
            'treeType' => $request->tree_type ?? 1,
            'hasPreliminary' => $request->hasPreliminary ?? 0,
            'preliminaryGroupSize' => $request->preliminary_group_size ?? 3,
            'fightingAreas' => $request->fighting_areas ?? 1,
            'limitByEntity' => $request->limit_by_field ?? 0,
            'teamSize' => $request->team_size ?? 1,
        ]);

        return $championship->fresh(['settings', 'category', 'fightersGroups.fights', 'competitors', 'teams']);
    }

    private function cleanChampionshipData()
    {
        DB::table('fight')->delete();
        DB::table('fighters_groups')->delete();
        DB::table('fighters_group_competitor')->delete();
        DB::table('fighters_group_team')->delete();
        DB::table('competitor')->delete();
        DB::table('team')->delete();
    }

    private function cleanTournamentData($tournamentId)
    {
        // Get all championships for this tournament
        $championshipIds = Championship::where('tournament_id', $tournamentId)->pluck('id');

        if ($championshipIds->count() > 0) {
            DB::table('fight')->whereIn('fighters_group_id', function($query) use ($championshipIds) {
                $query->select('id')->from('fighters_groups')->whereIn('championship_id', $championshipIds);
            })->delete();

            DB::table('fighters_groups')->whereIn('championship_id', $championshipIds)->delete();
            DB::table('fighters_group_competitor')->whereIn('fighters_group_id', function($query) use ($championshipIds) {
                $query->select('id')->from('fighters_groups')->whereIn('championship_id', $championshipIds);
            })->delete();
            DB::table('fighters_group_team')->whereIn('fighters_group_id', function($query) use ($championshipIds) {
                $query->select('id')->from('fighters_groups')->whereIn('championship_id', $championshipIds);
            })->delete();
            DB::table('competitor')->whereIn('championship_id', $championshipIds)->delete();
            DB::table('team')->whereIn('championship_id', $championshipIds)->delete();
            DB::table('championship_settings')->whereIn('championship_id', $championshipIds)->delete();

            // Delete the championships
            Championship::where('tournament_id', $tournamentId)->delete();
        }
    }

    /**
     * Get or create venue
     */
    protected function getOrCreateVenue()
    {
        try {
            $venue = Venue::first();
            if (!$venue) {
                $venue = Venue::create([
                    'venue_name' => 'Default Venue',
                    'city' => 'Default City',
                ]);
            }
            return $venue->id;
        } catch (\Exception $e) {
            return 1;
        }
    }

    /**
     * Get or create user
     */
    protected function getOrCreateUser()
    {
        try {
            $user = User::first();
            if (!$user) {
                $user = User::create([
                    'name' => 'Admin User',
                    'email' => 'admin@example.com',
                    'password' => bcrypt('password'),
                ]);
            }
            return $user->id;
        } catch (\Exception $e) {
            return 1;
        }
    }

    /**
     * Create teams for the championship
     */
    protected function createTeams(Championship $championship, $numFighters)
    {
        for ($i = 1; $i <= $numFighters; $i++) {
            Team::create([
                'championship_id' => $championship->id,
                'name' => 'Team ' . $i,
                'short_id' => $i
            ]);
        }
    }

    /**
     * Create competitors for the championship - USING ACTUAL USERS
     */
    protected function createCompetitors(Championship $championship, $numFighters)
    {
        // Get available users from the database
        $availableUsers = User::inRandomOrder()->limit($numFighters)->get();

        // If we don't have enough users, create some new ones
        if ($availableUsers->count() < $numFighters) {
            $usersNeeded = $numFighters - $availableUsers->count();
            \Log::info("Need to create {$usersNeeded} new users");

            for ($i = 0; $i < $usersNeeded; $i++) {
                $newUserNumber = $availableUsers->count() + $i + 1;
                User::create([
                    'name' => 'Competitor ' . $newUserNumber,
                    'email' => 'competitor' . $newUserNumber . '@example.com',
                    'password' => bcrypt('password'),
                    'email_verified_at' => now(),
                ]);
            }

            // Get all available users again (including newly created ones)
            $availableUsers = User::inRandomOrder()->limit($numFighters)->get();
        }

        \Log::info("Creating {$availableUsers->count()} competitors for championship {$championship->id}");

        // Create competitors using actual users
        foreach ($availableUsers as $index => $user) {
            \Log::info("Adding user as competitor: {$user->name} (ID: {$user->id})");

            DB::table('competitor')->insert([
                'championship_id' => $championship->id,
                'user_id' => $user->id,
                'short_id' => $index + 1,
                'confirmed' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        \Log::info("Successfully created {$availableUsers->count()} competitors");
    }

    /**
     * Update fight results
     */
    public function update(Request $request, Championship $championship)
    {
        try {
            $numFighter = 0;
            $query = FightersGroup::with('fights')
                ->where('championship_id', $championship->id);

            $fighters = $request->singleElimination_fighters ?? [];
            $scores = $request->score ?? [];

            if ($championship->hasPreliminary()) {
                $query = $query->where('round', '>', 1);
                $fighters = $request->preliminary_fighters ?? [];
            }

            $groups = $query->get();

            foreach ($groups as $group) {
                foreach ($group->fights as $fight) {
                    if (isset($fighters[$numFighter])) {
                        $fight->c1 = $fighters[$numFighter];
                        $fight->winner_id = $this->getWinnerId($fighters, $scores, $numFighter);
                        $numFighter++;
                    }

                    if (isset($fighters[$numFighter])) {
                        $fight->c2 = $fighters[$numFighter];
                        if ($fight->winner_id == null) {
                            $fight->winner_id = $this->getWinnerId($fighters, $scores, $numFighter);
                        }
                        $numFighter++;
                    }
                    $fight->save();
                }
            }

            return back()->with('success', 'Fight results updated successfully!');

        } catch (\Exception $e) {
            \Log::error('Error updating fight results', [
                'championship_id' => $championship->id,
                'error' => $e->getMessage()
            ]);

            return back()->withErrors(['Error updating fights: ' . $e->getMessage()]);
        }
    }

    private function getWinnerId($fighters, $scores, $numFighter)
    {
        return isset($scores[$numFighter]) && $scores[$numFighter] != null
            ? $fighters[$numFighter]
            : null;
    }

    /**
     * Delete a tournament
     */
    public function destroyTournament(Tournament $tournament)
    {
        try {
            $tournamentName = $tournament->name;
            $this->cleanTournamentData($tournament->id);
            $tournament->delete();

            return back()->with('success', "Tournament '{$tournamentName}' deleted successfully!");
        } catch (\Exception $e) {
            \Log::error('Error deleting tournament', ['error' => $e->getMessage()]);
            return back()->withErrors(['Error deleting tournament: ' . $e->getMessage()]);
        }
    }

    /**
     * Delete a championship
     */
    public function destroyChampionship(Championship $championship)
    {
        try {
            $championshipName = $championship->name;
            $tournamentId = $championship->tournament_id;

        // Clean championship data
        DB::table('fight')->whereIn('fighters_group_id', function($query) use ($championship) {
            $query->select('id')->from('fighters_groups')->where('championship_id', $championship->id);
        })->delete();

        DB::table('fighters_groups')->where('championship_id', $championship->id)->delete();
        DB::table('fighters_group_competitor')->whereIn('fighters_group_id', function($query) use ($championship) {
            $query->select('id')->from('fighters_groups')->where('championship_id', $championship->id);
        })->delete();
        DB::table('fighters_group_team')->whereIn('fighters_group_id', function($query) use ($championship) {
            $query->select('id')->from('fighters_groups')->where('championship_id', $championship->id);
        })->delete();
        DB::table('competitor')->where('championship_id', $championship->id)->delete();
        DB::table('team')->where('championship_id', $championship->id)->delete();
        DB::table('championship_settings')->where('championship_id', $championship->id)->delete();

        $championship->delete();

        // Check if tournament has any other championships
        $remainingChampionships = Championship::where('tournament_id', $tournamentId)->count();
        if ($remainingChampionships == 0) {
            Tournament::where('id', $tournamentId)->delete();
        }

            return back()->with('success', "Championship '{$championshipName}' deleted successfully!");
        } catch (\Exception $e) {
            \Log::error('Error deleting championship', ['error' => $e->getMessage()]);
            return back()->withErrors(['Error deleting championship: ' . $e->getMessage()]);
        }
    }
}
