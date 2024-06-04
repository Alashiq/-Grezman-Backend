<?php

namespace App\Features\Admin\v1\Controllers;

use App\Features\Admin\v1\Models\Admin;
use App\Features\Admin\v1\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

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
    public function show(Request $request, $admin)
    {
        $admin = Admin::with('role:id,name')
            ->where('id', '<>', $request->user()->id)
            ->where('id', $admin)->notDeleted()->first();
        if (!$admin)
            return $this->empty();
        return response()->json(['success' => true, 'message' => 'تم جلب المشرف بنجاح', 'data' => $admin], 200);
    }



    // Data For New
    public function new()
    {
        $roles = Role::latest()->notDeleted()->get();
        return response()->json(['success' => true, 'message' => 'تم جلب البيانات بنجاح','roles'=>$roles], 200);
    }





    // Add New Admin
    public function store(Request $request)
    {
        if (Validator::make($request->all(), [
            'phone' => 'unique:admins',
        ])->fails()) {
            return response()->json(["success" => false, "message" => "رقم الهاتف محجوز مسبقا"], 400);
        }

        if (Validator::make($request->all(), [
            'first_name' => 'required|string|min:2|max:25',
        ])->fails()) {
            return $this->badRequest("يجب عليك إدخال الإسم");
        }
        if (Validator::make($request->all(), [
            'last_name' => 'required|string|min:2|max:25',
        ])->fails()) {
            return $this->badRequest("يجب عليك إدخال اللقب");
        }

        if (Validator::make($request->all(), [
            'password' => 'required|string|min:6|max:25',
        ])->fails()) {
            return $this->badRequest("يجب عليك إدخال كلمة مرور صحيحة");
        }




        $role = Role::where('id', $request['role_id'])->where('status', '<>', 9)->first();
        if (!$role)
            return response()->json(['success' => false, 'message' => 'دور المشرف غير متاح'], 400);


        $admin = Admin::create([
            'first_name' => $request['first_name'],
            'last_name' => $request['last_name'],
            'phone' => $request['phone'],
            'role_id' => $request['role_id'],
            'password' => Hash::make($request['password']),
        ]);
        return response()->json(['success' => true, 'message' => 'تم إنشاء هذا الحساب بنجاح'], 200);
    }


    // End of Controller
}
