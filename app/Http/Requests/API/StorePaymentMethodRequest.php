<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

/**
 * @description
 *  This request designed to demonstrate Payment method storing request with required input params
 *
 * @summary Store payment method
 *
 * @_201 Payment method successfully created
 * @_422 Wrong input parameters. One of required input fields does not exist or has not valid data. Check response "errors" array.
 *
 * @payment_method Stripe ID of PaymentMethod object
 */
class StorePaymentMethodRequest extends FormRequest
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
            "payment_method" => "required"
        ];
    }
}
