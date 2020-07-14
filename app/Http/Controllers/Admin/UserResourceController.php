<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserUpdateAdminRequest;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserResourceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view("users.index")->with([
            "users" => User::paginate(30)
        ]);
    }

    /**
     * Show the form for creating a new resource.
     * @throws \Exception
     */
    public function create()
    {
        throw new \Exception("Method is not implemented yet");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @throws \Exception
     */
    public function store(Request $request)
    {
        throw new \Exception("Method is not implemented yet");
    }

    /**
     * Display the specified resource.
     *
     * @param $id
     * @throws \Exception
     */
    public function show($id)
    {
        throw new \Exception("Method is not implemented yet");
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if(!Auth::user()->can("edit users")){
            flash("You cannot edit user")->error();
            return redirect()->back();
        }

        $user = User::find($id);
        if(empty($user)){
            flash("User is not exist")->error();
            return redirect()->back();
        }
        return view("users.edit")->with([
            "user" => $user
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UserUpdateAdminRequest $request
     * @param $user_id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UserUpdateAdminRequest $request, $user_id)
    {
        $validated = $request->validated();

        $validated["password"] = bcrypt($validated["password"]);
        $updated = User::find($user_id)->update($validated);

        if($updated)
            flash("Successfully updated!")->success();
        else
            flash("Something went wrong!")->error();

        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $id
     * @throws \Exception
     */
    public function destroy($id)
    {
        throw new \Exception("Method is not implemented yet");
    }
}
