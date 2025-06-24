<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FiberReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_number',
        'seal_number',
        'connector_type',
        'latitude',
        'longitude',
    ];
}