<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Xoco70\LaravelTournaments\Models\Tournament;
use Xoco70\LaravelTournaments\Models\Championship;

class TournamentController extends Controller
{
    /**
     * Display tournaments listing
     */
    public function index()
    {
        $tournaments = Tournament::with(['championships', 'venue'])
            ->orderBy('dateIni', 'desc')
            ->paginate(10);

        return view('tournaments.index', compact('tournaments'));
    }

    /**
     * Show individual tournament
     */
    public function show($id)
    {
        $tournament = Tournament::with([
            'championships.settings',
            'championships.fightersGroups.fights',
            'championships.competitors.user',
            'venue'
        ])->findOrFail($id);

        return view('tournaments.show', compact('tournament'));
    }

    /**
     * Show championship brackets
     */
    public function showChampionship($tournamentId, $championshipId)
    {
        $championship = Championship::with([
            'fightersGroups.fights.competitor1.user',
            'fightersGroups.fights.competitor2.user',
            'fightersGroups.fights.group',
            'competitors.user',
            'settings'
        ])->findOrFail($championshipId);

        $tournament = $championship->tournament;

        return view('tournaments.package-championship', compact('championship', 'tournament'));
    }

    /**
     * Create demo tournament
     */
    public function createDemo()
    {
        // We'll use the seeder logic here later
        return redirect()->route('tournaments.index')
            ->with('info', 'Use the seeder to create demo tournaments for now.');
    }
    /**
     * Update tournament tree (required by package views)
     */
    public function updateTree(Request $request, $championshipId)
    {
        // For now, just redirect back - we'll implement tree updates later
        return redirect()->back()->with('info', 'Tree update functionality coming soon!');
    }
}
