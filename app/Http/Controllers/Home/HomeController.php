<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    /**
     * Show the login page.
     */
    public function index(Request $request): Response
    {
        return Inertia::render('Home', []);
    }
}
