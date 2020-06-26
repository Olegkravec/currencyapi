<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PermissionRevokeRequest;
use App\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class UserPermissionsController extends Controller
{
    public function index($user_id){
        if(empty($user = User::find($user_id))){
            flash("User is not exist")->error();
            return redirect()->back();
        }

        return view("users.permissions.index")->with([
            "user" => $user,
            "permissions" => $user->getAllPermissions()
        ]);
    }


    public function permission_revoke(PermissionRevokeRequest $request, $user_id, $permission_id){
        $perm = Permission::find($permission_id);
        $user = User::find($user_id);
        $user->revokePermissionTo($perm);

        flash("Permission <b>{$perm->name}</b> successfully revoked")->success();

        return redirect()->back();
    }
}
