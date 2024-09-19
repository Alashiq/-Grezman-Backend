<?php

namespace App\Features\App\v1\Models;

use Hamcrest\Arrays\IsArray;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class   Banner extends Model
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

    public function scopeIsActive($query)
    {
        return $query->where('is_active', 1);
    }

    public function getImageAttribute($value)
    {
        if ($value) {
            return url(Storage::url($value));
        }
    }

}
