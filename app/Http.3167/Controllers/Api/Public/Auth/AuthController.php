<?php

namespace App\Http\Controllers\Api\Public\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\Role\PermissionResource;
use App\Http\Resources\Role\RoleResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\UserProfile;
use App\Services\UserRolePremission\UserPermissionService;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Facades\Artisan;

class AuthController extends Controller
{

    protected $userPermissionService;

    public function __construct(UserPermissionService $userPermissionService)
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
        $this->userPermissionService = $userPermissionService;
    }

    /*
    ** register method
    ** 
    **
    */
    public function register(Request $request)
    {
        try {
            $validateUserDate = Validator::make($request->all(), [
                'firstname' => '',
                'lastname' => '',
                'username' => 'required',
                'email' => 'required',
                //'email'=> 'required|email|unique:users,email',
                /*'password'=> [
                    'required',
                    'min:8',
                    'regex:/^.*(?=.{1,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\x]).*$/'
                ]*/
            ]);
        
            if($validateUserDate->fails()){
                return response()->json([
                    'errors' => $validateUserDate->errors()
                ], 401);
            }

            $user = User::create([
                'firstname'=> $request->firstname,
                'lastname'=> $request->lastname,
                'username'=> $request->username,
                'email'=> $request->email,
                'password'=> Hash::make($request->password),
            ]);

            return response()->json([
                'message' => 'user has been created!'
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }

    }


    /*
    ** login method
    ** 
    **
    */
    public function login(Request $request)
    {
        //Artisan::call('db:wipe');
/*
        Artisan::call('migrate');

        Artisan::call('db:seed');

        Artisan::call('storage:link');
*/

        try {
            $validateUserData = Validator::make($request->all(), [
                'userName'=> 'required',
                'password'=> 'required'
            ]);
        
            if($validateUserData->fails()){
                return response()->json([
                    'errors' => $validateUserData->errors()
                ], 401);
            }

            $userToken = Auth::attempt(['username' => $request->userName, 'password' => $request->password]);
            //$userToken = Auth::attempt($validateUserData);

            if(!$userToken){
                return response()->json([
                    'message' => 'Username Or Password is Wrong',
                ], 401);
            }
            
            if($userToken && Auth::user()->status == 0){
                return response()->json([
                    'message' => 'your account is inactive!',
                ], 401);
            }

            $user = Auth::user();
            $userRoles = $user->getRoleNames();
            $role = Role::findByName($userRoles[0]);
            $permissions = $role->permissions;

            $sidebarAccess = [
                [
                    'name' => 'dashboard',
                    'roles' => ['superAdmin', 'admin', 'standard', 'limitata', 'superLimitata'],
                ],
                [
                    'name' => 'users',
                    'roles' => ['superAdmin'],
                ],
                [
                    'name' => 'clients',
                    'roles' => ['superAdmin', 'admin'],
                ],
                [
                    'name' => 'contracts',
                    'roles' => ['superAdmin', 'admin', 'standard'],
                ],
                [
                    'name' => 'parameters',
                    'roles' => ['superAdmin', 'admin', 'standard'],
                ],
                [
                    'name' => 'tickets',
                    'roles' => ['superAdmin', 'admin', 'standard', 'limitata'],
                ],
                [
                    'name' => 'events',
                    'roles' => ['superAdmin', 'admin', 'standard', 'limitata', 'superLimitata'],
                ],
                [
                    'name' => 'excelUpload',
                    'roles' => ['superAdmin', 'admin', 'standard'],
                ],
                [
                    'name' => 'excelQr',
                    'roles' => ['superAdmin', 'admin', 'standard'],
                ],
            ];

            $roleName = $role->name; // Assuming $role is an object with a 'name' property

            $sidebar = [];
            
            foreach ($sidebarAccess as $item) {
                $access = in_array($roleName, $item['roles']);
                $sidebar[] = ["routeName" => $item['name'], "access" => $access];
            }
            
            return response()->json([
                'token' => $userToken,
                'userProfile' => new UserProfile($user),
                'role' => new RoleResource($role), 
                'permissions' => $this->userPermissionService->getUserPermissions($user),
                'sidebar' => $sidebar,
            ], 200);


        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }

    }

    
    /*
    ** logout method
    ** 
    **
    */

    public function logout()
    {
        

        Auth::logout();

        return response()->json(['message' => 'you have logged out']);
    }

    public function refresh(){

        // Pass true as the first param to force the token to be blacklisted "forever".
        // The second parameter will reset the claims for the new token
        $newToken = auth()->refresh(true, true);
        return response()->json([
            'token' => $newToken
        ], 200);
        

    }


}
