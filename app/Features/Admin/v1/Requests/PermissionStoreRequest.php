<?php

namespace App\Features\Admin\v1\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;


class PermissionStoreRequest extends FormRequest
{

    public function rules()
    {
        return [
            'name' => 'required|max:40|min:3|unique:roles',
            'permissions' => 'required|array|min:1',
        ];
    }

    public function messages()
    {

        return [
            'name.required' => 'يجب عليك ادخال الإسم بشكل صحيح',
            'name.max' => 'يجب ان يكون الاسم اقل من 40 حرف',
            'name.min' => 'يجب ان يكون الاسم 3 أحرف على الاقل',
            'name.unique' => 'هذا الإسم محجوز مسبقا',
            'permissions.required' => 'يجب عليك ادخال الصلاحيات بشكل صحيح',
            'permissions.array' => 'يجب ان يتم ادخال الصلاحيات على شكل مصفوفة',
            'permissions.min' => 'يجب عليك ادخال صلاحية واحدة على الاقل',
        ];

    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 400)
        );



    }


}