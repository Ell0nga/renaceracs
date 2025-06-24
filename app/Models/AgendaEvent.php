<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgendaEvent extends Model
{
    use HasFactory;

    protected $table = 'agenda_events';

    protected $fillable = [
        'title',
        'description',
        'date',
        'time_slot',
        'priority',
        'status',
        'client_number',
        'motivo',
        'costo',
    ];
}
