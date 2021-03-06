<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

/**
 * @description
 *  This request designed to demonstrate Store Subscription request with required input params
 *
 * @summary Create new subscription
 *
 * @_200 Subscription was created successfully!
 * @_403 You dont have default payment method. Create them first, and then try to subscribe user
 * @_422 Wrong input parameters. One of required input fields does not exist or has not valid data. Check response "errors" array
 *
 * @plan Stripe Plan ID that will be subscribed
 */
class StoreSubscriptionAPIRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return !empty(Auth::user());
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'plan' => 'required|min:8'
        ];
    }
}
