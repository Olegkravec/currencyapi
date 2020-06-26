<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserUpdateAdminRequest;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UsersController extends Controller
{
    public function index(){
        return view("users.index")->with([
            "users" => User::paginate(30)
        ]);
    }

    public function edit($id){
        if(!Auth::user()->can("edit users")){
            flash("You cannot edit user")->error();
            return redirect()->back();
        }

        $user = User::disableCache()->find($id);
        if(empty($user)){
            flash("User is not exist")->error();
            return redirect()->back();
        }
        return view("users.edit")->with([
            "user" => $user
        ]);
    }


    public function update(UserUpdateAdminRequest $request, $user_id){
        $validated = $request->validated();

        $validated["password"] = bcrypt($validated["password"]);
        $updated = User::find($user_id)->update($validated);

        if($updated)
            flash("Successfully updated!")->success();
        else
            flash("Something went wrong!")->error();


        return redirect()->back();
    }
}
