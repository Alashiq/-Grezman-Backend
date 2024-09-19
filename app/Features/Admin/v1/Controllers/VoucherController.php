<?php

namespace App\Features\Admin\v1\Controllers;

use App\Features\Admin\v1\Models\Admin;
use App\Features\Admin\v1\Models\Batch;
use App\Features\Admin\v1\Models\Company;
use App\Features\Admin\v1\Models\Role;
use App\Features\Admin\v1\Models\Voucher;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class VoucherController extends Controller
{

    // Admin List
    public function index(Request $request)
    {
        if ($request->count)
            $count = $request->count;
        else
            $count = 10;

        $companies = Voucher::latest()
            ->with('batch')
            ->with('company')
            ->where('one_value', 'like', '%' . $request->one_value . '%')
            ->where('two_value', 'like', '%' . $request->two_value . '%')
            ->where('three_value', 'like', '%' . $request->two_value . '%')
            ->where('status', 'like', '%' . $request->status . '%')
            ->notDeleted()
            ->paginate($count);

        if ($companies->isEmpty())
            return $this->empty();
        return response()->json(['success' => true, 'message' => 'تم جلب  الشركات بنجاح', 'data' => $companies], 200);
    }


    // Get Voucher By Id
    public function show(Request $request, $company)
    {
        $voucher = Voucher::with('batch')
            ->with('company')
            ->where('id', $company)->notDeleted()->first();
        if (!$voucher)
            return $this->empty();
        return response()->json(['success' => true, 'message' => 'تم جلب بيانات الكرت بنجاح', 'data' => $voucher], 200);
    }



    // Data For New
    public function new()
    {
        $companies = Company::notDeleted()->get();
        $batches = Batch::notDeleted()->get();
        return response()->json(['success' => true, 'message' => 'تم جلب البيانات بنجاح', 'companies' => $companies, 'batches' => $batches], 200);
    }





    // Add New Admin
    public function store(Request $request)
    {



        if (Validator::make($request->all(), [
            'hash_key' => 'required|string',
        ])->fails()) {
            return $this->badRequest("يجب عليك إدخال رمز التشفير");
        }



        $voucher = Voucher::create([
            'one_state' => $request['one_state'],
            'one_label' =>$request['one_state']==1? $request['one_label']:'No Value',
            'one_value' =>$request['one_state']==1? $request['one_value']:'No Value',
            'two_state' => $request['two_state'],
            'two_label' =>$request['two_state']==1?  $request['two_label']:'No Value',
            'two_value' => $request['two_state']==1?  $request['two_value']:'No Value',
            'three_state' => $request['three_state'],
            'three_label' =>$request['three_state']==1?  $request['three_label']:'No Value',
            'three_value' =>$request['three_state']==1?  $request['three_value']:'No Value',
            'four_state' => $request['four_state'],
            'four_label' =>$request['four_state']==1? $request['four_label']:'No Value',
            'four_value' =>$request['four_state']==1?  $request['four_value']:'No Value',



            'hash_key' => $request['hash_key'],
            'company_id' => $request['company_id'],
            'batch_id' => $request['batch_id'],
        ]);
        return response()->json(['success' => true, 'message' => 'تم إضافة الكرت بنجاح'], 200);
    }



    //  Change Company
    public function edit($voucher, Request $request)
    {
        $voucher = Voucher::where('id', $voucher)->notDeleted()->first();
        if (!$voucher)
            return response()->json(['success' => false, 'message' => 'هذه الشركة غير متوفرة'], 400);


        $edit =  $voucher->Update(
            request()->only(
                'one_state',
                'one_label',
                'one_value',
                'two_state',
                'two_label',
                'two_value',
                'three_state',
                'three_label',
                'three_value',
                'four_state',
                'four_label',
                'four_value',
                'hash_key',
            )
        );
        $voucher->save();
        if ($edit)
            return response()->json(['success' => true, 'message' => 'تم تحديث بيانات الكرت بنجاح', 'voucher' => $voucher], 200);
        return response()->json(['success' => true, 'message' => 'حدث خطأ ما'], 400);
    }









    // Cancel Company
    public function cancel($voucher)
    {
        $voucher = Voucher::where('id', $voucher)->notDeleted()->first();
        if (!$voucher)
            return response()->json(['success' => false, 'message' => 'هذه الكرت غير موجود'], 204);

        if ($voucher->status != 0)
            return $this->badRequest('هذا الكرت قد يكون مباع او مستخدم من قبل او تم ايقافه');

        $voucher->status = 3;
        $edit = $voucher->save();
        if ($edit)
            return response()->json(['success' => true, 'message' => 'تم ايقاف هذا الكرت بنجاح'], 200);
        return response()->json(['success' => true, 'message' => 'حدث خطأ ما'], 400);
    }





    // End of Controller
}
