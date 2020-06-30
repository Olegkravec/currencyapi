<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\GrantPermissionToUserRequest;
use App\Http\Requests\PermissionRevokeRequest;
use App\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class UserPermissionsController extends Controller
{
    /**
     * Show table of all users's permission
     * @param $user_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
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

    /**
     * Show table of possible permissions thats can be granted to the user
     * @param $user_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function permission_grant($user_id){
        if(empty($user = User::find($user_id))){
            flash("User is not exist")->error();
            return redirect()->back();
        }

        return view("users.permissions.grant")->with([
            "user" => $user,
            "permissions" => Permission::all()
        ]);
    }

    /**
     * Actually give some permission to user
     * @param GrantPermissionToUserRequest $request
     * @param $user_id
     * @param $permission_id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function permission_create(GrantPermissionToUserRequest $request, $user_id, $permission_id){
        $user = User::find($user_id);
        $user->givePermissionTo(Permission::find($permission_id));

        flash("Permission successfully granted")->success();

        return redirect()->back();
    }

    /**
     * Remove permission from user. Disallow user to do in specified scope
     * @param PermissionRevokeRequest $request
     * @param $user_id
     * @param $permission_id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function permission_revoke(PermissionRevokeRequest $request, $user_id, $permission_id){
        $perm = Permission::find($permission_id);
        $user = User::find($user_id);
        $user->revokePermissionTo($perm);

        flash("Permission <b>{$perm->name}</b> successfully revoked")->success();

        return redirect()->back();
    }
}
