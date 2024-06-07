<?php

namespace App\Features\Admin\v1\Controllers;

use App\Features\Admin\v1\Models\Admin;
use App\Features\Admin\v1\Models\Role;
use App\Features\Admin\v1\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{

    // User List
    public function index(Request $request)
    {
        if ($request->count)
            $count = $request->count;
        else
            $count = 10;

        if ($request->status != "") {

            $admins = User::latest()
                ->where('status', $request->status)
                ->where('phone', 'like', '%' . $request->phone . '%')
                ->where('first_name', 'like', '%' . $request->first_name . '%')
                ->where('last_name', 'like', '%' . $request->last_name . '%')
                ->notDeleted()
                ->paginate($count);
        } else {
            $admins = User::latest()
                ->where('phone', 'like', '%' . $request->phone . '%')
                ->where('first_name', 'like', '%' . $request->first_name . '%')
                ->where('last_name', 'like', '%' . $request->last_name . '%')
                ->notDeleted()
                ->paginate($count);
        }

        if ($admins->isEmpty())
            return $this->empty();
        return response()->json(['success' => true, 'message' => 'تم جلب  المستخدمين بنجاح', 'data' => $admins], 200);
    }


    // Get User By Id
    public function show(Request $request, $user)
    {
        $user = User::where('id', $user)->notDeleted()->first();
        if (!$user)
            return $this->empty();
        return response()->json(['success' => true, 'message' => 'تم جلب المستخدم بنجاح', 'data' => $user], 200);
    }





    // Banned User
    public function banned($user)
    {
        $user = User::where('id', $user)->notDeleted()->first();
        if (!$user)
            return response()->json(['success' => false, 'message' => 'هذه المستخدم غير موجود'], 204);

        if ($user->status == 3)
            return response()->json(['success' => false, 'message' => 'هذا المستخدم محظور مسبقا'], 400);

        $user->status = 3;
        $edit = $user->save();
        if ($edit)
            return response()->json(['success' => true, 'message' => 'تم حظر هذا المستخدم ولا يمكنم استخدامه مجددا'], 200);
        return response()->json(['success' => true, 'message' => 'حدث خطأ ما'], 400);
    }


        // Delete User
        public function delete($user)
        {
            $user = User::where('id', $user)->notDeleted()->first();
            if (!$user)
                return response()->json(['success' => false, 'message' => 'هذه الحساب غير موجود'], 204);
    
    
            $user->phone = 'old-' . $user->phone;
            $user->status = 9;
            $edit = $user->save();
            if ($edit)
                return response()->json(['success' => true, 'message' => 'تم حذف هذا الحساب بنجاح'], 200);
            return response()->json(['success' => true, 'message' => 'حدث خطأ ما'], 400);
        }






    // End of Controller
}
