<?php

namespace App\Features\Admin\v1\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckAdminRoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next,$roleName)
    {
        foreach (json_decode($request->user()->role->permissions) as $permission) {
            if (is_array($roleName) && in_array($permission, $roleName)) {
                return $next($request);
            } else if (is_string($roleName) && strcmp($roleName, $permission) == 0) {
                return $next($request);
            }
        }
        return response()->json(["success"=>false,"message"=>"ليس لديك الصلاحية للقيام بهذه العملية"],403);

    }
}
