<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\SignInAPIRequest;
use App\Http\Requests\API\SignUpAPIRequest;
use App\Http\Requests\API\UpdateUserAPIRequest;
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
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SignUpAPIRequest $request)
    {
        $user = User::where("email", $request->validated()["email"])->first();
        if(!empty($user)){
            return response([
                "status" => "error",
                "message" => "User already exist",
            ], 400);
        }
        $credentials = $request->validated();
        $credentials["password"] = bcrypt($credentials["password"]);

        $user = User::create($credentials);
        $user->createOrGetStripeCustomer();

        return response([
            "status" => "success",
            "message" => "Created successfully",
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateUserAPIRequest $request, $id)
    {
        $validated = $request->validated();
        $user = Auth::guard("api")->user();
        if(!empty($validated["password"]))
            $validated["password"] = bcrypt($validated["password"]);

        if(empty($user)){
            return response([
                "status" => "error",
                "message" => "User not found",
            ], 404);
        }
        $updated = $user->update($validated);

        return response([
            "status" => $updated,
            "message" => "User updated",
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }


    public function signin(SignInAPIRequest $request){
        $credentials = [
            "email" => $request->validated()['email'],
            "password" => $request->validated()['password']
        ];

        if (! $token = Auth::guard("api")->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }


        return response(Auth::guard("api")->user())
            ->header('access_token', $token)
            ->header('token_type', 'bearer')
            ->header('expires_in', 3600);
    }

}
