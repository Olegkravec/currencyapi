<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;

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
            'amount' => 'required|integer',
        ];
    }
}
