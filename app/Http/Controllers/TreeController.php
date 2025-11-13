<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Xoco70\LaravelTournaments\Exceptions\TreeGenerationException;
use Xoco70\LaravelTournaments\Models\Championship;
use Xoco70\LaravelTournaments\Models\ChampionshipSettings;
use Xoco70\LaravelTournaments\Models\Competitor;
use Xoco70\LaravelTournaments\Models\FightersGroup;
use Xoco70\LaravelTournaments\Models\Team;
use Xoco70\LaravelTournaments\Models\Tournament;

class TreeController extends Controller
{
    /**
     * Display a listing of trees.
     */
    public function index()
    {
        $tournament = Tournament::with([
            'championships.settings',
            'championships.category',
            'championships.competitors'
        ])->first();

        return view('tournaments.tree-index', compact('tournament'));
    }

    /**
     * Build Tree.
     */
    public function store(Request $request, $championshipId)
    {
        // Clear existing data but keep the structure
        DB::table('fight')->delete();
        DB::table('fighters_groups')->delete();
        DB::table('fighters_group_competitor')->delete();
        DB::table('fighters_group_team')->delete();
        DB::table('competitor')->delete();
        DB::table('team')->delete();
        DB::table('users')->where('id', '<>', 1)->delete();

        $numFighters = (int) $request->numFighters;
        $isTeam = (int) $request->isTeam;

        // USE THE $championshipId FROM THE ROUTE!
        $championship = $this->provisionObjects($request, $isTeam, $numFighters, $championshipId);
        $generation = $championship->chooseGenerationStrategy();

    }

    private function deleteEverything()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('fight')->truncate();
        DB::table('fighters_groups')->truncate();
        DB::table('fighters_group_competitor')->truncate();
        DB::table('fighters_group_team')->truncate();
        DB::table('competitor')->truncate();
        DB::table('team')->truncate();
        DB::table('users')->where('id', '<>', 1)->delete();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    /**
     * Create tournament objects
     */
    protected function provisionObjects(Request $request, $isTeam, $numFighters, $championshipId)
    {
        // Use the championshipId from the route parameter!
        $championship = Championship::find($championshipId);

        if (!$championship) {
            throw new \Exception("Championship with ID $championshipId not found!");
        }

        if ($isTeam) {
            // Create teams
            for ($i = 1; $i <= $numFighters; $i++) {
                Team::create([
                    'championship_id' => $championship->id,
                    'name' => 'Team ' . $i,
                    'short_id' => $i,
                ]);
            }
        } else {
            // Create competitors
            for ($i = 1; $i <= $numFighters; $i++) {
                $user = User::create([
                    'name' => 'Competitor ' . $i,
                    'email' => 'competitor' . $i . '@demo.test',
                    'password' => bcrypt('password'),
                ]);

                Competitor::create([
                    'championship_id' => $championship->id, // Use existing championship
                    'user_id' => $user->id,
                    'short_id' => $i,
                    'confirmed' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // Create or update settings for the existing championship
        $settingsData = [
            'championship_id' => $championship->id,
            'hasPreliminary' => $request->hasPreliminary ?? 0,
            'preliminaryGroupSize' => $request->preliminaryGroupSize ?? 3,
            'preliminaryWinner' => 1,
            'fightingAreas' => $request->fightingAreas ?? 1,
            'treeType' => 1,
            'fightDuration' => '05:00',
            'created_at' => now(),
            'updated_at' => now(),
        ];

        ChampionshipSettings::updateOrCreate(
            ['championship_id' => $championship->id],
            $settingsData
        );

        return $championship->fresh(['settings', 'competitors']);
    }

    /**
     * Update tree results
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
                        $fight->save();
                    }
                }
            }

            return back()->with('success', 'Tournament results updated!');
        } catch (\Exception $e) {
            return back()->with('error', 'Error updating results: ' . $e->getMessage());
        }
    }

    private function getWinnerId($fighters, $scores, $numFighter)
    {
        return isset($scores[$numFighter]) && $scores[$numFighter] != null ? $fighters[$numFighter] : null;
    }

    /**
     * Show tree for specific championship
     */
    public function show($championshipId)
    {
        try {
            $championship = Championship::with([
                'tournament',
                'settings',
                'fightersGroups.fights.competitor1.user',
                'fightersGroups.fights.competitor2.user',
                'fightersGroups.fights.group',
                'competitors.user'
            ])->findOrFail($championshipId);

            return view('tournaments.tree-show', compact('championship'));
        } catch (\Exception $e) {
            return redirect()->route('tree.index')->with('error', 'Championship not found: ' . $e->getMessage());
        }
    }
}
