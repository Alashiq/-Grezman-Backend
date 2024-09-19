<?php

namespace App\Features\Admin\v1\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;

class Profile extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'profile_id',
        'price',
        'time',
        'description',
        'download',
        'upload',
        'qouta',
        'next',
        'is_active',
        'company_id',
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



    public function company()
    {
        return $this->belongsTo(Company::class);
    }


}