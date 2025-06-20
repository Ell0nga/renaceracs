<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles; // Esta línea es correcta

// Si estás usando Laravel Sanctum para API tokens, descomenta la siguiente línea:
// use Laravel\Sanctum\HasApiTokens; 

class User extends Authenticatable
{
    // Consolida todos los traits en una sola línea 'use':
    use HasFactory, Notifiable, HasRoles; // Eliminamos HasApiTokens si no lo necesitas, y unificamos.

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Relación: Un usuario puede tener muchos ingresos
    public function incomes()
    {
        return $this->hasMany(Income::class);
    }

    // Relación: Un usuario puede tener muchos gastos
    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }
}