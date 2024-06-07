<?php

namespace App\Features\Admin\v1\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;

class User extends Model
{
    use HasFactory;

    
    protected $fillable = [
        'phone',
        'first_name',
        'last_name',
        'photo',
        'login_attempts',
        'attempts_at',
        'ban_expires_at',

        'otp',
        'otp_attempts',
        'otp_attempts_at',


        'point',
        'device_token',
        'last_notification',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'otp',
        'remember_token',
    ];
    


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


    public function getCreatedAtAttribute()
    {
      Carbon::setlocale("ar");
        return Carbon::parse($this->attributes['created_at'])->diffForHumans();
    }

    public function getUpdatedAtAttribute()
    {
      Carbon::setlocale("ar");
        return Carbon::parse($this->attributes['created_at'])->diffForHumans();
    }

    
}
