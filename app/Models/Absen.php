<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absen extends Model
{
    /** @use HasFactory<\Database\Factories\AbsenFactory> */
    use HasFactory;

    protected $fillable = [
        'worker_id',
        'location_id',
        'date',
        'status',
    ];

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function worker()
    {
        return $this->belongsTo(Worker::class);
    }
}
