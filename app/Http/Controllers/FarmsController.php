<?php

namespace App\Http\Controllers;

use App\Farm;
use App\Token;
use App\MapMarker;
use Auth, Cache;
use App\Http\Requests\Farms\IndexRequest;
use App\Http\Requests\Farms\UpdateRequest;
use Illuminate\Http\Request;

class FarmsController extends Controller
{
    /**
     * List Farms (Sortable)
     *
     * @param  \App\Http\Requests\Farms\IndexRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function index(IndexRequest $request)
    {
        // Sort Order
        $sort = $request->has('q') ? 'search' : $request->input('sort', 'crops');

        // List Farms
        $farms = Farm::getSortedFarms($request, $sort)->with('firstCrops')
            ->withCount('harvests')
            ->paginate(45);

        // Index View
        return view('farms.index', compact('farms', 'sort'));
    }

    /**
     * Show Farm
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Farm  $farm
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, Farm $farm)
    {
        // Tokens: Access and Rewards
        $tokens = $farm->tokenBalances()->get();

        // Tokens: Upgrades
        $upgrades = $farm->upgradeBalances()->get();

        // Tokens: Upgrades Total
        $upgrades_total = Cache::remember('upgrades_total', 60, function () {
            return Token::published()->upgrades()->count();
        });

        // Tokens: % of Progress
        $progress = round($upgrades->count() / $upgrades_total * 100);

        // Unlocked Achievements
        $unlocked_achievements = $farm->achievements()
            ->with('details')
            ->whereNotNull('unlocked_at')
            ->oldest('unlocked_at')
            ->get();

        // Locked Achievements
        $locked_achievements = $farm->achievements()
            ->with('details')
            ->whereNull('unlocked_at')
            ->oldest('unlocked_at')
            ->get()
            ->sortByDesc(function ($achievement) {
                return $achievement->points / $achievement->details->points;
            });

        // Bitcorn Battle API
        $battle = $farm->getBattleStats();

        // Return View
        return view('farms.show', compact('farm', 'battle', 'tokens', 'upgrades', 'progress', 'unlocked_achievements', 'locked_achievements'));
    }

    /**
     * Edit Farm
     *
     * @param  \App\Farm  $farm
     * @return \Illuminate\Http\Response
     */
    public function edit(Farm $farm)
    {
        return view('farms.edit', compact('farm'));
    }

    /**
     * Update Player
     *
     * @param  \App\Http\Requests\Farms\UpdateRequest  $request
     * @param  \App\Farm  $farm
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRequest $request, Farm $farm)
    {
        // Authentication
        if($error = $farm->validateSignature($request))
        {
            return back()->with('error', $error);
        }

        // Validation
        if($error = MapMarker::validateCoordinates($request, $farm))
        {
            return back()->with('error', $error);
        }

        // Execution
        $farm->update($request->all());

        // Return Back
        return back()->with('success', 'Update Complete');
    }
}