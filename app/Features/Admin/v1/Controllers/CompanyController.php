<?php

namespace App\Features\Admin\v1\Controllers;

use App\Features\Admin\v1\Models\Admin;
use App\Features\Admin\v1\Models\Company;
use App\Features\Admin\v1\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class CompanyController extends Controller
{

    // Admin List
    public function index(Request $request)
    {
        if ($request->count)
            $count = $request->count;
        else
            $count = 10;

        $companies = Company::latest()
            ->where('name', 'like', '%' . $request->name . '%')
            ->where('phone', 'like', '%' . $request->phone . '%')
            ->notDeleted()
            ->paginate($count);

        if ($companies->isEmpty())
            return $this->empty();
        return response()->json(['success' => true, 'message' => 'تم جلب  الشركات بنجاح', 'data' => $companies], 200);
    }


    // Get Admin By Id
    public function show(Request $request, $company)
    {
        $company = Company::where('id', $company)->notDeleted()->first();
        if (!$company)
            return $this->empty();
        return response()->json(['success' => true, 'message' => 'تم جلب بيانات الشركة بنجاح', 'data' => $company], 200);
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

        if (!$request->hasFile('logo')) {
            return $this->badRequest("يجب عليك إختيار صورة ليتم رفعها");
        }
        if (Validator::make($request->all(), [
            'logo' => 'mimes:jpg,jpeg,png',
        ])->fails()) {
            return response()->json(["success" => false, "message" => "الملف الذي اخترته ليس صورة"], 400);
        }


        if (!$request->hasFile('background')) {
            return $this->badRequest("يجب عليك إختيار خلفية ليتم رفعها");
        }
        if (Validator::make($request->all(), [
            'background' => 'mimes:jpg,jpeg,png',
        ])->fails()) {
            return response()->json(["success" => false, "message" => "الخلفية التي اخترتها ليس صورة"], 400);
        }



        $file_name = Str::uuid() . '.' . $request->logo->getClientOriginalExtension();
        $file_path = $request->file('logo')->storeAs('companies', $file_name, 'public');



        $file_name_background = Str::uuid() . '.' . $request->background->getClientOriginalExtension();
        $file_path_background = $request->file('background')->storeAs('companies_background', $file_name_background, 'public');

        $company = Company::create([
            'name' => $request['name'],
            'description' => $request['description'],
            'address' => $request['address'],
            'logo' => $file_path,
            'background' => $file_path_background,
            'phone' => $request['phone'],
            'cities' => $request['cities'],
            'longitude' => $request['longitude'],
            'latitude' => $request['latitude'],
            'move_price' => $request['move_price'],
            'join_price' => $request['join_price'],
            'is_in_store' => $request['is_in_store'],
            'is_in_map' => $request['is_in_map'],
            'is_have_account' => $request['is_have_account'],
            'system_type' => $request['system_type'],
            'website' => $request['website'],
            'email' => $request['email'],
            'support_phone' => $request['support_phone'],
        ]);
        return response()->json(['success' => true, 'message' => 'تم إضافة الشركة بنجاح'], 200);
    }



    //  Change Company
    public function edit($company, Request $request)
    {
        $company = Company::where('id', $company)->notDeleted()->first();
        if (!$company)
            return response()->json(['success' => false, 'message' => 'هذه الشركة غير متوفرة'], 400);

            if($request->file('image')){
                $file_name = Str::uuid() . '.' . $request->image->getClientOriginalExtension();
                $file_path = $request->file('image')->storeAs('companies', $file_name, 'public');
                $request['logo']=$file_path;
            }

            if($request->file('image_background')){
                $file_name_background = Str::uuid() . '.' . $request->image_background->getClientOriginalExtension();
                $file_path_background = $request->file('image_background')->storeAs('companies_background', $file_name_background, 'public');
                $request['background']=$file_path_background;
            }

          $edit=  $company->Update(
                request()->only(
                    'name',
                    'description',
                    'address',
                    'phone',
                    'logo',
                    'background',
                    'is_in_store',
                    'is_in_map',
                    'is_have_account',
                    'system_type',
                    'website',
                    'email',
                    'support_phone',
                )
            );
            $company->save();
        if ($edit)
            return response()->json(['success' => true, 'message' => 'تم تحديث بيانات الشركة بنجاح','company'=>$company], 200);
        return response()->json(['success' => true, 'message' => 'حدث خطأ ما'], 400);
    }



    





    // Delete Company
    public function delete($company)
    {
        $company = Company::where('id', $company)->notDeleted()->first();
        if (!$company)
            return response()->json(['success' => false, 'message' => 'هذه الشركة غير موجود'], 204);


        $company->status = 9;
        $edit = $company->save();
        if ($edit)
            return response()->json(['success' => true, 'message' => 'تم حذف هذه الشركة بنجاح'], 200);
        return response()->json(['success' => true, 'message' => 'حدث خطأ ما'], 400);
    }





    // End of Controller
}
