<?php

namespace App\Features\Admin\v1\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;

class Batch extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'en_name',
        'value',
        'description',
        'keywords',
        'image',
        'rank',
        'price',
        'company_id',
        'is_valid',
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


    public function getImageAttribute($value)
    {
        if ($value) {
            return url(Storage::url($value));
        }
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function availableVouchers()
    {
       return $this->hasMany(Voucher::class,'batch_id')->where('vouchers.status', 0);
    }

    public function soldVouchers()
    {
       return $this->hasMany(Voucher::class,'batch_id')->where('vouchers.status', 1);
    }

    public function vouchers()
    {
       return $this->hasMany(Voucher::class,'batch_id')->where('vouchers.status', '<>', 9);
    }

}
