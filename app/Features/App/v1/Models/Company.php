<?php

namespace App\Features\App\v1\Models;

use Hamcrest\Arrays\IsArray;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Company extends Model
{
    protected $fillable = [
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
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


    public function scopeIsISP($query)
    {
        return $query->where('is_in_map', 1);
    }


}
