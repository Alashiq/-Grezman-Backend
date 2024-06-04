<?php

namespace App\Features\Admin\v1\Controllers;

use App\Features\Admin\v1\Models\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminController extends Controller
{

    // Admin List
    public function index(Request $request)
    {
        if ($request->count)
            $count = $request->count;
        else
            $count = 10;

            if ($request->status != "") {

            $admins = Admin::latest()
                ->where('status', $request->status)
                ->where('id', '<>', $request->user()->id)
                ->where('phone', 'like', '%' . $request->phone . '%')
                ->where('first_name', 'like', '%' . $request->first_name . '%')
                ->where('last_name', 'like', '%' . $request->last_name . '%')
                ->notDeleted()
                ->paginate($count);
        } else {
            $admins = Admin::latest()
                ->where('id', '<>', $request->user()->id)
                ->where('phone', 'like', '%' . $request->phone . '%')
                ->where('first_name', 'like', '%' . $request->first_name . '%')
                ->where('last_name', 'like', '%' . $request->last_name . '%')
                ->notDeleted()
                ->paginate($count);
        }

        if ($admins->isEmpty())
            return $this->empty();
        return response()->json(['success' => true, 'message' => 'تم جلب  المشرفين بنجاح', 'data' => $admins], 200);
    }


        // Get Admin By Id
        public function show(Request $request,$admin)
        {
            $admin = Admin::with('role:id,name')
            ->where('id', '<>', $request->user()->id)
            ->where('id', $admin)->notDeleted()->first();
            if (!$admin)
            return $this->empty();
            return response()->json(['success' => true, 'message' => 'تم جلب المشرف بنجاح', 'data' => $admin], 200);
        }




    // End of Controller
}
