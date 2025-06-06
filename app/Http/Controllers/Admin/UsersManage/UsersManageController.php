<?php

namespace App\Http\Controllers\Admin\UsersManage;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UsersManage\GetListRequest;
use App\Http\Resources\Admin\UsersManage\UsersResource;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class UsersManageController extends Controller
{
    /**
     * Show the login page.
     */
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
            'users'=>UsersResource::collection($users->paginate($request->perPage?intval($request->perPage):25)->withQueryString())
        ]);
    }
}
