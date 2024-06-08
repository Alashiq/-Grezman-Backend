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




    // Data For New
    public function new()
    {
        return response()->json(['success' => true, 'message' => 'تم جلب البيانات بنجاح'], 200);
    }


    // Add New Notification
    public function store(Request $request)
    {
        if (Validator::make($request->all(), [
            'title' => 'required|string|min:3|max:30',
        ])->fails()) {
            return $this->badRequest("يجب عليك إدخال عنوان بين 3 و 30 حرف");
        }
        if (Validator::make($request->all(), [
            'message' => 'required|string|min:4',
        ])->fails()) {
            return $this->badRequest("يجب عليك إدخال محتوى اكثر من 4 أحرف");
        }

        if (Validator::make($request->all(), [
            'send_time' => 'required|date|min:3|max:30',
        ])->fails()) {
            return $this->badRequest("يجب عليك إدخال وقت وتاريخ الإرسال بشكل صحيح");
        }


        if ($request['type'] != 1 && $request['type'] != 2)
            return $this->badRequest("يجب عليك تحديد نوع الإشعار بشكل صحيح");

        if ($request['type'] == 2 && $request['user_id'] == null)
            return $this->badRequest("يجب عليك تحديد مستلم الإشعار");

        $user = User::where('id', $request['user_id']??0)->notDeleted()->first();
        if (!$user && $request['type'] == 2)
            return $this->badRequest("لم نتمكن من تحديد المستخدم");


        $newItem = UserNotification::create([
            'title' => $request['title'],
            'content' => $request['message'],
            'type' => $request['type'],
            'user_id' => $request['type'] == 2 ? $request['user_id'] : null,
            'is_sent' => 0,
            'send_time' => $request['send_time'],

        ]);
        if ($newItem)
            return response()->json(['success' => true, 'message' => 'تم إنشاء هذا الإشعار بنجاح'], 200);
        else
            return $this->badRequest('حدث خطأ ما حاول مجددا');
    }



    // End of Controller
}
