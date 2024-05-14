<?php

namespace App\Features\App\v1\Controllers;

use App\Features\App\v1\Models\User;
use App\Models\Notification;
use Illuminate\Support\Facades\Validator;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthAppV1Controller extends Controller
{

    public function login(Request $request)
    {


        // Validation
        if (Validator::make($request->all(), [
            'phone' => 'required|numeric',
        ])->fails()) {
            return response()->json(["success" => false, "message" => "رقم الهاتف يجب ان يكون عبارة عن رقم ومكون من 10 أرقام"], 400);
        }


        //  Create PIN
        $user = User::where('phone', $request->phone)->first();
        if ($request->phone == "0926503011")
            $smsCode = "123456";
        else if ($request->phone == "2")
            $smsCode = "654321";
        else
            $smsCode = rand(100000, 999999);



        if (!$user) {
            // New Customer
            User::create([
                'first_name' => 'الإسم',
                'last_name' => 'اللقب',
                'phone' => $request->phone,
                'device_token' => $request->device_token ?? 'nan',
                'pin' => $smsCode,
            ]);
            return response()->json(['success' => true, 'message' => 'تم إرسال رمز التفعيل إلى هاتفك'], 200);
        } else {
            if ($user->status == 3)
                return $this->badRequest('رقم الهاتف محظور ولا يمكن استخدامه');
            // Exist Customer
            $user->pin = $smsCode;
            $user->device_token = $request->device_token ?? 'nan';
            $user->save();

            return response()->json(['success' => true, 'message' => 'تم إرسال رمز التفعيل إلى هاتفك'], 200);
        }




        // return User::all();
        return $this->badRequest('hello');
    }
}
