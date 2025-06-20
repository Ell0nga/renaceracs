<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaccion; // Importa tu modelo Transaccion
use App\FinanzaCategoriaTipo; // Importa tu enum FinanzaCategoriaTipo
use Illuminate\Validation\Rule; // Para validar si el tipo existe en el enum
use Illuminate\Support\Facades\Log; // Opcional: para depuración

class TransaccionController extends Controller
{
    /**
     * Display a listing of the resource.
     * Muestra una lista de todas las transacciones.
     */
    public function index()
    {
        // Puedes filtrar por tipo si es necesario, o mostrar todas
        $transacciones = Transaccion::orderBy('created_at', 'desc')->get();

        // Puedes pasar las transacciones a una vista o devolverlas como JSON
        return view('transacciones.index', compact('transacciones'));
        // return response()->json($transacciones); // Para API
    }

    /**
     * Show the form for creating a new resource.
     * Muestra el formulario para crear una nueva transacción.
     */
    public function create()
    {
        // Pasa los casos del enum a la vista para poblar un select/dropdown
        $tipos = FinanzaCategoriaTipo::cases();
        return view('transacciones.create', compact('tipos'));
    }

    /**
     * Store a newly created resource in storage.
     * Almacena una nueva transacción en la base de datos.
     */
    public function store(Request $request)
    {
        // Valida los datos entrantes.
        // Rule::in asegura que 'tipo_categoria' sea uno de los valores del enum.
        $validatedData = $request->validate([
            'monto' => ['required', 'numeric', 'min:0.01'],
            'descripcion' => ['nullable', 'string', 'max:255'],
            'tipo_categoria' => [
                'required',
                'string',
                Rule::in(array_column(FinanzaCategoriaTipo::cases(), 'value')),
            ],
        ]);

        try {
            // Crea la transacción. Laravel maneja automáticamente el casting del string al enum.
            $transaccion = Transaccion::create($validatedData);

            // Redirige o responde con éxito
            return redirect()->route('finanzas.transacciones.index')
                ->with('success', 'Transacción guardada exitosamente.');

            // O para API:
            // return response()->json(['message' => 'Transacción guardada exitosamente.', 'transaccion' => $transaccion], 201);

        } catch (\Exception $e) {
            // Manejo de errores
            Log::error("Error al guardar la transacción: " . $e->getMessage());
            return back()->withInput()->withErrors(['error' => 'Hubo un problema al guardar la transacción. Por favor, inténtalo de nuevo.']);
        }
    }

    /**
     * Display the specified resource.
     * Muestra una transacción específica.
     */
    public function show(Transaccion $transaccion)
    {
        return view('transacciones.show', compact('transaccion'));
        // return response()->json($transaccion); // Para API
    }

    /**
     * Show the form for editing the specified resource.
     * Muestra el formulario para editar una transacción existente.
     */
    public function edit(Transaccion $transaccion)
    {
        $tipos = FinanzaCategoriaTipo::cases();
        return view('transacciones.edit', compact('transaccion', 'tipos'));
    }

    /**
     * Update the specified resource in storage.
     * Actualiza una transacción existente en la base de datos.
     */
    public function update(Request $request, Transaccion $transaccion)
    {
        $validatedData = $request->validate([
            'monto' => ['required', 'numeric', 'min:0.01'],
            'descripcion' => ['nullable', 'string', 'max:255'],
            'tipo_categoria' => [
                'required',
                'string',
                Rule::in(array_column(FinanzaCategoriaTipo::cases(), 'value')),
            ],
        ]);

        try {
            $transaccion->update($validatedData);

            return redirect()->route('finanzas.transacciones.index')
                ->with('success', 'Transacción actualizada exitosamente.');
            // O para API:
            // return response()->json(['message' => 'Transacción actualizada exitosamente.', 'transaccion' => $transaccion]);

        } catch (\Exception $e) {
            Log::error("Error al actualizar la transacción: " . $e->getMessage());
            return back()->withInput()->withErrors(['error' => 'Hubo un problema al actualizar la transacción. Por favor, inténtalo de nuevo.']);
        }
    }

    /**
     * Remove the specified resource from storage.
     * Elimina una transacción de la base de datos.
     */
    public function destroy(Transaccion $transaccion)
    {
        try {
            $transaccion->delete();
            return redirect()->route('finanzas.transacciones.index')
                ->with('success', 'Transacción eliminada exitosamente.');

            // O para API:
            // return response()->json(['message' => 'Transacción eliminada exitosamente.']);

        } catch (\Exception $e) {
            Log::error("Error al eliminar la transacción: " . $e->getMessage());
            return back()->withErrors(['error' => 'Hubo un problema al eliminar la transacción.']);
        }
    }

    /**
     * Muestra el dashboard financiero general.
     */
    public function dashboard()
    {
        // Ejemplo de cálculo para el dashboard
        $totalIngresos = Transaccion::where('tipo_categoria', FinanzaCategoriaTipo::INGRESO)->sum('monto');
        $totalEgresos = Transaccion::where('tipo_categoria', FinanzaCategoriaTipo::EGRESO)->sum('monto');
        $balance = $totalIngresos - $totalEgresos;

        return view('finanzas.dashboard', compact('totalIngresos', 'totalEgresos', 'balance'));
    }
}