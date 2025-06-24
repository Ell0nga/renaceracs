<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\FinanzaCategoriaTipo; // ¡IMPORTA TU ENUM AQUÍ!

class Transaccion extends Model
{
    use HasFactory;

    // Opcional: Si tu tabla se llama diferente a 'transaccions' (plural de 'transaccion')
    // protected $table = 'transacciones';

    // Campos que pueden ser asignados masivamente
    protected $fillable = [
        'monto',
        'descripcion',
        'tipo_categoria', // Asegúrate de incluir la columna de tu enum
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        // ¡ESTA ES LA MAGIA! Indica a Laravel que 'tipo_categoria' es un enum
        'tipo_categoria' => FinanzaCategoriaTipo::class,
    ];
}