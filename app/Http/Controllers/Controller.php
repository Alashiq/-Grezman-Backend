<?php

namespace App\Http\Controllers;

use App\Features\App\v1\Models\User;

abstract class Controller
{

    public function badRequest(String $message)
    {
        return response()->json(['success' => false, 'message' => $message], 400);
    }

    public function unauthorized(String $message)
    {
        return response()->json(['success' => false, 'message' => $message], 401);
    }

    public function empty()
    {
        return response()->json([],204);
    }
}
