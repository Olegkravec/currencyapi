<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;


/**
 * @_200 Currency comparing was successfully done
 * @_403 Unauthorized or premium subscription not found
 *
 * @pair Part of URI that indicates system about main of comparable currency
 */
class CompareCurrencyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $user = Auth::guard("api")->user();
        return !empty($user) and !empty($user->subscriptions()->active()->where("name", "premium")->first());
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'compare_to' => 'required|string'
        ];
    }
}
