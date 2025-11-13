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
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $tournament = Tournament::with([
            'championships.settings',
            'championships.category',
            'championships.competitors.user',
            'championships.teams'
        ])->first();

        return view('tournaments.index')
            ->with('tournament', $tournament);
    }

    /**
     * Build Tree.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $this->deleteEverything();
        $numFighters = $request->numFighters;
        $isTeam = $request->isTeam ?? 0;

        $championship = $this->provisionObjects($request, $isTeam, $numFighters);
        $generation = $championship->chooseGenerationStrategy();

        try {
            $generation->run();
        } catch (TreeGenerationException $e) {
            return redirect()->back()
                ->withErrors([$e->getMessage()]);
        }

        return back()
            ->with('success', 'Tournament tree generated successfully!')
            ->with('numFighters', $numFighters)
            ->with('isTeam', $isTeam);
    }

    private function deleteEverything()
    {
        DB::table('fight')->delete();
        DB::table('fighters_groups')->delete();
        DB::table('fighters_group_competitor')->delete();
        DB::table('fighters_group_team')->delete();
        DB::table('competitor')->delete();
        DB::table('team')->delete();
    }

    /**
     * @param Request $request
     * @param $isTeam
     * @param $numFighters
     *
     * @return Championship
     */
    protected function provisionObjects(Request $request, $isTeam, $numFighters)
{
    // First, create a tournament if none exists
    $tournament = Tournament::first();
    if (!$tournament) {
        $tournament = Tournament::create([
            'name' => 'Demo Tournament',
            'slug' => Str::slug('Demo Tournament'),
            'dateIni' => now(),
            'dateFin' => now()->addDays(3),
            'level_id' => 1,
            'type' => 1,
            'venue_id' => Venue::first()->id,
            'user_id' => User::first()->id,
        ]);
    }

    // Get the appropriate category
    $category = $isTeam
        ? Category::where('isTeam', 1)->first()
        : Category::where('isTeam', 0)->first();

    // Create or get championship
    $championship = Championship::firstOrCreate([
        'tournament_id' => $tournament->id,
        'category_id' => $category->id,
    ], [
        'name' => ($isTeam ? 'Team' : 'Individual') . ' Championship'
    ]);

    // DEBUG: Check if championship was created successfully
    \Log::info("Championship ID: " . $championship->id);
    \Log::info("Is Team: " . $isTeam);

    if ($isTeam) {
        // Create teams
        for ($i = 1; $i <= $numFighters; $i++) {
            Team::create([
                'championship_id' => $championship->id,
                'name' => 'Team ' . $i,
                'short_id' => $i  // Changed from 'T'.$i to just $i
            ]);
        }
    } else {
        // Get users who are not already competitors in this championship
        $existingCompetitorUserIds = Competitor::where('championship_id', $championship->id)
            ->pluck('user_id')
            ->toArray();

        $availableUsers = User::whereNotIn('id', $existingCompetitorUserIds)
            ->limit($numFighters)
            ->get();

        $usersNeeded = $numFighters - $availableUsers->count();

        // Create new users if we don't have enough
        if ($usersNeeded > 0) {
            $newUsers = User::factory($usersNeeded)->create();
            $availableUsers = $availableUsers->merge($newUsers);
        }

        // DEBUG: Log before creating competitors
        \Log::info("Creating " . $availableUsers->count() . " competitors for championship ID: " . $championship->id);

        // Create competitors - use the correct approach
        foreach ($availableUsers as $index => $user) {
            $competitor = new Competitor();
            $competitor->championship_id = $championship->id; // Correct database column
            $competitor->user_id = $user->id;
            $competitor->confirmed = 1;
            $competitor->short_id = $index + 1;
            $competitor->save();

            // DEBUG: Log success
            \Log::info("Created competitor ID: " . $competitor->id . " for championship ID: " . $championship->id);
        }
    }

    // Update championship settings
    $championship->settings()->updateOrCreate(
        ['championship_id' => $championship->id],
        [
            'treeType' => $request->tree_type ?? 1,
            'hasPreliminary' => $request->hasPreliminary ?? 0,
            'preliminaryGroupSize' => $request->preliminary_group_size ?? 3,
            'fightingAreas' => $request->fighting_areas ?? 1,
            'limitByEntity' => $request->limit_by_field ?? 0,
            'teamSize' => $request->team_size ?? 1,
        ]
    );

    return $championship->fresh(['settings', 'category']);
}
    /**
     * @param Request $request
     * @param Championship $championship
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Championship $championship)
    {
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
    }

    private function getWinnerId($fighters, $scores, $numFighter)
    {
        return $scores[$numFighter] != null ? $fighters[$numFighter] : null;
    }
}
