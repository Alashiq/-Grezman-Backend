<?php

namespace App\Features\Admin\v1\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class  AdminResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $permissions = [];
        foreach (config('permissions.permissions') as $name => $value) {
            $boolVal = false;
            for ($i = 0; $i < count(json_decode($this->role->permissions, true)); $i++)
                if ($name == json_decode($this->role->permissions, true)[$i]) {
                    $boolVal = true;
                }
            array_push($permissions, ["name" => $name, "description" => $value, "state" => $boolVal]);
        }
        return [
            'phone' => $this->phone,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'photo' => url('/'). $this->photo,
            'token' => $this->token,
            'role' => $this->role->name,
           'permissions' =>$permissions ,
        ];
        //return parent::toArray($request);
    }
}
