<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Worker extends Model
{
    /** @use HasFactory<\Database\Factories\WorkerFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'location_id',
        'age',
        'phone_number',
    ];

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function absens()
    {
        return $this->hasMany(Absen::class);
    }
}
