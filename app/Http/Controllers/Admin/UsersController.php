<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    public function index(){
        return view("users.index")->with([
            "users" => User::paginate(30)
        ]);
    }

    public function edit($id){
        return view("users.edit")->with([
            "user" => User::firstOrFail("id", $id)
        ]);
    }


}
