<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Xoco70\LaravelTournaments\Models\Tournament;
use Xoco70\LaravelTournaments\Models\Competitor;

class SimpleTournamentController extends Controller
{
    public function index()
    {
        $tournaments = Tournament::with(['competitors.user'])->get();
        return view('tournaments.simple-generator', compact('tournaments'));
    }

    public function generate(Request $request)
    {
        try {
            // Clear old tournament data
            DB::table('fight')->delete();
            DB::table('fighters_groups')->delete();
            DB::table('fighters_group_competitor')->delete();
            DB::table('competitor')->delete();
            DB::table('users')->where('id', '<>', 1)->delete();

            // Create or get tournament
            $tournament = Tournament::first();
            if (!$tournament) {
                $tournament = Tournament::create([
                    'name' => 'Demo Tournament',
                    'slug' => 'demo-tournament',
                    'dateIni' => now()->format('Y-m-d'),
                    'dateFin' => now()->addDays(1)->format('Y-m-d'),
                    'user_id' => 1,
                    'sport' => 1,
                    'rule_id' => 1,
                    'type' => 1,
                    'level_id' => 1,
                ]);
            }

            // Create competitors directly for the tournament
            $numFighters = 8;
            for ($i = 1; $i <= $numFighters; $i++) {
                $user = User::create([
                    'name' => 'Player ' . $i,
                    'email' => 'player' . $i . '@test.com',
                    'password' => bcrypt('password'),
                ]);

                // Create competitor linked to tournament (not championship)
                Competitor::create([
                    'tournament_id' => $tournament->id, // Use tournament_id instead of championship_id
                    'user_id' => $user->id,
                    'short_id' => $i,
                    'confirmed' => 1,
                ]);
            }

            // Generate the tournament structure
            // The package should handle this automatically when competitors are added to tournament

            return redirect()->route('simple.tournament.show', $tournament->id)
                ->with('success', 'Tournament generated successfully!');

        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $tournament = Tournament::with([
            'competitors.user',
            'fightersGroups.fights.competitor1.user',
            'fightersGroups.fights.competitor2.user'
        ])->findOrFail($id);

        return view('tournaments.simple-show', compact('tournament'));
    }
}
