<?php

namespace App\Features\App\v1\Models;

use Carbon\Carbon;
use Hamcrest\Arrays\IsArray;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Sale extends Model
{
    protected $fillable = [
        'user_id',
        'voucher_id',
        'sale_date',
        'amount',
        'payment_method',
        'transaction_id',

    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];

    public function voucher()
    {
        return $this->belongsTo(Voucher::class);
    }


    public function getSaleDateAttribute()
    {
      Carbon::setlocale("ar");
        return Carbon::parse($this->attributes['sale_date'])->diffForHumans();
    }

}
