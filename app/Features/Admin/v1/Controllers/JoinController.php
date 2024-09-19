<?php

namespace App\Features\Admin\v1\Controllers;

use App\Features\Admin\v1\Models\Admin;
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

class JoinController extends Controller
{

    // Admin List
    public function index(Request $request)
    {
        if ($request->count)
            $count = $request->count;
        else
            $count = 10;

        $companies = Join::latest()
        ->with('company')
        ->with('user')
            ->where('name', 'like', '%' . $request->name . '%')
            ->where('is_sloved', 'like', '%' . $request->is_sloved . '%')
            ->when($request->company_id, function ($query) use ($request) {
                $query->where('company_id', $request->company_id);
            })
            ->notDeleted()
            ->paginate($count);

        if ($companies->isEmpty())
            return $this->empty();
        return response()->json(['success' => true, 'message' => 'تم جلب  طلبات الإشتراك بنجاح', 'data' => $companies], 200);
    }


    // Get Tower By Id
    public function show(Request $request, $tower)
    {
        $tower = Tower::with('company')->where('id', $tower)->notDeleted()->first();
        if (!$tower)
            return $this->empty();
        return response()->json(['success' => true, 'message' => 'تم جلب بيانات البرج بنجاح', 'data' => $tower], 200);
    }



    // Data For New
    public function new()
    {
        $companies = Company::notDeleted()->where('is_in_map',true)->get();
        return response()->json(['success' => true, 'message' => 'تم جلب البيانات بنجاح', 'companies' => $companies], 200);
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
        $file_path = $request->file('image')->storeAs('towers', $file_name, 'public');

        $company = Tower::create([
            'name' => $request['name'],
            'city' => $request['city'],
            'town' => $request['town'],
            'address' => $request['address'],
            'longitude' => $request['longitude'],
            'latitude' => $request['latitude'],
            'description' => $request['description'],
            'company_id' => $request['company_id'],
            'is_active' => $request['is_active'],
            'image' => $file_path,
        ]);
        return response()->json(['success' => true, 'message' => 'تم إضافة البرج بنجاح'], 200);
    }




    // Get Tower By Id
    public function showForEdit(Request $request, $tower)
    {
        $tower = Tower::with('company:id,name')->where('id', $tower)->notDeleted()->first();
        if (!$tower)
            return $this->empty();


            $companies = Company::select('id','name')->notDeleted()->where('is_in_map',true)->get();

        return response()->json(['success' => true, 'message' => 'تم جلب بيانات البرج بنجاح', 'data' => $tower, 'companies' => $companies], 200);
    }


    

    //  Change Tower
    public function edit($tower, Request $request)
    {
        $tower = Tower::where('id', $tower)->notDeleted()->first();
        if (!$tower)
            return response()->json(['success' => false, 'message' => 'هذه البرج غير متوفرة'], 400);



        if ($request->hasFile('image_file') && Validator::make($request->all(), [
            'image_file' => 'mimes:jpg,jpeg,png',
        ])->fails()) {
            return response()->json(["success" => false, "message" => "الملف الذي اخترته ليس صورة"], 400);
        }

        $company = Company::where('id', $request['company_id'])->notDeleted()->where('is_in_map',true)->first();
        if (!$company && $request['company_id'])
            return $this->badRequest('الشركة المختارة غير صحيحة');


        if ($request->hasFile('image_file')) {
            $file_name = Str::uuid() . '_.' . $request->image_file->getClientOriginalExtension();
            $request['image'] = $request->file('image_file')->storeAs('towers', $file_name, 'public');
        }

        $edit =  $tower->Update(
            request()->only(
                'name',
                'image',
                'city',
                'town',
                'address',
                'longitude',
                'latitude',
                'is_active',
                'description',
                'company_id',
            )
        );
        $tower->save();
        if ($edit)
            return response()->json(['success' => true, 'message' => 'تم تحديث بيانات الشركة بنجاح', 'company' => $company], 200);
        return response()->json(['success' => true, 'message' => 'حدث خطأ ما'], 400);
    }









    





    // Delete Tower
    public function delete($tower)
    {
        $tower = Tower::where('id', $tower)->notDeleted()->first();
        if (!$tower)
            return response()->json(['success' => false, 'message' => 'هذه البرج غير موجود'], 204);


        $tower->status = 9;
        $edit = $tower->save();
        if ($edit)
            return response()->json(['success' => true, 'message' => 'تم حذف هذا البرج بنجاح'], 200);
        return response()->json(['success' => true, 'message' => 'حدث خطأ ما'], 400);
    }





    // End of Controller
}
