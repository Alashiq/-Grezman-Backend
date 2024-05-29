<?php

namespace App\Features\App\v1\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserNotification extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
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

    public function scopeIsSent($query)
    {
        return $query->where('is_sent', 1);
    }


    public function getCreatedAtAttribute()
    {
      Carbon::setlocale("ar");
        return Carbon::parse($this->attributes['created_at'])->diffForHumans();
    }
    
}
