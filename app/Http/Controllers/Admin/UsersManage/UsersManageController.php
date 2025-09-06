<?php

namespace App\Http\Controllers\Admin\UsersManage;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UsersManage\GetListRequest;
use App\Http\Requests\Admin\UsersManage\ModulkassaUserRequest;
use App\Http\Requests\Admin\UsersManage\UserStoreRequest;
use App\Http\Resources\Admin\UsersManage\UsersResource;
use App\Models\CashRegister;
use App\Models\Role;
use App\Models\User;
use App\Services\ModulKassa\ModulKassa;
use Illuminate\Http\Request;
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
        if ($request->couriers) $users->whereHas('roles', function($query){
            $query->whereHas('permissions', function($query){
                $query->whereCode('delivery_manage');
            });
        });
        return Inertia::render('Admin/UsersManage/Users', [
            'users'=>UsersResource::collection($users->paginate($request->perPage?intval($request->perPage):25)->withQueryString()),
            'roles'=>Role::with('permissions')->get(),
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

    public function getCurrentCashRegisters(int $userId)
    {
        return CashRegister::whereUserId($userId)->whereSystem('modulkassa')->get(['cr_id', 'details']);
    }

    public function getAvailableCashRegisters(int $userId)
    {
        $usedCashRegisters = $this->getCurrentCashRegisters($userId);

        return (new ModulKassa())->getRetailPoints()->filter(function($point) use ($usedCashRegisters){
            return $usedCashRegisters->doesntContain(function($a) use ($point){return $a->cr_id==$point['id'];});
        });
    }

    public function getModulkassaUsers(int $userId, string $pointId)
    {
        return (new ModulKassa())->getRetailPointUsers($pointId);
    }

    public function setModulkassaUser(ModulkassaUserRequest $request, int $userId)
    {
        CashRegister::firstOrcreate([
            'system'=>'modulkassa',
            'user_id'=>$request->user_id,
            'cashier'=>$request->cashier,
            'cr_id'=>$request->cashierRegister
        ], ['details'=>$request->point]);
    }

    public function removeModulkassaUser(ModulkassaUserRequest $request, int $userId)
    {
        CashRegister::whereSystem('modulkassa')->whereUserId($request->user_id)->whereCrId($request->cashierRegister)->delete();
    }
}
