<?php

namespace App\Features\App\v1\Controllers;

use App\Features\App\v1\Models\Item;
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

class ItemController extends Controller
{

    public function index(Request $request)
    {


        if ($request->count)
            $count = $request->count;
        else
            $count = 1;

        $list = Item::latest()
            ->notDeleted()
            ->where('name', 'like', '%' . $request->name . '%')
            ->paginate($count);
        if ($list->isEmpty())
            return $this->empty();
        return response()->json(['success' => true, 'message' => 'تم جلب  الحسابات بنجاح', 'data' => $list], 200);
    }


    public function show($id)
    {

        if (Validator::make(['id' => $id], [
            'id' => 'required|numeric'
        ])->fails()) {
            return $this->badRequest("يجب عليك ادخال رقم صحيح");
        }

        $item = Item::notDeleted()->where('id', $id)->first();
        if (!$item)
            return $this->empty();
        return response()->json(['success' => true, 'message' => 'تم جلب الحساب بنجاح', 'data' => $item], 200);
    }


    public function store(Request $request){

        if (Validator::make($request->all(), [
            'naem' => 'required|string|min:2|max:25',
        ])->fails()) {
            return $this->badRequest("يجب عليك إدخال الإسم");
        }


        $newItem = Item::create([
            'name' => $request['name'],
            'description' => $request['description']?? null,

        ]);
        return response()->json(['success' => true, 'message' => 'تم إضافة هذا العنصر بنجاح'], 201);
    }



    // End OF Controller
}
