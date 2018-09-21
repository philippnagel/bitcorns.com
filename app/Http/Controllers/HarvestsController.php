<?php

namespace App\Http\Controllers;

use App\Harvest;
use Illuminate\Http\Request;

class HarvestsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Harvest Schedule
        $harvests = Harvest::oldest('scheduled_at')->get();

        // Index View
        return view('harvests.index', compact('harvests'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Harvest  $harvest
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, Harvest $harvest)
    {
        // Show View
        return view('harvests.show', compact('harvest'));
    }
}