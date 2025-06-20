<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AgendaEvent;
use Carbon\Carbon;

class AgendaController extends Controller
{
    // NUEVA VISTA con tarjetas por día (sin calendario)
    public function cards()
    {
        $eventos = AgendaEvent::whereIn('status', ['Pendiente', 'En ejecución'])
            ->whereDate('date', '>=', Carbon::now()->subDays(1)) // muestra también los del día anterior
            ->orderBy('date')
            ->get()
            ->groupBy('date');

        return view('agenda.cards', ['diasConEventos' => $eventos]);
    }

    // GUARDAR nuevo evento
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'date' => 'required|date',
            'time_slot' => 'required|in:AM,PM',
            'priority' => 'required|in:Alta,Media,Baja',
            'status' => 'required|in:Pendiente,En ejecución,Completada,Pospuesta',
            'motivo' => 'required|in:Instalación,Reparación,Comercial,Otro',
        ]);

        AgendaEvent::create($request->all());

        return redirect()->route('agenda.cards')->with('success', 'Evento creado correctamente.');
    }
}
