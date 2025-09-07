<?php

namespace App\Http\Controllers\Modulkassa;

use App\Http\Controllers\Controller;
use App\Models\Log;
use Illuminate\Http\Request;

class ModulKassaController extends Controller
{
    public function index(Request $request, $orderCode)
    {
        //TODO тестирование, возможно не потребуется этот контроллер
        Log::create([
            'message'=>$orderCode,
            'author'=>'ModulkassaController@index',
            'object'=>$request->toArray()
        ]);
    }
}
