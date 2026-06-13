<?php

namespace App\Http\Controllers\Api\Private\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\CreateUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Services\User\UserService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->middleware('auth:api');
        $this->userService = $userService;
    }

        /**
     * Display a listing of the resource.
     */
    public function allusers(Request $request)
    {
        return $this->userService->allusers($request);
    }

    /**
     * Show the form for creating a new resource.
     */

    public function create(CreateUserRequest $req)
    {

        return $this->userService->createUser($req->validated());

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request)
    {
        return $this->userService->editUser($request->userId);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request)
    {
        
        return $this->userService->updateUser($request->validated());

    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete(Request $request, UserService $userService)
    {   
        
        return $this->userService->deleteUser($request->userId);

    }


}
