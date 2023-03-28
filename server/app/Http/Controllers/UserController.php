<?php

// namespace App\Http\Controllers;

// use Auth;
// use Illuminate\Database\Eloquent\Collection;
// use Illuminate\Support\Facades\Hash;
// use Illuminate\Http\Request;
// use App\Models\User;
// use Illuminate\Support\Facades\Validator;
// use App\Http\Requests\UserPostRequest;
// use Illuminate\Support\Facades\DB;

// class UserController extends Controller
// {
//     public function __construct()
//     {
//         $this->middleware('auth:sanctum');
//     }

//     public function getAuthUser(): User
//     {
//         //return auth()->user();
//         return  auth()->user()->with(['roles' => function($q){ $q->with('permissions'); }])->whereId(auth()->user()->id)->get();
//     }

//     public function getAllUsers(): Collection
//     {
//         // abort_if(auth()->user()->role_id != 1, 403);
//         //return User::all();
//         // return User::with(['roles' => function($q){ $q->with('permissions'); }])->get();
//         $users = User::with(['roles' => function($q){ $q->with('permissions'); }]);
//         if (request()->has('municipality_id')) {
//             $users->whereMunicipalityId(request('municipality_id'));
//         }
//         return $users->get();
//     }

//     public function fetchAuthUser()
//     {
//         //return $request->user();
//         return [
//             'auth' => auth()->user()->with(['roles' => function($q){ $q->with('permissions'); }])->whereId(auth()->user()->id)->first(),
//             'enums' => (new \App\Constants)->enumValues()
//         ];
//     }

//     public function manageUser(User $user, UserPostRequest $request)
//     {
//         DB::beginTransaction();
//         $response = 'Success';
//         try {
//             switch ($request->type) {
//                 case 'add':
//                     if ($request->hasFile('image')) $request->image->storeAs('images', $request->file_name, 'public');
//                     $created_user = User::create($request->validated());
//                     $created_user->assignRole(json_decode($request->role));
//                     break;
//                 case 'edit':
//                     $request_validated = $request->validated();
//                     if ($request->hasFile('image')) {
//                         try {
//                             if (explode("/", $user->avatar)[0] != "images") {
//                                 unlink($user->avatar);
//                             }
//                         } catch (\Throwable $th) {}
//                         $request->image->storeAs('images', $request->file_name, 'public');
//                     } else {
//                         unset($request_validated['avatar']);
//                     }
//                     $user->syncRoles(json_decode($request->role));
//                     $user->update($request_validated);
//                     break;
//                 case 'delete':
//                     try {
//                         if (explode("/", $user->avatar)[0] != "images") {
//                             unlink($user->avatar);
//                         }
//                     } catch (\Throwable $th) {}
//                     $roles = array_map(function ($role) { return $role->name; }, $user->roles->all());
//                     foreach ($roles as $role) $user->removeRole($role);
//                     $user->delete();
//                     break;
//             }
//         } catch (\Throwable $th) {
//             DB::rollBack();
//             abort(response()->json('Failed', 500));
//         }
//         DB::commit();
//         return $response;
//     }

//     public function updateProfile(User $user, Request $request)
//     {
//         DB::beginTransaction();
//         try {
//             if (request('to_update') == 'profile') {
//                 if ($request->hasFile('avatar')) {
//                     try {
//                         if (explode("/", $user->avatar)[0] != "images") {
//                             unlink($user->avatar);
//                         }
//                     } catch (\Throwable $th) {}

//                     $request->avatar->storeAs('images', $request->file_name, 'public');

//                     $user->update([
//                         'avatar' => $request->path
//                     ]);
//                 } else {
//                     return response()->json(
//                         [
//                             'message' => [['Please select an image']],
//                             'type' => 'warning',
//                             'title' => ucfirst(request('to_update'))
//                         ],
//                         405
//                     );
//                 }
//             } else if (request('to_update') == 'name') {
//                 $user->update([
//                     'first_name' => $request->first_name,
//                     'middle_name' => $request->middle_name,
//                     'last_name' => $request->last_name
//                 ]);
//             } else if (request('to_update') == 'contact') {
//                 $user->update([
//                     'email' => $request->email,
//                     'contact_number' => $request->contact_number,
//                 ]);
//             } else if (request('to_update') == 'username') {
//                 $user->update([
//                     'username' => $request->username,
//                 ]);
//             } else if (request('to_update') == 'password') {
//                 if (
//                     Hash::check($request->old_password, auth()->user()->password) &&
//                     $request->new_password == $request->confirm_new_password
//                 ) {
//                     if ($request->old_password !== $request->new_password ) {
//                         $user->update([
//                             'password' => $request->new_password,
//                         ]);
//                     } else {
//                         return response()->json(
//                             [
//                                 'message' => [["Current/New password no changes"]],
//                                 'type' => 'warning',
//                                 'title' => ucfirst(request('to_update'))
//                             ],
//                             405
//                         );
//                     }
//                 } else {
//                     return response()->json(
//                         [
//                             'message' => [["Current/New password didn't match"]],
//                             'type' => 'warning',
//                             'title' => ucfirst(request('to_update'))
//                         ],
//                         405
//                     );
//                 }
//             }

//             DB::commit();
//             return response()->json(
//                 [
//                     'message' => [['Your '.strtolower(request('to_update')).' has been successfully updated']],
//                     'type' => 'success',
//                     'title' => ucfirst(request('to_update')),
//                     'auth_user' => auth()->user()->with(['roles' => function($q){ $q->with('permissions'); }])->whereId(auth()->user()->id)->first()
//                 ],
//                 200
//             );
//         } catch (\Throwable $th) {
//             DB::rollBack();
//             return response()->json(
//                 [
//                     'message' => [['Failed to update your '.strtolower(request('to_update'))]],
//                     'type' => 'error',
//                     'title' => ucfirst(request('to_update'))
//                 ],
//                 405
//             );
//         }
//     }

//     public function updateUserAccountStatus(User $user, Request $request)
//     {
//         DB::beginTransaction();
//         try {
//             $user->update([
//                 'is_active' => $request->status,
//             ]);
//             $user->tokens()->delete();
//             DB::commit();
//             return response()->json(
//                 [
//                     'message' => [['User account has been successfully '.($request->status ? 'activated.' : 'deactivated.')]],
//                     'type' => 'success',
//                     'title' => ucfirst($request->status ? 'Activate' : 'Deactivate')
//                 ],
//                 200
//             );
//         } catch (\Throwable $th) {
//             DB::rollBack();
//             return response()->json(
//                 [
//                     'message' => [['Failed to '.($request->status ? 'activated.' : 'deactivated.')]],
//                     'type' => 'error',
//                     'title' => ucfirst($request->status ? 'Activate' : 'Deactivate')
//                 ],
//                 405
//             );
//         }
//     }

// }


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        if (!auth()->user()->can('view-user-list')) {
            return response()->json(
                [
                    'message' => 'Unauthorized',
                    'type' => 'error',
                ],
                401
            );
        }

        $users = User::query();

        $users->with(['roles' => function($q){ $q->with('permissions'); }]);

        if (request()->has('municipality_id')) {
            $users->whereMunicipalityId(!!request('municipality_id') ? request('municipality_id') : NULL);
        }

        if (request()->has('user_status') && request('user_status') != 'All') {
            $users->whereIsActive(request('user_status') == 'Deactivated' ? 0 : 1);
        }

        if (request()->has('search')) {
            $users->whereColumnContains(request('search'), json_decode(request('search_column')));
        }

        if (request()->has('order_by')) {
            [$column, $sort] = explode(",", request('order_by'));
            $users->orderBy($column, $sort);
        } else {
            $users->orderBy('created_at', 'DESC');
        }

        return request()->has('page') ?
            $users->paginate(intval(request('rows')))->appends(request()->all()) :
            $users->get();

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
    public function store(Request $request)
    {

        if (!auth()->user()->can('add-user')) {
            return response()->json(
                [
                    'message' => 'Unauthorized',
                    'type' => 'error',
                ],
                401
            );
        }

        DB::beginTransaction();
        try {
            $fields = $request->validate([
                'first_name' => 'required|string|max:55',
                'middle_name' => 'nullable',
                'last_name' => 'required|string|max:55',
                'username'  => 'required|string|unique:users',
                'password' => 'required|string|confirmed|min:5|regex:/^[A-Za-z0-9_]+$/',
                // 'is_active' => 'required|boolean',
                'avatar' => 'nullable',
            ]);

            $user =  User::create([
                'first_name' => $fields['first_name'],
                'middle_name' => $fields['middle_name'],
                'last_name' => $fields['last_name'],
                'username' =>  $fields['username'],
                'password' => $fields['password'],
                'is_active' => 1,
                'avatar' => $request->hasFile('image') ? $request->avatar : NULL,
            ]);

            //$token = $user->createToken('myapptoken')->plainTextToken;

            if (!!json_decode($request->role)) {
                $user->assignRole(json_decode($request->role));
            }

            // $response = [
            //     'user' => $user,
            //     // 'token' => $token
            // ];

            // return response($response, 201);

            if ($request->hasFile('image')) {
                // $avatar_path = $request->avatar;
                $request->image->storeAs('images', $request->file_name, 'public');
            } else {
                // $avatar_path = NULL;
            }

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(
                [
                    'message' => 'Failed to add "'.$request->username.'"',
                    'type' => 'error',
                ],
                500
            );
        }
        DB::commit();
        return response()->json(
            [
                'message' => '"'.$request->username.'" has been successfully added',
                'type' => 'success',
            ],
            200
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {

        if (!auth()->user()->can('edit-user')) {
            return response()->json(
                [
                    'message' => 'Unauthorized',
                    'type' => 'error',
                ],
                401
            );
        }

        DB::beginTransaction();
        try {
            $old_image_path = (clone $user)->avatar;

            if (json_decode($request->change_username_password)) {
                $fields = $request->validate([
                    'first_name' => 'required|string|max:55',
                    'middle_name' => 'nullable',
                    'last_name' => 'required|string|max:55',
                    'username'  => 'required|string|unique:users,username,'.$user->id,
                    'password' => 'required|string|confirmed|min:5|regex:/^[A-Za-z0-9_]+$/',
                    'avatar' => 'nullable',
                ]);

                $user->update([
                    'first_name' => $fields['first_name'],
                    'middle_name' => $fields['middle_name'],
                    'last_name' => $fields['last_name'],
                    'username' =>  $fields['username'],
                    'password' => bcrypt($fields['password']),
                    'avatar' => $request->hasFile('image') ? $request->avatar : $user->avatar,
                ]);
            } else {
                $fields = $request->validate([
                    'first_name' => 'required|string|max:55',
                    'middle_name' => 'nullable',
                    'last_name' => 'required|string|max:55',
                    'avatar' => 'nullable',
                ]);

                $user->update([
                    'first_name' => $fields['first_name'],
                    'middle_name' => $fields['middle_name'],
                    'last_name' => $fields['last_name'],
                    'avatar' => $request->hasFile('image') ? $request->avatar : $user->avatar,
                ]);
            }

            if ($request->hasFile('image')) {
                try {
                    if (explode("/", $old_image_path)[0] != "images") {
                        unlink($old_image_path);
                    }
                } catch (\Throwable $th) {}
                $request->image->storeAs('images', $request->file_name, 'public');
            }

            if (!!json_decode($request->role)) {
                $user->syncRoles(json_decode($request->role));
            }

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(
                [
                    'message' => 'Failed to update',
                    'type' => 'error',
                ],
                500
            );
        }
        DB::commit();
        return response()->json(
            [
                'message' => 'Record has been successfully updated',
                'type' => 'success',
            ],
            200
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {

        if (!auth()->user()->can('delete-user')) {
            return response()->json(
                [
                    'message' => 'Unauthorized',
                    'type' => 'error',
                ],
                401
            );
        }

        DB::beginTransaction();
        try {
            try {
                //if softdelete dont remove image = in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses(User::class))
                if (explode("/", $user->avatar)[0] != "images") {
                    unlink($user->avatar);
                }
            } catch (\Throwable $th) {}
            $roles = array_map(function ($role) { return $role->name; }, $user->roles->all());
            foreach ($roles as $role) $user->removeRole($role);
            $user->delete();

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(
                [
                    'message' => 'Failed to removed "'.$user->username.'"',
                    'type' => 'error',
                ],
                500
            );
        }
        DB::commit();
        return response()->json(
            [
                'message' => '"'.$user->username.'" has been successfully removed',
                'type' => 'success',
            ],
            200
        );
    }
}
