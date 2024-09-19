<?php

namespace App\Features\Admin\v1\Controllers;

use App\Features\Admin\v1\Models\Admin;
use App\Features\Admin\v1\Models\Batch;
use App\Features\Admin\v1\Models\Company;
use App\Features\Admin\v1\Models\Role;
use App\Features\Admin\v1\Models\Transaction;
use App\Features\Admin\v1\Models\User;
use App\Features\Admin\v1\Models\Voucher;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class TransactionController extends Controller
{

    // Admin List
    public function index(Request $request)
    {
        if ($request->count)
            $count = $request->count;
        else
            $count = 10;

        $companies = Transaction::latest()
            ->with('user')
            ->where('transaction_type', 'like', '%' . $request->transaction_type . '%')
            ->where('user_id', 'like', '%' . $request->user_id . '%')
            ->paginate($count);

        if ($companies->isEmpty())
            return $this->empty();
        return response()->json(['success' => true, 'message' => 'تم جلب  الشركات بنجاح', 'data' => $companies], 200);
    }


    // Get Transaction By Id
    public function show(Request $request, $transaction)
    {
        $transaction = Transaction::with('user')
            ->where('id', $transaction)->first();
        if (!$transaction)
            return $this->empty();
        return response()->json(['success' => true, 'message' => 'تم جلب بيانات العملية بنجاح', 'data' => $transaction], 200);
    }



    // Data For New
    public function new()
    {
        return response()->json(['success' => true, 'message' => 'تم جلب البيانات بنجاح', 'data' => []], 200);
    }





    // Add New Transaction
    public function store(Request $request)
    {


        $user = User::where('id', $request['user_id'])->first();
        if (Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
        ])->fails()) {
            return $this->badRequest("يجب عليك إدخال رقم مستخدم صحيح");
        }

        $newBalance = $user->balance + $request->amount;

        $voucher = Transaction::create([
            'transaction_type' => $request['transaction_type'],
            'user_id' => $request['user_id'],
            'amount' => $request['amount'],
            'balance_before' => $user->balance,
            'balance_after' => $newBalance,
            'points_before' => $user->point,
            'points_after' => $user->point,
        ]);

        $user->balance = $newBalance;
        $edit = $user->save();

        return response()->json(['success' => true, 'message' => 'تم إضافة الكرت بنجاح'], 200);
    }




    // End of Controller
}
