<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FiberReport;
use Illuminate\Support\Facades\Log;

class PublicFormController extends Controller
{
    public function show()
    {
        return view('public.form');
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'client_number' => 'required|string|max:255',
                'seal_number' => 'required|string|max:255',
                'connector_type' => 'required|in:SC/APC,SC/UPC',
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
            ]);

            $report = FiberReport::create([
                'client_number' => $validated['client_number'],
                'seal_number' => $validated['seal_number'],
                'connector_type' => $validated['connector_type'],
                'latitude' => $validated['latitude'],
                'longitude' => $validated['longitude'],
            ]);

            return response()->json([
                'message' => 'Reporte enviado con éxito',
                'data' => $report,
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Error de validación: ' . json_encode($e->errors()));
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error al guardar el reporte: ' . $e->getMessage());
            return response()->json([
                'message' => 'Error al guardar el reporte',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}