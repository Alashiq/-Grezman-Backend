<?php

use App\Features\App\Controllers\AccountController;
use App\Features\App\v1\Controllers\AuthAppV1Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;


Route::get('/', [AuthAppV1Controller::class, 'login']);

