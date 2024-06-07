<?php

namespace App\Features\Admin\v1\Controllers;

use App\Features\Admin\v1\Models\Admin;
use App\Features\Admin\v1\Models\Role;
use App\Features\Admin\v1\Models\User;
use App\Features\Admin\v1\Models\UserNotification;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserNotificationController extends Controller
{

    // User List
    public function index(Request $request)
    {
        if ($request->count)
            $count = $request->count;
        else
            $count = 10;

        if ($request->user_id != "") {

            $admins = UserNotification::latest()
                ->where('title', 'like', '%' . $request->title . '%')
                ->where('type', 'like', '%' . $request->type . '%')
                ->where('is_sent', 'like', '%' . $request->is_sent . '%')
                ->where('user_id', $request->user_id)
                ->notDeleted()
                ->paginate($count);
        } else {
            $admins = UserNotification::latest()
                ->where('title', 'like', '%' . $request->title . '%')
                ->where('type', 'like', '%' . $request->type . '%')
                ->where('is_sent', 'like', '%' . $request->is_sent . '%')
                ->notDeleted()
                ->paginate($count);
        }

        if ($admins->isEmpty())
            return $this->empty();
        return response()->json(['success' => true, 'message' => 'تم جلب  إشعارات المستخدمين بنجاح', 'data' => $admins], 200);
    }


    // Get User By Id
    public function show(Request $request, $notification)
    {
        $notification = UserNotification::where('id', $notification)->notDeleted()->first();
        if (!$notification)
            return $this->empty();
        return response()->json(['success' => true, 'message' => 'تم جلب الإشعار بنجاح', 'data' => $notification], 200);
    }




    // Delete User
    public function delete($notification)
    {
        $notification = UserNotification::where('id', $notification)->notDeleted()->first();
        if (!$notification)
            return response()->json(['success' => false, 'message' => 'هذا الإشعار غير موجود'], 204);


        $notification->is_sent = 0;
        $notification->status = 9;
        $edit = $notification->save();
        if ($edit)
            return response()->json(['success' => true, 'message' => 'تم حذف هذا الإشعار بنجاح'], 200);
        return response()->json(['success' => true, 'message' => 'حدث خطأ ما'], 400);
    }






    // End of Controller
}
