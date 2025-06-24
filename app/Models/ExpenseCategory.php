<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpenseCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        // 'user_id', // <--- Eliminamos esta línea
    ];

    // Relación: Una categoría de gasto puede tener muchos gastos
    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    // Eliminamos la relación user() si estaba aquí
    // public function user()
    // {
    //     return $this->belongsTo(User::class);
    // }
}