<?php

namespace App\Http\Controllers;

use Cache;
use App\Token;
use Droplister\XcpCore\App\Asset;
use Droplister\XcpCore\App\OrderMatch;
use App\Http\Requests\Cards\StoreRequest;
use Illuminate\Http\Request;

class CardsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Get Cards
        $cards = Token::published()
            ->upgrades()
            ->oldest()
            ->get();

        // Index View
        return view('cards.index', compact('cards'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Token  $token
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, Token $card)
    {
        // Token Redirect Guard
        if($card->type !== 'upgrade')
        {
            return redirect(route('tokens.show', ['token' => $card->slug]));
        }

        // Get Farm Balances
        $balances = $card->tokenBalances()->with('farm')
            ->orderBy('quantity', 'desc')
            ->paginate(20);

        // Show View
        return view('cards.show', compact('card', 'balances'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        // DEX Average
        $dex_average = $this->getAverageDexPrice();

        // Queue Count
        $queue_count = Token::publishable()->upgrades()->count();

        // Create View
        return view('cards.create', compact('dex_average', 'queue_count'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Cards\StoreRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRequest $request)
    {
        // Validation+
        if($error = $this->guardAgainstInvalidTokens($request->name) || $error = $this->guardAgainstInvalidBurns($request->burn))
        {
            return back()->withInput()->with('error', $error);
        }

        // Create Card
        $card = Token::createCard($request);

        // Report Back
        return redirect(route('cards.create'))->with('success', 'Success - Card Submitted!');
    }

    /**
     * Get Average DEX Price
     * 
     * @return string
     */
    private function getAverageDexPrice()
    {
        // DEX Average
        return Cache::remember('dex_average', 1440, function () {
            // Cards
            $card_assets = Token::published()->upgrades()
                ->pluck('xcp_core_asset_name')
                ->toArray();

            // Buys
            $buys = OrderMatch::whereIn('forward_asset', $card_assets)
                ->where('backward_asset', '=', config('bitcorn.reward_token'));
            $average_buy = $buys->sum('forward_quantity') === 0 ? 0 : $buys->sum('backward_quantity') / $buys->sum('forward_quantity');

            // Sells
            $sells = OrderMatch::whereIn('backward_asset', $card_assets)
                ->where('forward_asset', '=', config('bitcorn.reward_token'));
            $average_sell = $sells->sum('backward_asset') === 0 ? 0 : $sells->sum('forward_asset') / $sells->sum('backward_asset');

            // DEX Average
            return number_format(($average_buy + $average_sell) / 2);
        });
    }

    /**
     * Guard Against Invalid Tokens
     * 
     * @param  string  $asset_name
     * @return mixed
     */
    private function guardAgainstInvalidTokens($asset_name)
    {
        // Get Asset
        $asset = Asset::find($asset_name);

        // Check It!
        if(! $asset->divisible)
        {
            return 'Error - Bitcorn Cards cannot be divisible.';
        }
        elseif(! $asset->locked)
        {
            return 'Error - Asset issuance needs to be locked.';
        }

        // No Errors
        return false;
    }


    /**
     * Guard Against Invalid Burns
     * 
     * @param  string  $tx_hash
     * @return mixed
     */
    private function guardAgainstInvalidBurns($tx_hash)
    {
        // Get Send
        $send = Send::where('tx_hash', '=', $tx_hash)->first();

        // Check It!
        if($send->destination !== config('bitcorn.subfee_address'))
        {
            return 'Error - Burn TX invalid, wrong destination!';
        }

        // No Errors
        return false;
    }
}