<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class RolesAndPermissionsController extends Controller
{
    public function getRolesAndPermissions()
    {
        $users = User::with('roles')->whereHas('roles', function($q){
            $q->where('name', '<>', '');
        })->get();
        $roles = Role::with(['permissions'])->get();
        return response()->json([
            'users' => $users,
            'roles' => $roles
        ], 200);

        // return Role::with(['permissions', 'users'])->get();
    }

    public function manageRolesAndPermissions(Role $role, Request $request)
    {
        DB::beginTransaction();
        $response = 'Success';
        try {
            switch ($request->type) {
                case 'add':
                    $role = $role->create(['name' => $request->role, 'description' => $request->description]);
                    foreach ($request->data as $permit) {
                        $permission = Permission::findByName($permit);
                        $role->givePermissionTo($permission->name);
                    }
                    break;
                case 'edit':
                    if (!$role) {
                        return abort('No role found', 422);
                    }
                    $toRemove = $role->permissions()->whereNotIn('name', $request->data)->get();
                    if (count($toRemove)) {
                        foreach($toRemove as $value) {
                            $role->revokePermissionTo($value->name);
                        }
                    }
                    foreach ($request->data as $permit) {
                        $checkIfExist = $role->permissions()->where('name', $permit)->get();
                        if (!count($checkIfExist)) {
                            $role->givePermissionTo($permit);
                        }
                    }
                    $role->update(['name' => $request->role, 'description' => $request->description]);
                    break;
                case 'delete':
                    $users = User::with('roles')->whereHas('roles', function($q) use($request) { $q->where('name', $request->role); })->get();
                    if (count($users)) {
                        foreach($users as $user) {
                            $userFind = User::find($user->id);
                            $userFind->removeRole($request->role);
                        }
                    }
                    foreach($request->data[0]['permissions'] as $permit) {
                        $role->revokePermissionTo($permit['name']);
                    }
                    $role->delete();
                    break;
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            abort(response()->json('Failed', 500));
        }
        DB::commit();
        return $response;
    }

    public function getPermissions()
    {
        return Permission::get();
    }

}
