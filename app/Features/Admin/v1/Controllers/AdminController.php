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
        return response()->json(['success' => true, 'message' => 'تم جلب البيانات بنجاح', 'roles' => $roles], 200);
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




        $role = Role::where('id', $request['role_id'])->notDeleted()->first();
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



    // Get Admin By Id With Permseeions
    public function showWithRoles($admin)
    {

        $admin = Admin::with('role:id,name')->where('id', $admin)->notDeleted()->first();
        if (!$admin)
            return response()->json(['success' => false, 'message' => 'هذه الحساب غير موجود'], 204);


        $roles = Role::select('id', 'name')->notDeleted()->get();
        if (count($roles) == 0)
            return response()->json([], 204);


        return response()->json(['success' => true, 'message' => 'تم جلب المشرف بنجاح', 'data' => $admin, 'roles' => $roles], 200);
    }


    //  Change Admin Role
    public function edit($admin, Request $request)
    {
        $admin = Admin::with('role:id,name')->where('id', $admin)->notDeleted()->first();
        if (!$admin)
            return response()->json(['success' => false, 'message' => 'هذه الحساب غير موجود'], 400);

        if (
            Validator::make($request->all(), [
                'role_id' => 'required',
            ])->fails()
        ) {
            return response()->json(["success" => false, "message" => "يجب عليك إرسال رقم الدور"], 400);
        }

        $role = Role::where('id', $request['role_id'])->notDeleted()->first();
        if (!$role)
            return response()->json(['success' => false, 'message' => 'هذا الدور لم يعد متاح قم بإختيار دور اخر'], 400);

        $admin->role_id = $request['role_id'];
        $edit = $admin->save();
        if ($edit)
            return response()->json(['success' => true, 'message' => 'تم تحديث دور الحساب بنجاح'], 200);
        return response()->json(['success' => true, 'message' => 'حدث خطأ ما'], 400);
    }



    // Delete Admin
    public function delete($admin)
    {
        $admin = Admin::where('id', $admin)->notDeleted()->first();
        if (!$admin)
            return response()->json(['success' => false, 'message' => 'هذه الحساب غير موجود'], 204);


        $admin->phone = 'old-'.$admin->phone;
        $admin->status = 9;
        $edit = $admin->save();
        if ($edit)
            return response()->json(['success' => true, 'message' => 'تم حذف هذا الحساب بنجاح'], 200);
        return response()->json(['success' => true, 'message' => 'حدث خطأ ما'], 400);
    }


        // Activate Admin
        public function active($admin)
        {
            $admin = Admin::where('id', $admin)->notDeleted()->first();
            if (!$admin)
                return response()->json(['success' => false, 'message' => 'هذه الحساب غير موجود'], 204);
    
            if ($admin->status == 1)
                return response()->json(['success' => false, 'message' => 'هذا الحساب مفعل مسبقا'], 400);
    
            if ($admin->status == 2)
                return response()->json(['success' => false, 'message' => 'هذا الحساب محظور ولا يمكن تفعيله'], 400);
            $admin->status = 1;
            $edit = $admin->save();
            if ($edit)
                return response()->json(['success' => true, 'message' => 'تم تفعيل هذا الحساب'], 200);
            return response()->json(['success' => true, 'message' => 'حدث خطأ ما'], 400);
        }
    
    
        // DisActivate Admin
        public function disActive($admin)
        {
            $admin = Admin::where('id', $admin)->notDeleted()->first();
            if (!$admin)
                return response()->json(['success' => false, 'message' => 'هذه الحساب غير موجود'], 204);
    
            if ($admin->status == 0)
                return response()->json(['success' => false, 'message' => 'هذا الحساب غير مفعل مسبقا'], 400);
    
            if ($admin->status == 2)
                return response()->json(['success' => false, 'message' => 'هذا الحساب محظور ولا يمكن تفعيله'], 400);
    
            $admin->status = 0;
            $edit = $admin->save();
            if ($edit)
                return response()->json(['success' => true, 'message' => 'تم إلغاء تفعيل هذا الحساب'], 200);
            return response()->json(['success' => true, 'message' => 'حدث خطأ ما'], 400);
        }
    
    
        // Banned Admin
        public function banned($admin)
        {
            $admin = Admin::where('id', $admin)->notDeleted()->first();
            if (!$admin)
                return response()->json(['success' => false, 'message' => 'هذه الحساب غير موجود'], 204);
    
            if ($admin->status == 2)
                return response()->json(['success' => false, 'message' => 'هذا الحساب محظور مسبقا'], 400);
    
            $admin->status = 2;
            $edit = $admin->save();
            if ($edit)
                return response()->json(['success' => true, 'message' => 'تم حظر هذا الحساب ولا يمكنم استخدامه مجددا'], 200);
            return response()->json(['success' => true, 'message' => 'حدث خطأ ما'], 400);
        }
    
    // Banned Admin
    public function resetPassword($admin)
    {
        $admin = Admin::where('id', $admin)->notDeleted()->first();
        if (!$admin)
            return response()->json(['success' => false, 'message' => 'هذه الحساب غير موجود'], 204);

        if ($admin->status == 2)
            return response()->json(['success' => false, 'message' => 'هذا الحساب محظور'], 400);

        $admin->password = Hash::make("123456");
        $edit = $admin->save();
        if ($edit)
            return response()->json(['success' => true, 'message' => 'تم تغيير كلمة المرور إلى 123456 , يجب عليك تغيير كلمة المرور بمجرد تسجيل دخول إلى الحساب'], 200);
        return response()->json(['success' => true, 'message' => 'حدث خطأ ما'], 400);
    }
        

    // End of Controller
}
