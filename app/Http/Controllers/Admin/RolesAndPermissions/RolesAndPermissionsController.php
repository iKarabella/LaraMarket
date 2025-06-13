<?php

namespace App\Http\Controllers\Admin\RolesAndPermissions;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RolesAndPermissions\RolesAndPermissionsUpdateRequest;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class RolesAndPermissionsController extends Controller
{
    /**
     * Show the login page.
     */
    public function index(Request $request): Response
    {
        $permissions = Permission::all(['id', 'name']);
        $roles = Role::with(['permissions'])->get();
        
        return Inertia::render('Admin/RolesAndPermissions/RolesAndPermissions', [
            //'users'=>UsersResource::collection($users->paginate($request->perPage?intval($request->perPage):25)->withQueryString()),
            'roles'=>$roles->toArray(),
            'permissions'=>$permissions->toArray()
        ]);
    }

    public function store(RolesAndPermissionsUpdateRequest $request) //RolesAndPermissionsUpdateRequest
    {
        $role = Role::updateOrCreate(['id'=>$request->role_id], ['name' => $request->name, 'description' => $request->description]);

        if ($request->permissions) $role->permissions()->sync(array_column($request->permissions, 'id'));
    }

    public function delete(Request $request){

        $request->validate(['role_id' => ['required','numeric', 'exists:roles,id']]);

        Role::where('id', $request->role_id)->delete();
    }
}
