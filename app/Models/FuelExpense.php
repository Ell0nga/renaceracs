<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FuelExpense extends Model
{
    use HasFactory;

    protected $fillable = [
        'expense_id',
        'vehicle_id',
        'liters',
        'odometer_reading',
        'notes',
    ];

    // Relación: Un gasto de combustible pertenece a un gasto principal (Expense)
    public function expense()
    {
        return $this->belongsTo(Expense::class);
    }

    // Relación: Un gasto de combustible pertenece a un vehículo
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }
}