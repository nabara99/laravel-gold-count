<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    /** @use HasFactory<\Database\Factories\LocationFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'percent_investor',
        'percent_worker',
    ];

    public function absens()
    {
        return $this->hasMany(Absen::class);
    }

    public function stocks()
    {
        return $this->hasMany(Stock::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function workers()
    {
        return $this->hasMany(Worker::class);
    }
}


