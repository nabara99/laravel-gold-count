<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvestorLocation extends Model
{
    /** @use HasFactory<\Database\Factories\InvestorLocationFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'location_id',
        'amount_invested',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }
}
