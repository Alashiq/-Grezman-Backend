<?php

namespace App\Features\App\v1\Controllers;

use App\Features\App\v1\Models\Item;
use App\Features\App\v1\Models\Profile;
use App\Features\App\v1\Models\Tower;
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

class   NearController extends Controller
{

    public function towers(Request $request)
    {


        if ($request->count)
            $count = $request->count;
        else
            $count = 5;

        $list = Tower::latest()
            ->with('company')
            ->notDeleted()
            ->isActive()
            ->paginate($count);
        if ($list->isEmpty())
            return $this->empty();
        return response()->json(['success' => true, 'message' => 'تم جلب  الأبراج بنجاح', 'data' => $list], 200);
    }



    public function companyItems(Request $request, $id)
    {

        if (Validator::make(['id' => $id], [
            'id' => 'required|numeric'
        ])->fails()) {
            return $this->badRequest("يجب عليك ادخال رقم صحيح");
        }

        if ($request->count)
            $count = $request->count;
        else
            $count = 100;

        $list = Profile::latest()
            ->with('company')
            ->notDeleted()
            ->isActive()
            ->where('company_id', $id)
            ->paginate($count);
        if ($list->isEmpty())
            return $this->empty();
        return response()->json(['success' => true, 'message' => 'تم جلب  الأبراج بنجاح', 'data' => $list], 200);
    }





    // End OF Controller
}
