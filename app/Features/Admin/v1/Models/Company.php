<?php

namespace App\Features\Admin\v1\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;

class Company extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'description',
        'address',
        'logo',
        'background',
        'phone',
        'cities',
        'longitude',
        'latitude',
        'move_price',
        'join_price',
        'is_in_store',
        'is_in_map',
        'is_have_account',
        'system_type',
        'website',
        'email',
        'support_phone',
    ];


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
    ];
    
    public function scopeNotDeleted($query)
    {
        return $query->where('status', '<>', 9);
    }

    public function getLogoAttribute($value)
    {
        if ($value) {
            return url(Storage::url($value));
        }

    }

    public function getBackgroundAttribute($value)
    {
        if ($value) {
            return url(Storage::url($value));
        }

    }

}
