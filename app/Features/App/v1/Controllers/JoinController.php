<?php

namespace App\Features\App\v1\Controllers;

use App\Features\App\v1\Models\Item;
use App\Features\App\v1\Models\Join;
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

class   JoinController extends Controller
{





    public function store(Request $request){

        if (Validator::make($request->all(), [
            'name' => 'required|string|min:2|max:25',
        ])->fails()) {
            return $this->badRequest("يجب عليك إدخال الإسم");
        }


        $newItem = Join::create([
            'join_type' => $request['join_type'],
            'name' => $request['name'],
            'phone' => $request['phone'],
            'address' => $request['address'],
            'user_id' => $request->user()->id,
            'company_id' => $request['company_id'],
        ]);
        return response()->json(['success' => true, 'message' => 'تم تسجيل طلبك بنجاح, سيتم التواصل معك من قبل الشركة'], 201);
    }



    // End OF Controller
}
