<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    /** @use HasFactory<\Database\Factories\TransactionFactory> */
    use HasFactory;

    protected $fillable = [
        'date',
        'location_id',
        'period_id',
        'qty',
        'price',
        'type',
        'amount',
    ];

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function period()
    {
        return $this->belongsTo(Period::class);
    }
}
