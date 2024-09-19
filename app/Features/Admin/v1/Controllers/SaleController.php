<?php

namespace App\Features\Admin\v1\Controllers;

use App\Features\Admin\v1\Models\Admin;
use App\Features\Admin\v1\Models\Batch;
use App\Features\Admin\v1\Models\Company;
use App\Features\Admin\v1\Models\Role;
use App\Features\Admin\v1\Models\Sale;
use App\Features\Admin\v1\Models\Transaction;
use App\Features\Admin\v1\Models\User;
use App\Features\Admin\v1\Models\Voucher;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class   SaleController extends Controller
{

    // Admin List
    public function index(Request $request)
    {
        if ($request->count)
            $count = $request->count;
        else
            $count = 10;

        $companies = Sale::latest()
            ->with('user:id,first_name,last_name')
            ->with('voucher.batch')
            ->where('transaction_id', 'like', '%' . $request->transaction_id . '%')
            ->where('user_id', 'like', '%' . $request->user_id . '%')
            ->paginate($count);

        if ($companies->isEmpty())
            return $this->empty();
        return response()->json(['success' => true, 'message' => 'تم جلب الكروت المباعة بنجاح', 'data' => $companies], 200);
    }


    // Get Transaction By Id
    public function show(Request $request, $sale)
    {
        $sale = Sale::with('user')
            ->with('voucher')
            ->where('id', $sale)->first();
        if (!$sale)
            return $this->empty();
        return response()->json(['success' => true, 'message' => 'تم جلب بيانات العملية بنجاح', 'data' => $sale], 200);
    }







    // End of Controller
}
