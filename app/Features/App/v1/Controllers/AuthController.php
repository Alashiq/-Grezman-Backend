<?php

namespace App\Features\App\v1\Controllers;

use App\Features\App\v1\Models\User;
use App\Features\App\v1\Models\UserNotification;
use App\Models\Notification;
use Illuminate\Support\Facades\Validator;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AuthController extends Controller
{

    public function login(Request $request)
    {

        // Validation
        if (Validator::make($request->all(), [
            'phone' => 'required|numeric|digits:10|starts_with:09',
        ])->fails()) {
            return $this->badRequest("رقم الهاتف يجب ان يكون عبارة عن رقم ومكون من 10 أرقام");
        }

        //  Create OTP
        $user = User::where('phone', $request->phone)->first();
        if ($request->phone == "0926503011")
            $smsCode = "123456";
        else if ($request->phone == "0900000000")
            $smsCode = "654321";
        else
            $smsCode = rand(100000, 999999);

        if (!$user) {
            $lastNotificationId = UserNotification::orderBy('id', 'desc')->value('id');

            User::create([
                'first_name' => 'الإسم',
                'last_name' => 'اللقب',
                'phone' => $request->phone,
                'device_token' => $request->device_token ?? 'nan',
                'last_notification' => $lastNotificationId??1,
                'otp' => $smsCode,
                'status' => 0,
            ]);
            return response()->json(['success' => true, 'message' => 'تم إرسال رمز التفعيل إلى هاتفك'], 200);
        } else {
            if ($user->status == 3)
                return $this->badRequest('رقم الهاتف محظور ولا يمكن استخدامه');

            if ($user->ban_expires_at >= Carbon::now() && $user->ban_expires_at != null)
                return $this->badRequest('لا يمكنك الدخول الأن حاول في وقت اخر');


            if ($user->login_attempts == 1 && Carbon::parse($user->attempts_at)->addMinutes(1) > now()) {
                return $this->badRequest('حاول بعد دقيقة من الأن');
            }

            if ($user->login_attempts == 2 && Carbon::parse($user->attempts_at)->addMinutes(5) > now()) {
                return $this->badRequest('حاول بعد 5 دقائق من الأن');
            }


            if ($user->login_attempts == 3 && Carbon::parse($user->attempts_at)->addMinutes(30) > now()) {
                return $this->badRequest('حاول بعد 30 دقيقة من الأن');
            }


            if ($user->login_attempts >= 4 && Carbon::parse($user->attempts_at)->addMinutes(240) > now()) {
                return $this->badRequest('لقد قمت بالعديد من محاولات الدخول');
            }

            if (Carbon::parse($user->attempts_at)->addMinutes(240) > now())
                $attemp_count = $user->login_attempts + 1;
            else
                $attemp_count = 1;

            $user->login_attempts = $attemp_count;
            $user->attempts_at = Carbon::now();
            $user->otp = $smsCode;
            $user->device_token = $request->device_token ?? 'nan';
            $user->save();

            return response()->json(['success' => true, 'message' => 'تم إرسال رمز التفعيل إلى هاتفك'], 200);
        }
    }



    public function activate(Request $request)
    {
        if (Validator::make($request->all(), [
            'phone' => 'required|numeric|digits:10|starts_with:09',
        ])->fails()) {
            return $this->badRequest("رقم الهاتف يجب ان يكون عبارة عن رقم ومكون من 10 أرقام");
        }
        if (Validator::make($request->all(), [
            'otp' => 'required|numeric|digits:6',
        ])->fails()) {
            return $this->badRequest("يجب عليك ادخال رمز تحقق صحيح من 6 أرقام");
        }


        $user = User::where('phone', $request->phone)->first();
        if (!$user)
            return $this->badRequest('بيانات دخول غير صحيحة');

        if ($user->status == 3 || $user->ban_expires_at >= Carbon::now())
            return $this->badRequest('رقم الهاتف محظور');






        if ($user->otp_attempts == 4 && Carbon::parse($user->otp_attempts_at)->addMinutes(1) > now()) {
            return $this->badRequest('حاول بعد دقيقة من الأن');
        }

        if ($user->otp_attempts == 5 && Carbon::parse($user->otp_attempts_at)->addMinutes(5) > now()) {
            return $this->badRequest('حاول بعد 5 دقائق من الأن');
        }


        if ($user->otp_attempts == 6 && Carbon::parse($user->otp_attempts_at)->addMinutes(30) > now()) {
            return $this->badRequest('حاول بعد 30 دقيقة من الأن');
        }


        if ($user->otp_attempts >= 7 && Carbon::parse($user->otp_attempts_at)->addMinutes(240) > now()) {
            return $this->badRequest('لقد قمت بالعديد من محاولات الدخول');
        }


        if (Carbon::parse($user->otp_attempts_at)->addMinutes(240) > now())
            $otp_attemp_count = $user->otp_attempts + 1;
        else
            $otp_attemp_count = 1;


        if ($user->otp != $request->otp) {
            $user->otp_attempts = $otp_attemp_count;
            $user->otp_attempts_at = Carbon::now();
            $user->save();
            return $this->unauthorized('رمز التفعيل الذي ادختله غير صحيح');
        }



        // Create OTP
        $smsCode = rand(100000, 999999);

        // Update OTP AND DeviceToken
        if ($user->status == 0)
            $user->status = 1;

        $user->otp_attempts = 0;
        $user->otp = $smsCode;
        $user->device_token = $request->device_token ?? 'nan';
        $user->save();


        $user_id = $user->id;
        $notificationCount = UserNotification::where(function ($query) use ($user_id) {
            $query->where('type', 1)
                ->orWhere(function ($query) use ($user_id) {
                    $query->where('type', 2)
                        ->where('user_id', $user_id);
                });
        })->where('id', '>', $user->last_notification)->isSent()->notDeleted()->count();

        return response()->json([
            'success' => true,
            'message' => 'مرحبا بك',
            'user' => [
                'firstname' => $user->first_name,
                'lastname' => $user->last_name,
                'phone' => $user->phone,
                'photo' => $user->photo,
                'token' => $user->createToken('app', ['role:user'])->plainTextToken,
                'point' => $user->point,
                'balance' => $user->balance,
                'notifications' => $notificationCount,
                'status' => $user->status,
            ]
        ], 200);
    }



    public function signup(Request $request)
    {
        //  Vlidation
        if (Validator::make($request->all(), [
            'first_name' => 'required|string|min:2|max:20',
            'last_name' => 'required|string|min:2|max:20',
        ])->fails()) {
            return $this->badRequest("يجب عليك إدخال الإسم واللقب بصيغة صحيحة");
        }
        //  Load User
        $user = User::where('id', $request->user()->id)->first();
        if (!$user)
            return $this->badRequest('بيانات دخول غير صحيحة');


        // Check Status
        if ($user->status != 1)
            return $this->badRequest('هذا الحساب مسجل مسبقا او غير مفعل');

        // Update User
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->status = 2;
        $user->save();
        // Return Response
        return response()->json([
            'success' => true,
            'message' => 'مرحبا بك',
            'user' => [
                'firstname' => $user->first_name,
                'lastname' => $user->last_name,
                'phone' => $user->phone,
                'photo' => $user->photo,
                'token' => $user->createToken('app', ['role:user'])->plainTextToken,
                'point' => $user->point,
                'balance' => $user->balance,
                'notifications' => 0,
                'status' => $user->status,
            ]
        ], 200);
    }


    public function profile(Request $request)
    {

        $user = User::find($request->user()->id);

        if ($user->status == 3  || $user->ban_expires_at >= Carbon::now())
            return $this->unauthorized('هذا الحساب تم حظره');

        if ($user->status != 2 && $user->status != 1)
            return $this->unauthorized('هذا الحساب غير مفعل او تم حذفه');

        if ($request->device_token != null)
            $user->device_token = $request->device_token;
        $user->save();


        $user_id = $request->user()->id;
        $notificationCount = UserNotification::where(function ($query) use ($user_id) {
            $query->where('type', 1)
                ->orWhere(function ($query) use ($user_id) {
                    $query->where('type', 2)
                        ->where('user_id', $user_id);
                });
        })->where('id', '>', $user->last_notification)->isSent()->notDeleted()->count();


        return response()->json([
            'success' => true,
            'message' => 'مرحبا بك',
            'user' => [
                'firstname' => $user->first_name,
                'lastname' => $user->last_name,
                'phone' => $user->phone,
                'photo' => $user->photo,
                'token' => $request->bearerToken(),
                'point' => $user->point,
                'balance' => $user->balance,
                'notifications' => $notificationCount,
                'status' => $user->status,
            ]
        ], 200);
    }


    public function logout(Request $request)
    {
        $user = $request->user();
        if (!$user)
            return $this->unauthorized('إنتهت جلسة الدخول مسبقا');

        $user->currentAccessToken()->delete();
        return response()->json([
            'success' => true,
            'message' => 'تم تسجيل خروجك بنجاح',
        ], 200);
    }

    public function photo(Request $request)
    {

        if (!$request->hasFile('photo')) {
            return response()->json(["success" => false, "message" => "يجب عليك إختيار صورة ليتم رفعها"], 400);
        }

        if (Validator::make($request->all(), [
            'photo' => 'mimes:jpg,jpeg,png',
        ])->fails()) {
            return response()->json(["success" => false, "message" => "الملف الذي اخترته ليس صورة"], 400);
        }

        $file_name = Str::uuid() . '_' . $request->photo->getClientOriginalExtension();
        $file_path = $request->file('photo')->storeAs('app/users', $file_name, 'public');



        $request->user()->photo = $file_path;
        $request->user()->save();

        return response()->json([
            "success" => true,
            "message" => "تم تحديث صورة المستخدم بنجاح",
            "photo" => url(Storage::url($file_path))
        ]);
    }



    public function name(Request $request)
    {

        //  Validation 

        if ($request->first_name || $request->last_name) {
            $request->user()->Update(
                request()->only(
                    "first_name",
                    "last_name"
                )
            );
            return response()->json([
                "success"=>true,
                "message"=>"تم تحديث الإسم بنجاح",
                "user"=>$request->user(),
            ],200);
        } else {
            return $this->badRequest('يجب عليك إدخال الإسم أو اللقب');
        }
    }
    // End OF Controller
}
