<?php

namespace App\Features\App\v1\Middleware;

use Illuminate\Support\Facades\Auth;

use Closure;
use Illuminate\Http\Request;

class AuthAppMiddleware
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

    //  return $request->user()->tokenCan('role:user');
        if ($request->user()->tokenCan('role:user')) {
            if ($request->user()->status == 3)
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
