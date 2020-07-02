<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentsAPIController extends Controller
{
    public function create(){
        $user = Auth::guard("api")->user();
        $stripeCustomer = $user->createOrGetStripeCustomer();
        return view("payments.create")->with([
            'intent' => $user->createSetupIntent(),
            'token' => Auth::tokenById(Auth::id()),
            'postback_url' => route("payments.storeMethod")
        ]);
    }

    public function storeMethod(Request $request){
        $user = Auth::user();
        $paymentMethodID = $request->get('payment_method');

        if( $user->stripe_id == null ){
            $user->createAsStripeCustomer();
        }

        $user->addPaymentMethod( $paymentMethodID );
        $user->updateDefaultPaymentMethod( $paymentMethodID );

        return response()->json( null, 204 );
    }
}
