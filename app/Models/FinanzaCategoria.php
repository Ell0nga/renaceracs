<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FinanzaCategoria extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'finanza_categorias';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nombre',
        'descripcion',
        'tipo',
        'parent_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'tipo' => \App\Enums\FinanzaCategoriaTipo::class, // Vamos a crear este Enum
    ];

    /**
     * Get the parent category that owns the FinanzaCategoria.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(FinanzaCategoria::class, 'parent_id');
    }

    /**
     * Get the child categories for the FinanzaCategoria.
     */
    public function children(): HasMany
    {
        return $this->hasMany(FinanzaCategoria::class, 'parent_id');
    }
}