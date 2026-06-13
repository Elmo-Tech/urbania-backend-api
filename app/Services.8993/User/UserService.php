<?php

namespace App\Services\User;

use App\Http\Resources\AllUserCollection;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Utils\PaginateCollection;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;


class UserService{

    private $users;

    public function __construct(User $users)
    {
        $this->users = $users;
    }

    public function allusers(Object $request){
        $allUsers = $this->users->newQuery()->where(function($query) use($request) {

        /*$username = $request->username? ['LIKE', "%".$request->username."%"]: ['!=', ""];
        $firstname = $request->firstname? ['LIKE', "%".$request->firstname."%"]: ['IS', null];
        $lastname = $request->lastname? ['LIKE', "%".$request->lastname."%"]: ['IS ', null] ;
        $status = $request->status !== null? ['=', $request->status]: ['!=', ''];*/

        $username = $request->username? ['LIKE', "%".$request->username."%"]: null;
        $firstname = $request->firstname? ['LIKE', "%".$request->firstname."%"]: null;
        $lastname = $request->lastname? ['LIKE', "%".$request->lastname."%"]:  null;
        $status = $request->status !== null? ['=', $request->status]: null;

        $query/*->Where('username', $username[0], $username[1])
                ->Where ( 'firstname',  $firstname[0], $firstname[1])
                ->Where ( 'lastname', $lastname[0], $lastname[1])
                ->Where ( 'status', $status[0], $status[1])
                ->whereNot('id', Auth::id());*/
                ->when(isset($request->username), function ($query) use ($username) {
                    return $query->where('username', $username[0], $username[1]);
                })
                ->when(isset($request->firstname), function ($query) use ($firstname) {
                    return $query->where('firstname', $firstname[0], $firstname[1]);
                })
                ->when(isset($request->lastname), function ($query) use ($lastname) {
                    return $query->where('lastname', $lastname[0], $lastname[1]);
                })
                ->when(isset($request->status), function ($query) use ($status) {
                    return $query->where('status', $status[0], $status[1]);
                })
                ->whereNot('id', Auth::id());
        })->get();
            
        
        return response()->json([
            'UsersPage' => new AllUserCollection(PaginateCollection::paginate($allUsers, $request->page_size?$request->page_size:10))
        ], 200);

    }
        
    public function createUser(array $userData){
                    
        
        try {

            DB::beginTransaction();
            
            $newUser = User::create([
                'username' => $userData['username'],
                'firstname' => $userData['firstname'],
                'lastname' => $userData['lastname'],
                'email' => $userData['email'],
                'password' => Hash::make($userData['password'])
            ]);
    
            $role = Role::find($userData['roleType']);
    
            $newUser->assignRole($role->id);
    
            DB::commit();
            
            return response()->json([
                'message' => 'user has been created !'
            ], 200);
                                

        } catch (\Exception $e) {

            DB::rollBack();
            throw $e;
        
        }


    }
    
    public function editUser(int $userId){
        $user = User::find($userId);

        return response()->json(
            new UserResource($user)
        ,200);

    }

    public function updateUser(array $userData){

        $password = strlen($userData['newPassword']) > 0 ? Hash::make($userData['newPassword']): $userData['oldPassword'];
        
        $user = User::find($userData['userId']);
        $user->fill([
            'username' => $userData['username'],
            'firstname' => $userData['firstname'],
            'lastname' => $userData['lastname'],
            'email' => $userData['email'],
            'status' => $userData['status'],
            'password' => $password
        ]);
        
        $user->save();

        $role = Role::find($userData['roleType']);

        $user->syncRoles($role->id);

        return response()->json([
            'message' => 'user has been updated!'
        ], 200);

    }

    public function deleteUser(int $userId){
        $user = User::find($userId);
        $user->delete();
        return response()->json([
            'message' => 'user has been deleted!'
        ], 200);

    }

}