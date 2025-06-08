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
        'payment_method',
        'assigned_to',
        'comment',
    ];

    protected $casts = [
        'transaction_date' => 'date',
    ];

    // Relación: Un gasto pertenece a un usuario
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relación: Un gasto pertenece a una categoría de gasto
    public function category()
    {
        return $this->belongsTo(ExpenseCategory::class, 'expense_category_id');
    }
}
