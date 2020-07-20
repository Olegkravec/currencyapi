<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\StorePaymentMethodRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class PaymentsAPIController extends Controller
{
    /**
     * Create  new payment intent
     * @header Authorization|required|JWT authorization token
     * @return array|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create(){
        $user = Auth::guard("api")->user();
        $stripeCustomer = $user->createOrGetStripeCustomer();
        return view("payments.create")->with([
            'intent' => $user->createSetupIntent(),
            'token' => Auth::tokenById(Auth::id()),
            'postback_url' => route("payments.storeMethod")
        ]);
    }


    /**
     *
     * @summary
     * Store created payment intent
     *
     * @description
     * This request designed to demonstrate Payment method storing request with required input params
     *
     * @payment_method Stripe PaymentMethod ID that will be assigned to authenticated user
     *
     * @header Authorization|required|JWT authorization token
     * @param StorePaymentMethodRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeMethod(StorePaymentMethodRequest $request){
        $user = Auth::user();
        $paymentMethodID = $request->get('payment_method');

        if( $user->stripe_id == null ){
            $user->createAsStripeCustomer();
        }

        $user->addPaymentMethod( $paymentMethodID );
        $user->updateDefaultPaymentMethod( $paymentMethodID );

        return response()->json( null, Response::HTTP_CREATED );
    }
}
