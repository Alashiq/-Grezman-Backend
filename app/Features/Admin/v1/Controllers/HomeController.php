<?php

namespace App\Features\Admin\v1\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Features\Admin\v1\Resources\AdminResource;
use App\Features\Admin\v1\Models\Admin;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class HomeController extends Controller
{


    // Load Home Statisic
    public function index()
    {
        return response()->json(['success' => true, 'message' => 'ليس لديك الصلاحية للقيام بهذه المهمة', 'data' => [
            'users'=>20,
            'roles'=>30,
        ]
    ], 200);

    }
    

    // End of Controller
}
