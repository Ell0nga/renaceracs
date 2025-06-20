<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    // Relación: Un método de pago puede tener muchos gastos
    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    // Relación: Un método de pago puede tener muchos ingresos
    public function incomes()
    {
        return $this->hasMany(Income::class);
    }
}