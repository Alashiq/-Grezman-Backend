<?php

namespace App\Features\Admin\v1\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class  PermissionResource extends JsonResource
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
            for ($i = 0; $i < count(json_decode($this->permissions, true)); $i++)
                if ($name == json_decode($this->permissions, true)[$i]) {
                    $boolVal = true;
                }
            array_push($permissions, ["name" => $name, "description" => $value, "state" => $boolVal]);
        }
        return [
            'id' => $this->id,
            'name' => $this->name,
            'admins_count' => $this->admins_count,
           'permissions' =>$permissions ,
        ];
        //return parent::toArray($request);
    }
}
