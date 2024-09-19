<?php

namespace App\Features\App\v1\Controllers;

use App\Features\App\v1\Models\Batch;
use App\Features\App\v1\Models\Item;
use App\Features\App\v1\Models\Sale;
use App\Features\App\v1\Models\Transaction;
use App\Features\App\v1\Models\User;
use App\Features\App\v1\Models\Voucher;
use App\Models\Notification;
use Illuminate\Support\Facades\Validator;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Cache\RateLimiter;

class StoreController extends Controller
{

    public function index(Request $request)
    {


        if ($request->count)
            $count = $request->count;
        else
            $count = 1;

        $list = Batch::latest()
            ->isValid()
            ->withAvailableVouchers()
            ->where('name', 'like', '%' . $request->name . '%')
            ->paginate($count);
        if ($list->isEmpty())
            return $this->empty();
        return response()->json(['success' => true, 'message' => 'تم جلب  الكروت بنجاح', 'data' => $list], 200);
    }



    public function buy(Request $request)
    {
        // Get the authenticated user's ID
        $userId = $request->user()->id;

        $request->validate([
            'products' => 'required|array',
            'products.*.batch_id' => 'required|exists:batches,id',
            'products.*.count' => 'required|integer|min:1'
        ]);

        $user = User::find($userId);
        $totalAmount = 0.0;
        $vouchers = [];

        DB::beginTransaction();

        
        try {
            foreach ($request->products as $product) {
                $batch = Batch::find($product['batch_id']);
                $amount = $batch->price * $product['count']; 
                $totalAmount += $amount;

                $availableVouchers = Voucher::where('batch_id', $product['batch_id'])
                    ->where('status', 0) // 0 means available
                    ->limit($product['count'])
                    // ->sharedLock()
                    ->get();


                if ($availableVouchers->count() < $product['count']) {
                    return $this->badRequest('أحد الكروت التي قمت بطلبها غير متوفر بكمية كافية' . $product['batch_id']);
                }

                foreach ($availableVouchers as $voucher) {
                    $vouchers[] = $voucher;
                }
                

            }

            if ($user->balance < $totalAmount) {
                return $this->badRequest('رصيدك غير كافي للشراء');
            }

            // Proceed with the purchase
            foreach ($vouchers as $voucher) {
                $voucher->status = 1; // 1 means sold
                $voucher->save();
            }

            // Update user balance and points
            $balanceBefore = $user->balance;
            $user->balance -= $totalAmount;
            $user->save();


            // Record transactions

            $transaction = Transaction::create([
                'user_id' => $user->id,
                'transaction_type' => 'buy',
                'amount' => $totalAmount,
                'balance_before' => $balanceBefore,
                'balance_after' => $user->balance,
                'points_before' => $user->point,
                'points_after' => $user->point, // Assuming points are not updated here
            ]);

            foreach ($vouchers as $voucher) {
                Sale::create([
                    'user_id' => $user->id,
                    'voucher_id' => $voucher->id,
                    'sale_date' => now(),
                    'amount' => $voucher->batch->price, // Assuming batch has a 'value' attribute
                    'payment_method' => 'balance',
                    'transaction_id' => $transaction->id,
                ]);
            }


            DB::commit();

            return response()->json(['message' => 'تمت عملية الشراء بنجاح', 'balance' => $user->balance], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->badRequest('حدث خطأ ما, حاول مجددا ');
            //return response()->json(['message' => $e->getMessage()], 400);
        }
    }



    public function myPurchases(Request $request)
    {


        if ($request->count)
            $count = $request->count;
        else
            $count = 1;

        $list = Sale::latest()
            ->where('user_id', $request->user()->id)->with('voucher.batch')
            ->paginate($count);
        if ($list->isEmpty())
            return $this->empty();
        return response()->json(['success' => true, 'message' => 'تم جلب  الحسابات بنجاح', 'data' => $list], 200);
    }


    public function myTransactions(Request $request)
    {


        if ($request->count)
            $count = $request->count;
        else
            $count = 20;

        $list = Transaction::latest()
            ->where('user_id', $request->user()->id)
            ->paginate($count);
        if ($list->isEmpty())
            return $this->empty();
        return response()->json(['success' => true, 'message' => 'تم جلب  التحويلات بنجاح', 'data' => $list], 200);
    }


    // End OF Controller
}
