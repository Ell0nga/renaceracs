<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Nueva Transacción</title>
    <style>
        body { font-family: sans-serif; margin: 20px; }
        form div { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="text"],
        input[type="number"],
        select {
            width: 300px;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        button {
            padding: 10px 15px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
        .alert {
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>

    <h1>Crear Nueva Transacción</h1>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('finanzas.transacciones.store') }}">
        @csrf {{-- ¡Importante! Token CSRF para seguridad en formularios Laravel --}}

        <div>
            <label for="monto">Monto:</label>
            <input type="number" step="0.01" name="monto" id="monto" value="{{ old('monto') }}" required>
            @error('monto')
                <div class="alert alert-danger">{{ $message }}</div>
            @enderror
        </div>

        <div>
            <label for="descripcion">Descripción:</label>
            <input type="text" name="descripcion" id="descripcion" value="{{ old('descripcion') }}">
            @error('descripcion')
                <div class="alert alert-danger">{{ $message }}</div>
            @enderror
        </div>

        <div>
            <label for="tipo_categoria">Tipo de Categoría:</label>
            <select name="tipo_categoria" id="tipo_categoria" required>
                <option value="">Seleccione un tipo</option>
                {{-- Iteramos sobre los casos de tu enum FinanzaCategoriaTipo --}}
                @foreach ($tipos as $tipo)
                    <option value="{{ $tipo->value }}" {{ old('tipo_categoria') == $tipo->value ? 'selected' : '' }}>
                        {{ $tipo->name }} {{-- Mostrará 'INGRESO', 'EGRESO', etc. --}}
                    </option>
                @endforeach
            </select>
            @error('tipo_categoria')
                <div class="alert alert-danger">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit">Guardar Transacción</button>
    </form>

    <br>
    <a href="{{ route('finanzas.transacciones.index') }}">Ver todas las Transacciones</a>

</body>
</html>