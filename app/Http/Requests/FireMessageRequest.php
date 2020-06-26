<?php

namespace App\Http\Requests;

use App\Models\RoomsModel;
use Illuminate\Foundation\Http\FormRequest;
use Spatie\Permission\Models\Permission;

class FireMessageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $room = RoomsModel::find($this->route("room_id"));
        return !empty($room);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'message' => 'string'
        ];
    }
}
