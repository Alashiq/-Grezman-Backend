<?php

namespace App\Features\Admin\v1\Models;

use App\Features\Admin\v1\Models\Admin;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $fillable = [
        'name',
        'permissions',
        'stats',
    ];

    public function admins()
    {
       return $this->hasMany(Admin::class,'role_id')->where('admins.state', '<>', 9);
    }

    public function scopeNotDeleted($query)
    {
        return $query->where('status', '<>', 9);
    }
}
