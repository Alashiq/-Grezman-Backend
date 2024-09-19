<?php

namespace App\Features\App\v1\Controllers;

use App\Features\App\v1\Models\Banner;
use App\Features\App\v1\Models\Batch;
use App\Features\App\v1\Models\Company;
use App\Features\App\v1\Models\Item;
use App\Features\App\v1\Models\Profile;
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
use Spatie\FlareClient\Http\Exceptions\BadResponse;

class SelaNetController extends Controller
{

    public function profiles(Request $request)
    {


        if ($request->count)
            $count = $request->count;
        else
            $count = 100;

        $list = Profile::latest()
            ->notDeleted()
            ->isActive()
            ->paginate($count);
        if ($list->isEmpty())
            return $this->empty();
        return response()->json(['success' => true, 'message' => 'تم جلب  الباقات بنجاح', 'data' => $list], 200);

    }




    // End OF Controller
}
