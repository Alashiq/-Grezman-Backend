<?php

namespace App\Features\App\v1\Controllers;

use App\Features\App\v1\Models\Item;
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

class UserNotificationController extends Controller
{

    public function index(Request $request)
    {
        if ($request->count)
            $count = $request->count;
        else
            $count = 10;

        $user_id = $request->user()->id;
        $list = UserNotification::latest()->where(function ($query) use ($user_id) {
            $query->where('type', 1)
                ->orWhere(function ($query) use ($user_id) {
                    $query->where('type', 2)
                        ->where('user_id', $user_id);
                });
        })->where('created_at', '>', $request->user()->created_at)->isSent()->notDeleted()->paginate($count);


        if ($list->isEmpty())
            return $this->empty();

        $lastNotificationId = $list->first()->id;
        $request->user()->update(['last_notification' => $lastNotificationId]);

        return response()->json(['success' => true, 'message' => 'تم جلب  الإشعارات بنجاح', 'data' => $list], 200);
    }



    // End OF Controller
}
