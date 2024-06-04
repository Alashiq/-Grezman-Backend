<?php

namespace App\Features\Admin\v1\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;

class Admin extends Model
{
    use HasFactory;
    use HasApiTokens;
    protected $fillable = [
        'phone',
        'first_name',
        'last_name',
        'password',
        'role_id',
    ];


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];
    

    public function role()
    {
        return $this->belongsTo(related: Role::class, foreignKey: 'role_id');
    }


    public function getPhotoAttribute($value)
    {
        if ($value) {
            return url(Storage::url($value));
        }

    }


    public function scopeNotDeleted($query)
    {
        return $query->where('status', '<>', 9);
    }

}
