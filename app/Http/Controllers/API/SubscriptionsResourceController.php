<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\StoreSubscriptionAPIRequest;
use App\Http\Requests\StoreSubscriptionRequest;
use App\Models\Responses\BaseResponseModel;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Laravel\Cashier\Subscription;
use Stripe\Plan;
use Stripe\Stripe;

class SubscriptionsResourceController extends Controller
{
    /**
     * @summary
     * Display a listing of the users subscriptions.
     *
     * @header Authorization|required|JWT authorization token
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
     * @summary
     * Create new subscription
     *
     * @plan Stripe Plan ID that will be subscribed
     * @body should contain json object with "plan" filed that should contain Stripe Plan ID
     * @header Authorization|required|JWT authorization token
     * @param StoreSubscriptionAPIRequest $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response|\Symfony\Component\HttpFoundation\Response
     * @throws \Stripe\Exception\ApiErrorException
     */
    public function store(StoreSubscriptionAPIRequest $request)
    {
        $user = Auth::guard("api")->user();

        $paymentMethod = $user->defaultPaymentMethod();
        if(empty($paymentMethod)){
            return response([
                "status" => "error",
                "message" => "Should add payment method first",
            ], Response::HTTP_FORBIDDEN);
        }
        Stripe::setApiKey(env("STRIPE_SECRET"));
        $plans = Plan::retrieve($request->validated()['plan']);
        $subscription_id = $plans->nickname;



        if ($user->subscribed($subscription_id)) {
            return response([
                "status" => "error",
                "message" => "Already subscribed",
            ], 403);
        }

        $result = $user->newSubscription($subscription_id, $request->validated()['plan'])->create($paymentMethod->id);
        return response([
            "status" => "success",
            "message" => $result,
        ], 200);
    }

    /**
     * @summary
     * Show the link to form for creating a new payment method.
     *
     * @header Authorization|required|JWT authorization token
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $user = Auth::guard("api")->user();
        if(empty($user)){
            return response(new BaseResponseModel("error" , null, "User not found!"), 404);
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
     * @summary
     * Remove the specified subscription from storage.
     *
     * @subscription subscription name that will be deleted(basic/premium)
     *
     * @header Authorization|required|JWT authorization token
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = Auth::guard("api")->user();

        if (!$user->subscribed($id)) {
            return response([
                "status" => "error",
                "message" => "Subscription not found",
            ], 404);
        }

        $response = $user->subscription($id)->cancelNow();
        if($response->stripe_status === "canceled"){
            return response([
                "status" => "success",
                "message" => $response,
            ], 200);
        }

        return response(new BaseResponseModel("error" , null, "Subscription canceled!"), 200);
    }


    /**
     * @summary
     * Returns list of active plans from stripe service
     *
     * @header Authorization|required|JWT authorization token
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     * @throws \Stripe\Exception\ApiErrorException
     */
    public function getPlans(){
        $user = Auth::guard("api")->user();
        Stripe::setApiKey(env("STRIPE_SECRET"));
        $plans = Plan::all(['active'=>true]);

        return response([
            "plans" => $plans,
            "status" => "Ok",
        ], Response::HTTP_OK);
    }
}
