<?php

namespace App\Features\Admin\v1\Controllers;

use App\Features\Admin\Requests\PermissionStoreRequest;
use App\Http\Controllers\Controller;
use App\Models\Permission;
use GrahamCampbell\ResultType\Success;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Features\Admin\Resources\PermissionResource;
use App\Features\Admin\v1\Models\Role;

use function PHPUnit\Framework\isEmpty;

class RoleController  extends Controller
{


    // All Permission
    public function index(Request $request)
    {
        if ($request->count)
            $count = $request->count;
        else
            $count = 10;

        $permissions = Role::latest()
        ->notDeleted()
            ->where('name', 'like', '%' . $request->name . '%')->reorder()->orderBy('id', 'asc')
            ->withCount('admins')
            ->paginate($count);
        if ($permissions->isEmpty())
            return response()->json(['success' => false, 'message' => 'لا يوجد اي ادوار في الموقع', 'data' => $permissions], 204);
        return response()->json(['success' => true, 'message' => 'تم جلب  الأدوار بنجاح', 'data' => $permissions], 200);
    }

    // Get Permissions By Id
    public function show($role)
    {
        $permission = Role::withCount('admins')->where('id', $role)->notDeleted()->first();
        if (!$permission)
            return response()->json(['success' => false, 'message' => 'هذه الدور غير موجود'], 204);
        return response()->json(['success' => true, 'message' => 'تم جلب الدور بنجاح', 'data' => new \App\Features\Admin\v1\Resources\PermissionResource($permission)], 200);

    }


    // Delete Permission
    public function delete($role)
    {
        $permission = Role::where('id', $role)->withCount('admins')->notDeleted()->first();
        if (!$permission)
            return response()->json(['success' => false, 'message' => 'هذه الدور غير موجود'], 204);

        if ($permission->admins_count > 0)
            return response()->json(['success' => false, 'message' => 'هذه الدور لا يمكن حذفه لانه يحتوى على مشرفين'], 400);

        $permission->status = 9;
        $permission->timestamps = false;
        $edit = $permission->save();
        if ($edit)
            return response()->json(['success' => true, 'message' => 'تم حذف هذا الدور بنجاح'], 200);
        return response()->json(['success' => true, 'message' => 'حدث خطأ ما'], 400);
    }


    // Edit Single Role
    public function edit(Request $request, $role)
    {
        // Check If Role Exist Or Not
        $role = Role::where('id', $role)->withCount('admins')->notDeleted()->first();


        if (!$role)
            return response()->json(['success' => false, 'message' => 'هذه الدور غير موجود'], 204);

        // Validate Name
        if (
            Validator::make($request->all(), [
                'name' => 'required',
            ])->fails()
        ) {
            return response()->json(["success" => false, "message" => "يجب عليك ادخال اسم الدور"], 400);
        }
        if (strtolower($request['name']) != strtolower($role->name)) {
            if (
                Validator::make($request->all(), [
                    'name' => 'unique:roles',
                ])->fails()
            ) {
                return response()->json(["success" => false, "message" => "يوجد دور بهذا الإسم"], 400);
            }
        }


        // Validate Permissions 
        if (
            Validator::make($request->all(), [
                'permissions' => 'required|min:1',
            ])->fails()
        ) {
            return response()->json(["success" => false, "message" => "يجب عليك ادخال صلاحية واحدة على الأقل"], 400);
        }
        if (
            Validator::make($request->all(), [
                'permissions' => 'array',
            ])->fails()
        ) {
            return response()->json(["success" => false, "message" => "نوع الصلاحية غير صحيح"], 400);
        }

        $role->name = $request['name'];
        $role->permissions = json_encode($request['permissions']);
        $role->timestamps = false;
        $edit = $role->save();
        if ($edit)
            return response()->json(['success' => true, 'message' => 'تم تحديث هذا الدور بنجاح'], 200);
        return response()->json(['success' => true, 'message' => 'حدث خطأ ما'], 400);
    }


    // Get All Permissions
    public function new(Request $request)
    {
        $permissions = [];
        foreach (config('permissions.permissions') as $name => $value) {
            array_push($permissions, ["name" => $name, "description" => $value, "state" => false]);
        }
        return response()->json([
            "success" => true,
            "message" => "تم جلب جميع الصلاحيات بنجاح",
            "data" => [
                "name" => "",
                "permissions" => $permissions,
            ]
        ]);
    }



    // Add New Role
    public function create(\App\Features\Admin\v1\Requests\PermissionStoreRequest $request)
    {
        $permission = Role::create([
            'name' => $request['name'],
            'permissions' => json_encode($request['permissions']),

        ]);
        return response()->json(['success' => true, 'message' => 'تم إنشاء الصلاحية بنجاح'], 200);
    }


}