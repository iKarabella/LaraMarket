<?php

namespace App\Http\Controllers\Admin\UsersManage;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class UsersManageController extends Controller
{
    /**
     * Show the login page.
     */
    public function index(Request $request): Response
    {
        return Inertia::render('Admin/UsersManage/Users', []);
    }
}
