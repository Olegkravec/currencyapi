<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @description
 *  This request designed to demonstrate Convert Currency request with required input params
 *
 * @summary Convert currency
 *
 * @_200 Request successfully handled, currency converted.
 * @_401 Bad auth data. Wrong email or password.
 * @_422 Wrong input parameters. One of required input fields does not exist or has not valid data. Check response "errors" array
 *
 * @from Currency that will be converted
 * @to Endpoint currency, that was converted "from"
 * @amount Amount of selected "from" currency
 */
class ConvertCurrencyAPIRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'from' => 'required|string|max:3',
            'to' => 'required|string|max:3',
            'amount' => 'required|integer|min:1',
        ];
    }
}
