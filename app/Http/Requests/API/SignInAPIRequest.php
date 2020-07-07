<?php

namespace App\Http\Requests\API;

use App\User;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @description
 *  This request designed to demonstrate SignIn request with required input params
 *
 * @summary Login an existing user
 *
 * @additionals Ok, I am working
 *
 * @_200 Request successfully handled, user retrieved.
 * @_401 Bad auth data. Wrong email or password.
 * @_422 Wrong input parameters. One of required input fields does not exist or has not valid data. Check response "errors" array
 *
 * @email Email
 * @password Password, must have more than 6 chars
 */
class SignInAPIRequest extends FormRequest
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
            'email' => "required|email|max:129",
            'password' => "required|min:6",
        ];
    }
}
