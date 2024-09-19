<?php

namespace App\Features\Admin\v1\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;

class Voucher extends Model
{
    use HasFactory;
    protected $fillable = [
        'one_state',
        'one_label',
        'one_value',
        'two_state',
        'two_label',
        'two_value',
        'three_state',
        'three_label',
        'three_value',
        'four_state',
        'four_label',
        'four_value',
        'hash_key',
        'company_id',
        'batch_id',
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

    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
    
}
