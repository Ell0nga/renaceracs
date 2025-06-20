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
        'payment_method', // <--- CAMBIADO: Ahora es una clave foránea
        'comment',
    ];

    protected $casts = [
        'transaction_date' => 'date',
    ];

    // Relación: Un ingreso pertenece a un usuario (quien lo registró)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // NUEVA Relación: Un ingreso pertenece a un método de pago
    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }
}