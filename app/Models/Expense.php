<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'expense_category_id',
        'amount',
        'transaction_date',
        'payment_method_id', // <--- CAMBIADO: Ahora es una clave foránea
        'assignment_id',     // <--- AÑADIDO: Clave foránea para la asignación
        'comment',
    ];

    protected $casts = [
        'transaction_date' => 'date',
    ];

    // Relación: Un gasto pertenece a un usuario (quien lo registró)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relación: Un gasto pertenece a una categoría de gasto
    public function category()
    {
        return $this->belongsTo(ExpenseCategory::class, 'expense_category_id');
    }

    // NUEVA Relación: Un gasto pertenece a un método de pago
    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    // NUEVA Relación: Un gasto pertenece a una asignación
    public function assignment()
    {
        return $this->belongsTo(Assignment::class);
    }

    // Relación: Un gasto puede ser un gasto de combustible específico (uno a uno)
    public function fuelExpense()
    {
        return $this->hasOne(FuelExpense::class);
    }
}