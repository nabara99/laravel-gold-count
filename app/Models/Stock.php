<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    /** @use HasFactory<\Database\Factories\StockFactory> */
    use HasFactory;

    protected $fillable = [
        'date',
        'weight',
        'location_id',
        'notes',
    ];

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

}
