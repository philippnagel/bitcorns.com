<?php

namespace App\Http\Controllers;

use Auth;
use App\Farm;
use App\Token;
use App\MapMarker;
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
        $sort = $request->has('q') ? 'search' : $request->input('sort', 'access');

        // List Farms
        $farms = Farm::getSortedFarms($request, $sort)->paginate(45);

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
        $balances = $farm->tokenBalances()->tokens()->get();

        // Tokens: Upgrades
        $upgrades = $farm->tokenBalances()->upgrades()->nonZero()->get();

        // Tokens: Upgrades Total
        $upgrades_total = Token::whereType('upgrade')->count();

        // Tokens: % of Progress
        $progress = round($upgrades->count() / $upgrades_total * 100);

        // Return View
        return view('farms.show', compact('farm', 'balances', 'upgrades', 'progress'));
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
        /*
         * Gate / Policy Needed
         */

        // Authentication
        if(Auth::guard('farm')->check() && Auth::guard('farm')->user()->slug === $farm->slug)
        {
            // Logged In
        }
        else
        {
            if($error = $farm->validateSignature($request))
            {
                return back()->with('error', $error);
            }
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