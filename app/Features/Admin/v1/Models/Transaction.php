<?php

namespace App\Features\Admin\v1\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;

class Transaction extends Model
{
    use HasFactory;
    protected $fillable = [
        'transaction_type',
        'user_id',
        'amount',
        'balance_before',
        'balance_after',
        'points_before',
        'points_after',
    ];


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
    ];
    


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    
}
