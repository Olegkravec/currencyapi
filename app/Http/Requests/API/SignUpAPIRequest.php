<?php

namespace App\Http\Requests\API;

use App\User;
use Illuminate\Foundation\Http\FormRequest;


/**
 * @description
 *  This request designed to demonstrate SignUp request with required input params
 *
 * @summary Register new user
 *
 * @_201 Request successfully handled, user created.
 * @_400 Bad request. User already exist.
 * @_422 Wrong input parameters. One of required input fields does not exist or has not valid data. Check response "errors" array
 *
 * @name Name of user
 * @email Email
 * @password Password, must have more than 6 chars. Must have the "password_confirmation" field equal with this.
 * @password_confirmation Confirmation for password, must be equals with "password" field.
 */
class SignUpAPIRequest extends FormRequest
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
            "name" => "string",
            "email" => "email",
            "password" => "min:6|confirmed",
        ];
    }
}
