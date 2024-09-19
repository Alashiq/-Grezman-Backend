<?php

namespace App\Features\App\v1\Models;

use Hamcrest\Arrays\IsArray;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Batch extends Model
{
    protected $fillable = [];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];


    public function scopeIsValid($query)
    {
        return $query->where('status', '<>', 9)->where('is_valid', true);
    }

    public function getImageAttribute($value)
    {
        if ($value) {
            return url(Storage::url($value));
        }
    }

    public function vouchers()
    {
        return $this->hasMany(Voucher::class);
    }

    public function scopeWithAvailableVouchers($query)
    {
        return $query->whereHas('vouchers', function ($query) {
            $query->where('status', 0); // 0 means available
        });
    }
}
