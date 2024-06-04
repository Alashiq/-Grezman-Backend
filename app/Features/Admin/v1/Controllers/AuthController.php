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

class AuthController extends Controller
{

    // Login Admin
    public function login(Request $request)
    {
        $admin = Admin::with('role')->where('phone', $request->phone)->first();

        if (!$admin || !Hash::check($request->password, $admin->password)) {

            return response()->json(['success' => false, 'message' => 'إسم المستخدم أو كلمة المرور غير صحيحة'], 400);
        }

        if ($admin->status == 0) {
            return response()->json(['success' => false, 'message' => 'هذا الحساب غير مفعل قم بالتواصل مع المسؤول لتفعيل حسابك'], 400);
        } elseif ($admin->status == 2)
            return response()->json(['success' => false, 'message' => 'هذا الحساب محظور ولا يمكن استخدامه مجددا'], 400);

        $admin->token = $admin->createToken('website', ['role:admin'])->plainTextToken;
        return response()->json([
            'success' => true,
            'message' => 'تم تسجيل الدخول بنجاح',
            'admin' => new AdminResource($admin),
        ]);
    }


    // Auth Admin
    public function profile(Request $request)
    {
        // return $request->user();
        $request->user()->token = $request->bearerToken();
        return response()->json([
            'success' => true,
            'message' => 'تم تسجيل الدخول بنجاح',
            'admin' => new AdminResource($request->user()),
        ]);
    }


    // Logout
    public function logout(Request $request)
    {
        $user = $request->user();
        if (!$user)
            return $this->unauthorized('إنتهت جلسة الدخول مسبقا');
        $user->currentAccessToken()->delete();

        // $user->tokens()->delete();
        return response()->json(["success" => true, "message" => "تم تسجيل الخروج بنجاح"]);
    }


    // Add Change Passowrd
    public function password(Request $request)
    {
        if ($request->old_password && $request->new_password) {
            if (!Hash::check($request->old_password, $request->user()->password)) {
                return response()->json(['success' => false, 'message' => 'كلمة المرور القديمة غير صحيحة'], 400);
            }
            $request->user()->password = Hash::make($request->new_password);
            $request->user()->save();
            return response()->json(["success" => true, "message" => "تم تغيير كلمة المرور بنجاح"], 200);
        } else {
            return response()->json(["success" => false, "message" => "لم تقم بإرسال اي حقول لتعديلها"], 400);
        }
    }


    // Add Change Name
    public function name(Request $request)
    {


        if ($request->first_name || $request->last_name) {
            $request->user()->Update(
                request()->only(
                    "first_name",
                    "last_name"
                )
            );
            return response()->json([
                "success" => true,
                "message" => "تم تحديث الإسم بنجاح",
                "user" => $request->user(),
            ], 200);
        } else {
            return $this->badRequest('يجب عليك إدخال الإسم أو اللقب');
        }
    }



    //  Add Change Photo
    public function photo(Request $request)
    {

        if (!$request->hasFile('photo')) {
            return $this->badRequest("يجب عليك إختيار صورة ليتم رفعها");
        }
        if (Validator::make($request->all(), [
            'photo' => 'mimes:jpg,jpeg,png',
        ])->fails()) {
            return response()->json(["success" => false, "message" => "الملف الذي اخترته ليس صورة"], 400);
        }

        $file_name = Str::uuid() . '_' . $request->photo->getClientOriginalExtension();
        $file_path = $request->file('photo')->storeAs('app/admin', $file_name, 'public');

        $request->user()->photo = $file_path;
        $request->user()->save();



        return response()->json([
            "success" => true,
            "message" => "تم تحديث صورة المستخدم بنجاح",
            "photo" => url(Storage::url($file_path))
        ]);
    }


    // End of Controller
}
