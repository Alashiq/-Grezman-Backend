<?php

namespace App\Features\Admin\v1\Middleware;

use Illuminate\Support\Facades\Auth;

use Closure;
use Illuminate\Http\Request;

class AuthAdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->user()->tokenCan('role:admin')) {
            if ($request->user()->status == 0)
                return response()->json([
                    "success" => false,
                    "message" => "حسابك غير مفعل يجب عليك التواصل مع المسؤول لتفعيله"
                ], 401);
            if ($request->user()->status == 2)
                return response()->json([
                    "success" => false,
                    "message" => "تم حظر حسابك ولم بعد بإمكانك استخدامه"
                ], 401);
                if ($request->user()->status == 9)
                return response()->json([
                    "success" => false,
                    "message" => "لم يعد هذا الحساب متوفرا"
                ], 401);
            return $next($request);
        }

        return response()->json([
            "success" => false,
            "message" => "انتهت الجلسة ا لخاصة بك, يجب عليك إعادة تسجيل الدخول مجددا"
        ], 401);
    }
}
