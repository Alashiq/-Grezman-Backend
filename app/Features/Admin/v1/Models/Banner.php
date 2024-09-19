<?php

namespace App\Features\Admin\v1\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;

class Banner extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'image',
        'is_active',
        'rank',
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

    public function getCreatedAtAttribute()
    {
      Carbon::setlocale("ar");
        return Carbon::parse($this->attributes['created_at'])->diffForHumans();
    }



    public function getImageAttribute($value)
    {
        if ($value) {
            return url(Storage::url($value));
        }

    }

}
