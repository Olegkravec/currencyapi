<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSubscriptionRequest;
use App\Http\Requests\UpdateSubscriptionAdminRequest;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Cashier\Subscription;
use Stripe\Plan;
use Stripe\Stripe;

class SubscriptionsResourceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view("subscriptions.index")->with([
            "subscriptions" => Subscription::query()->orderBy("updated_at", "DESC")->get()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

    }

    /**
     * Show the form for creating a new resource.
     * @param $user_id
     * @return array|\Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     * @throws \Stripe\Exception\ApiErrorException
     */
    public function createAssigned($user_id)
    {
        $user = User::find($user_id);
        if(empty($user)){
            flash("User not found!")->error();
            return redirect()->back();
        }
        Stripe::setApiKey(env("STRIPE_SECRET"));
        $plans = Plan::all(['active'=>true]);
        return view("subscriptions.create")->with([
            'user' => $user,
            'plans' => $plans
        ]);
    }

    /**
     * Store a newly created resource in storage.
     * Checking of permission is present in request model
     *
     * @param StoreSubscriptionRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|void
     * @throws \Laravel\Cashier\Exceptions\PaymentActionRequired
     * @throws \Laravel\Cashier\Exceptions\PaymentFailure
     * @throws \Stripe\Exception\ApiErrorException
     */
    public function store(StoreSubscriptionRequest $request)
    {
        $user = User::find($request->validated()['user_id']);

        $paymentMethod = $user->defaultPaymentMethod();
        if(empty($paymentMethod)){
            flash("User doesnt has a default payment method")->error();
            return redirect()->back();
        }
        Stripe::setApiKey(env("STRIPE_SECRET"));
        $plan = Plan::retrieve($request->validated()['plan_id']);
        $user->newSubscription($plan->nickname, $plan->id)->create($paymentMethod->id);

        flash("Subscription was successfully created!")->success();
        return redirect('subscriptions');
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
     * Show the form for editing the specified resource.
     *
     * @param $id
     * @return array|\Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     * @throws \Stripe\Exception\ApiErrorException
     */
    public function edit($id)
    {
        $subscription = Subscription::find($id);
        if(empty($subscription)){
            flash("Subscription is not exist")->error();
            return redirect()->back();
        }
        Stripe::setApiKey(env("STRIPE_SECRET"));
        $plans = Plan::all(['active'=>true]);
        return view("subscriptions.edit")->with([
            "subscription" => $subscription,
            "plans" => $plans
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateSubscriptionAdminRequest $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Laravel\Cashier\Exceptions\PaymentActionRequired
     * @throws \Laravel\Cashier\Exceptions\PaymentFailure
     * @throws \Stripe\Exception\ApiErrorException
     */
    public function update(UpdateSubscriptionAdminRequest $request, $id)
    {
        $subscription = Subscription::find($id);
        $user = User::find($subscription->user_id);
        Stripe::setApiKey(env("STRIPE_SECRET"));
        if($subscription->stripe_plan !== $request->validated()['plan_id']){
            $user->subscription($subscription->name)->cancelNow();


            $paymentMethod = $user->defaultPaymentMethod();
            if(empty($paymentMethod)){
                flash("User doesnt has a default payment method")->error();
                return redirect()->back();
            }

            $plan = Plan::retrieve($request->validated()['plan_id']);
            $user->newSubscription($plan->nickname, $plan->id)->create($paymentMethod->id);
        }

        flash("Subscription was successfully saved!")->success();
        return redirect()->back();

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $subscription = Subscription::find($id);
        $user = User::find($subscription->user_id);
        $user->subscription($subscription->name)->cancelNow();

        flash("Subscription was successfully canceled!")->success();
        return redirect()->back();
    }


    public function usersSubscriptions($user_id){
        $user = User::find($user_id);
        if(empty($user)){
            flash("Unknown user")->error();
            return redirect()->back();
        }

        return view("users.subscriptions.index")->with([
            "user" => $user,
            "subscriptions" => $subscriptions = $user->subscriptions()->orderBy("updated_at", "DESC")->get()
        ]);
    }

}
