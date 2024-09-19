<?php

namespace App\Features\Admin\v1\Controllers;

use App\Features\Admin\v1\Models\Admin;
use App\Features\Admin\v1\Models\Banner;
use App\Features\Admin\v1\Models\Company;
use App\Features\Admin\v1\Models\Join;
use App\Features\Admin\v1\Models\Role;
use App\Features\Admin\v1\Models\Tower;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class   BannerController extends Controller
{

    // Admin List
    public function index(Request $request)
    {
        if ($request->count)
            $count = $request->count;
        else
            $count = 10;

        $companies = Banner::latest()
            ->where('name', 'like', '%' . $request->name . '%')
            ->where('is_active', 'like', '%' . $request->is_active . '%')
            ->notDeleted()
            ->paginate($count);

        if ($companies->isEmpty())
            return $this->empty();
        return response()->json(['success' => true, 'message' => 'تم جلب  البنرات بنجاح', 'data' => $companies], 200);
    }


    // Get Tower By Id
    public function show(Request $request, $banner)
    {
        $banner = banner::where('id', $banner)->notDeleted()->first();
        if (!$banner)
            return $this->empty();
        return response()->json(['success' => true, 'message' => 'تم جلب بيانات البانر بنجاح', 'data' => $banner], 200);
    }



    // Data For New
    public function new()
    {
        return response()->json(['success' => true, 'message' => 'تم جلب البيانات بنجاح'], 200);
    }





    // Add New Admin
    public function store(Request $request)
    {

        if (Validator::make($request->all(), [
            'name' => 'required|string|min:2|max:25',
        ])->fails()) {
            return $this->badRequest("يجب عليك إدخال الإسم");
        }

        if (!$request->hasFile('image')) {
            return $this->badRequest("يجب عليك إختيار صورة ليتم رفعها");
        }
        if (Validator::make($request->all(), [
            'image' => 'mimes:jpg,jpeg,png',
        ])->fails()) {
            return response()->json(["success" => false, "message" => "الملف الذي اخترته ليس صورة"], 400);
        }



        $file_name = Str::uuid() . '_' . $request->image->getClientOriginalExtension();
        $file_path = $request->file('image')->storeAs('banners', $file_name, 'public');

        $banner = Banner::create([
            'name' => $request['name'],
            'rank' => $request['rank'],
            'is_active' => $request['is_active'],
            'image' => $file_path,
        ]);
        return response()->json(['success' => true, 'message' => 'تم إضافة البانر بنجاح'], 200);
    }




    // Get Banner By Id
    public function showForEdit(Request $request, $banner)
    {
        $banner = Banner::where('id', $banner)->notDeleted()->first();
        if (!$banner)
            return $this->empty();



        return response()->json(['success' => true, 'message' => 'تم جلب بيانات البرج بنجاح', 'data' => $banner], 200);
    }


    

    //  Change Banner
    public function edit($banner, Request $request)
    {
        $banner = Banner::where('id', $banner)->notDeleted()->first();
        if (!$banner)
            return response()->json(['success' => false, 'message' => 'هذه البرج غير متوفرة'], 400);



        if ($request->hasFile('image_file') && Validator::make($request->all(), [
            'image_file' => 'mimes:jpg,jpeg,png',
        ])->fails()) {
            return response()->json(["success" => false, "message" => "الملف الذي اخترته ليس صورة"], 400);
        }

        

        if ($request->hasFile('image_file')) {
            $file_name = Str::uuid() . '_.' . $request->image_file->getClientOriginalExtension();
            $request['image'] = $request->file('image_file')->storeAs('towers', $file_name, 'public');
        }

        $edit =  $banner->Update(
            request()->only(
                'name',
                'image',
                'rank',
                'is_active',
            )
        );
        $banner->save();
        if ($edit)
            return response()->json(['success' => true, 'message' => 'تم تحديث بيانات البانر بنجاح', 'banner' => $banner], 200);
        return response()->json(['success' => true, 'message' => 'حدث خطأ ما'], 400);
    }









    





    // Delete Tower
    public function delete($banner)
    {
        $banner = Banner::where('id', $banner)->notDeleted()->first();
        if (!$banner)
            return response()->json(['success' => false, 'message' => 'هذه البانر غير موجود'], 204);


        $banner->status = 9;
        $edit = $banner->save();
        if ($edit)
            return response()->json(['success' => true, 'message' => 'تم حذف هذا البانر بنجاح'], 200);
        return response()->json(['success' => true, 'message' => 'حدث خطأ ما'], 400);
    }





    // End of Controller
}
