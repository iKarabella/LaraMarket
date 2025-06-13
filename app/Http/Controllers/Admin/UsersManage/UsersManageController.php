<?php

namespace App\Http\Controllers\Admin\UsersManage;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UsersManage\GetListRequest;
use App\Http\Requests\Admin\UsersManage\UserStoreRequest;
use App\Http\Resources\Admin\UsersManage\UsersResource;
use App\Models\Role;
use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;

class UsersManageController extends Controller
{
    public function index(GetListRequest $request): Response
    {
        $users = User::with(['roles']);
        
        if($request->order){
            if ($request->desc) $users->orderByDesc($request->order);
            else $users->orderBy($request->order);
        }
        if ($request->nickname)   $users->where('nickname', 'like', '%'.$request->login.'%');
        if ($request->phone)   $users->where('phone', 'like', '%'.$request->phone.'%');
        if ($request->name)    $users->where('name',  'like', '%'.$request->name.'%');
        if ($request->surname) $users->where('surname','like','%'.$request->surname.'%');
        
        return Inertia::render('Admin/UsersManage/Users', [
            'users'=>UsersResource::collection($users->paginate($request->perPage?intval($request->perPage):25)->withQueryString()),
            'roles'=>Role::all(),
        ]);
    }

    public function store(UserStoreRequest $request)
    {
        $validated=$request->validated();

        $user=User::where('id', $request->id)->firstOrFail();
        
        $user->fill($validated)->save();

        if($validated['roles']){
            $user->roles()->sync(array_column($validated['roles'], 'id'));
        }
    }
}
