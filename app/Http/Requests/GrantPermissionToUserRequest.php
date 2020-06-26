<?php

namespace App\Http\Requests;

use App\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;

class GrantPermissionToUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $user = User::disableCache()->find($this->route("user_id"));
        $permission = Permission::find($this->route("permission_id"));

        return !empty($user) and !empty($permission) and Auth::user()->can("edit users permissions");
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'user_id' => 'int',
            'permission_id' => 'int'
        ];
    }
}
