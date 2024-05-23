<?php

namespace App\Features\App\v1\Models;

use Hamcrest\Arrays\IsArray;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class User extends Model
{
    use HasApiTokens;
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
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
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
}
