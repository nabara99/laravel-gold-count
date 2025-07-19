<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cashbon extends Model
{
    protected $fillable = [
        'worker_id',
        'location_id',
        'date',
        'amount',
        'description',
        'status',
    ];

    public function worker()
    {
        return $this->belongsTo(Worker::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }
}
