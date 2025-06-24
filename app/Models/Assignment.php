<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
    ];

    // Relación: Una asignación puede tener muchos gastos
    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }
}