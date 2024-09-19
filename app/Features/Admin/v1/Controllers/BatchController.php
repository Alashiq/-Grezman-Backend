<?php

namespace App\Features\Admin\v1\Controllers;

use App\Features\Admin\v1\Models\Admin;
use App\Features\Admin\v1\Models\Batch;
use App\Features\Admin\v1\Models\Company;
use App\Features\Admin\v1\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class  BatchController extends Controller
{

    // Admin List
    public function index(Request $request)
    {
        if ($request->count)
            $count = $request->count;
        else
            $count = 10;

        $companies = Batch::with('company')->latest()
            ->where('name', 'like', '%' . $request->name . '%')
            ->where('is_valid', 'like', '%' . $request->is_valid . '%')
            ->notDeleted()
            ->paginate($count);

        if ($companies->isEmpty())
            return $this->empty();
        return response()->json(['success' => true, 'message' => 'تم جلب  الكروت بنجاح', 'data' => $companies], 200);
    }


    // Get Admin By Id
    public function show(Request $request, $batch)
    {
        $batch = Batch::with('company')->withCount('availableVouchers')->withCount('soldVouchers')->withCount('vouchers')->where('id', $batch)->notDeleted()->first();
        if (!$batch)
            return $this->empty();


        $companies = Company::notDeleted()->get();

        return response()->json(['success' => true, 'message' => 'تم جلب بيانات الكرت بنجاح', 'data' => $batch, 'companies' => $companies], 200);
    }



    // Data For New
    public function new()
    {
        $companies = Company::notDeleted()->get();
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

        $company = Company::where('id', $request['company_id'])->notDeleted()->first();
        if (!$company)
            return $this->badRequest('الشركة المختارة غير صحيحة');


        $file_name = Str::uuid() . '_' . $request->image->getClientOriginalExtension();
        $file_path = $request->file('image')->storeAs('batches', $file_name, 'public');

        $company = Batch::create([
            'name' => $request['name'],
            'description' => $request['description'],
            'en_name' => $request['en_name'],
            'value' => $request['value'],
            'keywords' => $request['keywords'],
            'rank' => $request['rank'],
            'price' => $request['price'],
            'company_id' => $request['company_id'],
            'is_valid' => $request->input('is_valid') == 1 ? 1 : 0,
            'image' => $file_path,
        ]);
        return response()->json(['success' => true, 'message' => 'تم إضافة الحزمة بنجاح'], 200);
    }



    //  Change Company
    public function edit($company, Request $request)
    {
        $batch = Batch::where('id', $company)->notDeleted()->first();
        if (!$batch)
            return response()->json(['success' => false, 'message' => 'هذه الشركة غير متوفرة'], 400);



        if ($request->hasFile('image_file') && Validator::make($request->all(), [
            'image_file' => 'mimes:jpg,jpeg,png',
        ])->fails()) {
            return response()->json(["success" => false, "message" => "الملف الذي اخترته ليس صورة"], 400);
        }

        $company = Company::where('id', $request['company_id'])->notDeleted()->first();
        if (!$company && $request['company_id'])
            return $this->badRequest('الشركة المختارة غير صحيحة');


        if ($request->hasFile('image_file')) {
            $file_name = Str::uuid() . '_.' . $request->image_file->getClientOriginalExtension();
            $request['image'] = $request->file('image_file')->storeAs('batches', $file_name, 'public');
        }

        $edit =  $batch->Update(
            request()->only(
                'name',
                'en_name',
                'value',
                'description',
                'keywords',
                'image',
                'rank',
                'price',
                'company_id',
                'is_valid',
            )
        );
        $batch->save();
        if ($edit)
            return response()->json(['success' => true, 'message' => 'تم تحديث بيانات الشركة بنجاح', 'company' => $company], 200);
        return response()->json(['success' => true, 'message' => 'حدث خطأ ما'], 400);
    }









    // Delete Company
    public function delete($batch)
    {
        $batch = Batch::where('id', $batch)->notDeleted()->first();
        if (!$batch)
            return response()->json(['success' => false, 'message' => 'هذه الحزمة غير موجود'], 204);


        $batch->status = 9;
        $edit = $batch->save();
        if ($edit)
            return response()->json(['success' => true, 'message' => 'تم حذف هذه الشركة بنجاح'], 200);
        return response()->json(['success' => true, 'message' => 'حدث خطأ ما'], 400);
    }





    // End of Controller
}
