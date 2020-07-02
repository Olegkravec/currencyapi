<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\StoreSubscriptionAPIRequest;
use App\Http\Requests\StoreSubscriptionRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Cashier\Subscription;

class SubscriptionsResourceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        $subscriptions = $user->subscriptions()->active()->get();

        return response([
            "elements" => count($subscriptions),
            "subscriptions" => $subscriptions,
        ], 200);
    }




    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }


    /**
     * Create new subscription
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     * StoreSubscriptionRequest
     */
    public function store(StoreSubscriptionAPIRequest $request)
    {
        $user = Auth::guard("api")->user();

        $paymentMethod = $user->defaultPaymentMethod();
        if(empty($paymentMethod)){
            return response([
                "status" => "error",
                "message" => "Should add payment method first",
            ], 403);
        }

        if ($user->subscribed('default')) {
            return response([
                "status" => "error",
                "message" => "Already subscribed",
            ], 403);
        }

        $result = $user->newSubscription('default', $request->validated()['plan'])->create($paymentMethod->id);
        return response([
            "status" => "success",
            "message" => $result,
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $user = Auth::guard("api")->user();
        if(empty($user)){
            return response([
                "status" => "error",
                "message" => "User not found",
            ], 404);
        }


        return response([
            "payment_link" => "/payments/create",
            "status" => "Ok",
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }


    /**
     * Returns list of active plans from stripe service
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     * @throws \Stripe\Exception\ApiErrorException
     */
    public function getPlans(){
        $user = Auth::guard("api")->user();
        \Stripe\Stripe::setApiKey(env("STRIPE_SECRET"));
        $plans = \Stripe\Plan::all(['active'=>true]);

        return response([
            "plans" => $plans,
            "status" => "Ok",
        ], 202);
    }
}
