<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'patent',
        'brand',
        'model',
        'year',
        'comment',
    ];

    // Relación: Un vehículo puede tener muchos gastos de combustible
    public function fuelExpenses()
    {
        return $this->hasMany(FuelExpense::class);
    }
}