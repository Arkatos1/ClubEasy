<?php

namespace App\Http\Controllers;

use App\Models\Event;
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
     * Display a listing of trees (Management interface for trainers/admins).
     */
    public function index()
    {
        // Check if user can manage tournaments
        if (!auth()->user() || (!auth()->user()->hasRole('trainer') && !auth()->user()->hasRole('administrator'))) {
            return redirect()->route('tournaments.list')->withErrors(['error' => __('Only trainers and administrators can manage tournaments')]);
        }

        $tournaments = Tournament::with([
            'championships.settings',
            'championships.category',
            'championships.competitors.user',
            'championships.teams',
            'championships.fightersGroups.fights'
        ])->latest()->get();

        $canManage = true; // This is the management interface

        return view('tournaments.index', compact('tournaments', 'canManage'));
    }


    /**
     * Show tournament details
     */
    public function show(Championship $championship)
    {
        $user = auth()->user();
        $isRegistered = $user ? $championship->competitors()->where('user_id', $user->id)->exists() : false;
        $hasActiveMembership = $user ? $user->hasActiveMembership() : false;
        $canManage = $user && ($user->hasRole('trainer') || $user->hasRole('administrator'));

        return view('tournaments.show', compact('championship', 'isRegistered', 'hasActiveMembership', 'canManage'));
    }

    /**
     * Show tournament list for members (Public interface).
     */
    public function list()
    {
        $tournaments = Tournament::with([
            'championships.settings',
            'championships.category',
            'championships.competitors.user',
            'championships.teams'
        ])->latest()->get();

        $user = auth()->user();
        $hasActiveMembership = $user ? $user->hasActiveMembership() : false;
        $canManage = $user && ($user->hasRole('trainer') || $user->hasRole('administrator'));

        return view('tournaments.list', compact('tournaments', 'hasActiveMembership', 'canManage'));
    }

    public function join(Championship $championship)
    {
        $user = auth()->user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Check membership
        if (!$user->hasActiveMembership()) {
            return redirect()->back()->withErrors(['error' => __('You must be a member to join tournaments')]);
        }

        // Check if already registered
        if ($championship->competitors()->where('user_id', $user->id)->exists()) {
            return redirect()->back()->with('info', __('You are already registered for this tournament'));
        }

        try {
            // Find and reuse placeholder
            $placeholder = $championship->competitors()
                ->whereHas('user', function($query) {
                    $query->where('email', 'LIKE', 'placeholder_%@example.com');
                })
                ->first();

            if ($placeholder) {
                // Replace the placeholder with the real user
                $placeholder->update([
                    'user_id' => $user->id,
                    'confirmed' => 1,
                ]);

                return redirect()->back()->with('success', __('Successfully joined tournament'));
            } else {
                // No placeholders available, check if we can add a new competitor
                $maxCompetitors = $championship->settings->limitByEntity ?? 0;
                $currentCompetitors = $championship->competitors()->whereDoesntHave('user', function($query) {
                    $query->where('email', 'LIKE', 'placeholder_%@example.com');
                })->count();

                if ($maxCompetitors > 0 && $currentCompetitors >= $maxCompetitors) {
                    return redirect()->back()->withErrors(['error' => __('Tournament is full')]);
                }

                // Add as new competitor
                $nextShortId = $championship->competitors()->max('short_id') + 1;
                Competitor::create([
                    'championship_id' => $championship->id,
                    'user_id' => $user->id,
                    'short_id' => $nextShortId,
                    'confirmed' => 1,
                ]);

                return redirect()->back()->with('success', __('Successfully joined tournament'));
            }

        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => __('Error joining tournament') . ': ' . $e->getMessage()]);
        }
    }

    /**
     * Leave a tournament.
     */
    public function leave(Championship $championship)
    {
        $user = auth()->user();

        if (!$user) {
            return redirect()->route('login');
        }

        try {
            // Find the user's competitor entry
            $competitor = $championship->competitors()->where('user_id', $user->id)->first();

            if ($competitor) {
                // Use generic placeholder format for maximum reusability across all tournaments
                $placeholderEmail = "placeholder_{$competitor->short_id}@example.com";

                // Check if placeholder user already exists
                $placeholderUser = User::where('email', $placeholderEmail)->first();

                if (!$placeholderUser) {
                    $placeholderUser = User::create([
                        'first_name' => __('Available Spot'),
                        'last_name' => '', // Empty last name
                        'email' => $placeholderEmail,
                        'username' => 'placeholder_' . $competitor->short_id,
                        'password' => bcrypt(Str::random(32)),
                    ]);
                }

                // Replace with placeholder user
                $competitor->update([
                    'user_id' => $placeholderUser->id,
                    'confirmed' => 0,
                ]);

                \Log::info("User {$user->id} left championship {$championship->id}, replaced with placeholder {$placeholderUser->id}");
                return redirect()->back()->with('success', __('Successfully left tournament'));
            } else {
                return redirect()->back()->withErrors(['error' => __('You are not registered for this tournament')]);
            }

        } catch (\Exception $e) {
            \Log::error('Error leaving tournament', [
                'user_id' => $user->id,
                'championship_id' => $championship->id,
                'error' => $e->getMessage()
            ]);
            return redirect()->back()->withErrors(['error' => __('Error leaving tournament') . ': ' . $e->getMessage()]);
        }
    }

    /**
     * Build Tree.
     */
    public function store(Request $request)
    {
        // Check if user can manage tournaments
        if (!auth()->user() || (!auth()->user()->hasRole('trainer') && !auth()->user()->hasRole('administrator'))) {
            return redirect()->back()->withErrors(['error' => __('Only trainers and administrators can manage tournaments')]);
        }

        $request->validate([
            'numFighters' => 'required|integer|min:2|max:128',
            'tree_type' => 'required|integer|in:1,2,3',
            'isTeam' => 'sometimes|boolean',
            'tournament_id' => 'nullable|exists:tournament,id',
            'tournament_name' => 'nullable|string|max:255',
            'dateIni_date' => 'required_without:tournament_id|date',
            'dateIni_time' => 'required_without:tournament_id|date_format:H:i',
            'dateFin_date' => 'required_without:tournament_id|date',
            'dateFin_time' => 'required_without:tournament_id|date_format:H:i',
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

            \Log::info("Before tree generation - Championship: {$championship->id}, Competitors: " . $championship->competitors()->count());

            // DOUBLE CHECK: Ensure we have the right number of competitors
            $currentCompetitors = $championship->competitors()->count();
            if ($currentCompetitors != $numFighters) {
                \Log::warning("Competitor count mismatch: expected {$numFighters}, found {$currentCompetitors}. Recreating competitors.");
                $this->createCompetitors($championship, $numFighters);
            }

            // Verify competitors have championship_id
            $invalidCompetitors = $championship->competitors()->whereNull('championship_id')->count();
            if ($invalidCompetitors > 0) {
                \Log::error("Found {$invalidCompetitors} competitors without championship_id. Deleting and recreating.");
                $championship->competitors()->whereNull('championship_id')->delete();
                $this->createCompetitors($championship, $numFighters);
            }

            // Final verification before tree generation
            $finalCompetitorCount = $championship->competitors()->count();
            \Log::info("Final competitor count before tree generation: {$finalCompetitorCount}");

            if ($finalCompetitorCount < 2) {
                throw new \Exception(__('Need at least 2 competitors to generate tournament tree'));
            }

            $generation = $championship->chooseGenerationStrategy();
            $generation->run();

            \Log::info('Tournament tree generated successfully', [
                'championship_id' => $championship->id,
                'num_fighters' => $numFighters,
                'is_team' => $isTeam,
                'tree_type' => $request->tree_type,
                'competitors_count' => $championship->competitors()->count()
            ]);

            return back()->with('success', __('Tournament tree generated successfully!'));

        } catch (TreeGenerationException $e) {
            \Log::error('Tree generation failed', ['error' => $e->getMessage()]);
            return redirect()->back()->withErrors([__('Tree generation failed') . ': ' . $e->getMessage()]);
        } catch (\Exception $e) {
            \Log::error('Unexpected error during tree generation', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->withErrors([__('Unexpected error') . ': ' . $e->getMessage()]);
        }
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

    /**
     * Create a new tournament with championship
     */
    protected function createNewTournament(Request $request, $isTeam, $numFighters)
    {
        // Clean existing data first
        $this->cleanChampionshipData();

        // Create tournament name - use custom name or generate one
        $tournamentName = $request->tournament_name;
        if (!$tournamentName) {
            $tournamentType = $isTeam ? __('Team') : __('Individual');
            $month = now()->translatedFormat('F');
            $day = now()->format('j');
            $year = now()->format('Y');
            $time = now()->format('H:i');
            $tournamentName = "{$tournamentType} - {$day}. {$month} {$year} {$time}";
        }

        // Create date objects from form inputs - FIX TIMEZONE ISSUE
        $dateIni = \Carbon\Carbon::createFromFormat('Y-m-d H:i', $request->dateIni_date . ' ' . $request->dateIni_time, config('app.timezone'));
        $dateFin = \Carbon\Carbon::createFromFormat('Y-m-d H:i', $request->dateFin_date . ' ' . $request->dateFin_time, config('app.timezone'));

        // Generate unique slug by adding timestamp to prevent duplicates
        $baseSlug = Str::slug($tournamentName);
        $slug = $baseSlug . '-' . time();

        // Create tournament
        $tournament = Tournament::create([
            'name' => $tournamentName,
            'slug' => $slug,
            'dateIni' => $dateIni,
            'dateFin' => $dateFin,
            'registerDateLimit' => $dateIni,
            'level_id' => 1,
            'type' => 1,
            'venue_id' => $this->getOrCreateVenue(),
            'user_id' => $this->getOrCreateUser(),
        ]);

        // CREATE EVENT FOR CALENDAR - One event per tournament
        try {
            Event::create([
                'title' => $tournamentName,
                'description' => 'Tournament',
                'start_date' => $dateIni,
                'end_date' => $dateFin,
            ]);
            \Log::info("Calendar event created for tournament: {$tournamentName}");
        } catch (\Exception $e) {
            \Log::error('Failed to create calendar event', [
                'tournament_name' => $tournamentName,
                'error' => $e->getMessage()
            ]);
            // Don't throw error - tournament creation should continue even if event creation fails
        }

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
            ['name' => $isTeam ? __('Team Category') : __('Individual Category')]
        );

        // Create championship with unique name
        $championshipType = $isTeam ? __('Team') : __('Individual');
        $time = now()->format('H:i:s');
        $championshipName = "{$championshipType} - {$time}";

        $championship = Championship::create([
            'tournament_id' => $tournament->id,
            'category_id' => $category->id,
            'name' => $championshipName
        ]);

        \Log::info("Created championship: {$championship->id}");

        // DEBUG: Test competitor creation
        $this->debugCompetitorCreation($championship);

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

        \Log::info("Championship settings created");

        return $championship->fresh(['settings', 'category', 'fightersGroups.fights', 'competitors', 'teams']);
    }

    /**
     * Temporary debug method to check competitor creation
     */
    protected function debugCompetitorCreation(Championship $championship)
    {
        \Log::info("=== DEBUG COMPETITOR CREATION ===");
        \Log::info("Championship ID: " . $championship->id);
        \Log::info("Championship exists: " . ($championship->exists ? 'YES' : 'NO'));

        // Test creating one competitor
        $testData = [
            'championship_id' => $championship->id,
            'user_id' => null,
            'short_id' => 999,
            'confirmed' => 0,
        ];

        \Log::info("Test data:", $testData);

        try {
            $testCompetitor = Competitor::create($testData);
            \Log::info("SUCCESS: Test competitor created with ID: " . $testCompetitor->id);
            // Check what fields were actually saved
            \Log::info("Saved competitor data:", $testCompetitor->toArray());
            $testCompetitor->delete(); // Clean up
        } catch (\Exception $e) {
            \Log::error("FAILED: " . $e->getMessage());
            \Log::error("Trace: " . $e->getTraceAsString());
        }

        \Log::info("=== END DEBUG ===");
    }

    /**
     * Create placeholder competitors for the championship that users can replace
     */
    protected function createCompetitors(Championship $championship, $numFighters)
    {
        $competitors = [];
        $now = now();

        // Create/reuse placeholder users - no limit, reuse across all tournaments
        for ($i = 1; $i <= $numFighters; $i++) {
            $placeholderEmail = "placeholder_{$i}@example.com";

            $placeholderUser = User::where('email', $placeholderEmail)->first();
            if (!$placeholderUser) {
                $placeholderUser = User::create([
                    'first_name' => __('Available Spot'),
                    'last_name' => '', // Empty last name
                    'email' => $placeholderEmail,
                    'username' => 'placeholder_' . $i,
                    'password' => bcrypt(Str::random(32)),
                ]);
            }

            $competitors[] = [
                'championship_id' => $championship->id,
                'user_id' => $placeholderUser->id,
                'short_id' => $i,
                'confirmed' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        DB::table('competitor')->insert($competitors);
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
                'name' => __('Team') . ' ' . $i,
                'short_id' => $i
            ]);
        }
    }

    /**
     * Add to existing tournament
     */
    protected function addToExistingTournament(Tournament $tournament, Request $request, $isTeam, $numFighters)
    {
        // Only create event if this is the first championship being added to the tournament
        $existingChampionshipsCount = $tournament->championships()->count();

        if ($existingChampionshipsCount == 0) {
            // CREATE EVENT FOR CALENDAR - Only if no championships exist yet
            try {
                // Create date objects from form inputs - FIX TIMEZONE ISSUE
                $dateIni = \Carbon\Carbon::createFromFormat('Y-m-d H:i', $request->dateIni_date . ' ' . $request->dateIni_time, config('app.timezone'));
                $dateFin = \Carbon\Carbon::createFromFormat('Y-m-d H:i', $request->dateFin_date . ' ' . $request->dateFin_time, config('app.timezone'));

                Event::create([
                    'title' => $tournament->name,
                    'description' => 'Tournament',
                    'start_date' => $dateIni,
                    'end_date' => $dateFin,
                ]);
                \Log::info("Calendar event created for existing tournament: {$tournament->name}");
            } catch (\Exception $e) {
                \Log::error('Failed to create calendar event for existing tournament', [
                    'tournament_id' => $tournament->id,
                    'error' => $e->getMessage()
                ]);
                // Don't throw error - championship creation should continue
            }
        }

        return $this->createChampionship($tournament, $request, $isTeam, $numFighters);
    }

    /**
     * Update fight results
     */
    public function update(Request $request, Championship $championship)
    {
        // Check if user can manage tournaments
        if (!auth()->user() || (!auth()->user()->hasRole('trainer') && !auth()->user()->hasRole('administrator'))) {
            return redirect()->back()->withErrors(['error' => __('Only trainers and administrators can manage tournaments')]);
        }

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

            return back()->with('success', __('Fight results updated successfully!'));

        } catch (\Exception $e) {
            \Log::error('Error updating fight results', [
                'championship_id' => $championship->id,
                'error' => $e->getMessage()
            ]);

            return back()->withErrors([__('Error updating fights') . ': ' . $e->getMessage()]);
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
        // Check if user can manage tournaments
        if (!auth()->user() || (!auth()->user()->hasRole('trainer') && !auth()->user()->hasRole('administrator'))) {
            return redirect()->back()->withErrors(['error' => __('Only trainers and administrators can manage tournaments')]);
        }

        try {
            $tournamentName = $tournament->name;

            // DELETE ASSOCIATED CALENDAR EVENT FIRST
            $this->deleteTournamentEvent($tournament);

            $this->cleanTournamentData($tournament->id);
            $tournament->delete();

            return back()->with('success', __('Tournament deleted successfully!'));
        } catch (\Exception $e) {
            \Log::error('Error deleting tournament', ['error' => $e->getMessage()]);
            return back()->withErrors([__('Error deleting tournament') . ': ' . $e->getMessage()]);
        }
    }

    /**
     * Delete a championship
     */
    public function destroyChampionship(Championship $championship)
    {
        // Check if user can manage tournaments
        if (!auth()->user() || (!auth()->user()->hasRole('trainer') && !auth()->user()->hasRole('administrator'))) {
            return redirect()->back()->withErrors(['error' => __('Only trainers and administrators can manage tournaments')]);
        }

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
                // If this was the last championship, delete the tournament and its event
                $tournament = Tournament::find($tournamentId);
                if ($tournament) {
                    $this->deleteTournamentEvent($tournament);
                    $tournament->delete();
                }
            }

            return back()->with('success', __('Championship deleted successfully!'));
        } catch (\Exception $e) {
            \Log::error('Error deleting championship', ['error' => $e->getMessage()]);
            return back()->withErrors([__('Error deleting championship') . ': ' . $e->getMessage()]);
        }
    }

    /**
     * Delete calendar event associated with a tournament
     */
    private function deleteTournamentEvent(Tournament $tournament)
    {
        try {
            // Convert dates to Carbon instances if they are strings
            $dateIni = $tournament->dateIni instanceof \Carbon\Carbon
                ? $tournament->dateIni
                : \Carbon\Carbon::parse($tournament->dateIni);

            $dateFin = $tournament->dateFin instanceof \Carbon\Carbon
                ? $tournament->dateFin
                : \Carbon\Carbon::parse($tournament->dateFin);

            $deleted = Event::where('title', $tournament->name)
                        ->where('start_date', $dateIni)
                        ->where('end_date', $dateFin)
                        ->delete();

            \Log::info("Deleted {$deleted} calendar events for tournament: {$tournament->name}");

            if ($deleted === 0) {
                \Log::warning("No calendar events found to delete for tournament: {$tournament->name}");

                // Try a broader search if exact match fails - use the parsed dates
                $alternativeDeleted = Event::where('title', $tournament->name)
                                        ->whereDate('start_date', $dateIni->format('Y-m-d'))
                                        ->delete();

                \Log::info("Alternative deletion result: {$alternativeDeleted} events deleted");
            }

        } catch (\Exception $e) {
            \Log::error('Error deleting calendar event for tournament', [
                'tournament_id' => $tournament->id,
                'tournament_name' => $tournament->name,
                'dateIni_type' => gettype($tournament->dateIni),
                'dateIni_value' => $tournament->dateIni,
                'dateFin_type' => gettype($tournament->dateFin),
                'dateFin_value' => $tournament->dateFin,
                'error' => $e->getMessage()
            ]);
        }
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
}
