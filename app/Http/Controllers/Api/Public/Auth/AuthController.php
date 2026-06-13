<?php

namespace App\Http\Controllers\Api\Public\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\Role\RoleResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\UserProfile;
use App\Services\UserRolePremission\UserPermissionService;
use Spatie\Permission\Models\Role;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    protected $userPermissionService;

    public function __construct(UserPermissionService $userPermissionService)
    {
        $this->middleware('auth:api', ['except' => ['login', 'register', 'refresh']]);
        $this->userPermissionService = $userPermissionService;
    }

    /*
    ** register method
    */
    public function register(Request $request)
    {
        try {
            $validateUserDate = Validator::make($request->all(), [
                'firstname' => '',
                'lastname' => '',
                'username' => 'required',
                'email' => 'required',
            ]);

            if ($validateUserDate->fails()) {
                return response()->json([
                    'errors' => $validateUserDate->errors()
                ], 401);
            }

            $user = User::create([
                'firstname' => $request->firstname,
                'lastname' => $request->lastname,
                'username' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($request->password),
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
    */
    public function login(Request $request)
    {
        try {
            $validateUserData = Validator::make($request->all(), [
                'userName' => 'required',
                'password' => 'required'
            ]);

            if ($validateUserData->fails()) {
                return response()->json([
                    'errors' => $validateUserData->errors()
                ], 401);
            }

            $userToken = Auth::attempt([
                'username' => $request->userName,
                'password' => $request->password
            ]);

            if (!$userToken) {
                return response()->json([
                    'message' => 'Username Or Password is Wrong',
                ], 401);
            }

            if ($userToken && Auth::user()->status == 0) {
                return response()->json([
                    'message' => 'your account is inactive!',
                ], 401);
            }

            $user = Auth::user();

            $refreshToken = $this->createRefreshToken($user);

            return response()->json(
                $this->authResponse($user, $userToken, $refreshToken),
                200
            );

        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }

    /*
    ** refresh token method
    **
    ** request body:
    ** {
    **     "refreshToken": "your_refresh_token"
    ** }
    */
    public function refresh(Request $request)
    {
        try {
            $validateData = Validator::make($request->all(), [
                'refreshToken' => 'required|string',
            ]);

            if ($validateData->fails()) {
                return response()->json([
                    'errors' => $validateData->errors()
                ], 401);
            }

            $payload = JWTAuth::setToken($request->refreshToken)->getPayload();

            if ($payload->get('type') !== 'refresh') {
                return response()->json([
                    'message' => 'Invalid refresh token',
                ], 401);
            }

            $user = User::find($payload->get('sub'));

            if (!$user) {
                return response()->json([
                    'message' => 'User not found',
                ], 401);
            }

            if ($user->status == 0) {
                return response()->json([
                    'message' => 'your account is inactive!',
                ], 401);
            }

            $token = Auth::login($user);

            $newRefreshToken = $this->createRefreshToken($user);

            return response()->json(
                $this->authResponse($user, $token, $newRefreshToken),
                200
            );

        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Invalid or expired refresh token',
            ], 401);
        }
    }

    /*
    ** logout method
    */
    public function logout()
    {
        Auth::logout();

        return response()->json([
            'message' => 'you have logged out'
        ]);
    }

    private function createRefreshToken(User $user)
    {
        $accessTtl = (int) config('jwt.ttl', 60);
        $refreshTtl = (int) config('jwt.refresh_ttl', 20160);

        JWTAuth::factory()->setTTL($refreshTtl);

        $refreshToken = JWTAuth::claims([
            'type' => 'refresh'
        ])->fromUser($user);

        JWTAuth::factory()->setTTL($accessTtl);

        return $refreshToken;
    }

    private function authResponse(User $user, $token, $refreshToken)
    {
        $userRoles = $user->getRoleNames();

        $role = Role::findByName($userRoles[0]);

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

        $roleName = $role->name;

        $sidebar = [];

        foreach ($sidebarAccess as $item) {
            $sidebar[] = [
                'routeName' => $item['name'],
                'access' => in_array($roleName, $item['roles']),
            ];
        }

        return [
            'token' => $token,
            'refreshToken' => $refreshToken,

            // milliseconds
            'expiresIn' => ((int) config('jwt.ttl', 60)) * 60 * 1000,

            'userProfile' => new UserProfile($user),
            'role' => new RoleResource($role),
            'permissions' => $this->userPermissionService->getUserPermissions($user),
            'sidebar' => $sidebar,
        ];
    }
}
