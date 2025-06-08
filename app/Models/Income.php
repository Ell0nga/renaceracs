<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Income extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'client_number',
        'amount',
        'transaction_date',
        'type',
        'payment_method',
        'comment',
    ];

    protected $casts = [
        'transaction_date' => 'date',
    ];

    // Relación: Un ingreso pertenece a un usuario
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
