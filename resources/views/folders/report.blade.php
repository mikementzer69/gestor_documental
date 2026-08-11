<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Carpetas</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .header p {
            margin: 5px 0 0;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px 10px;
            text-align: left;
        }
        th {
            background-color: #f5f5f5;
            font-weight: bold;
            text-transform: uppercase;
        }
        .text-center {
            text-align: center;
        }
        @media print {
            body {
                padding: 0;
            }
            .no-print {
                display: none;
            }
        }
        .btn-print {
            background-color: #4F46E5;
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 14px;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }
        .btn-print:hover {
            background-color: #4338CA;
        }
    </style>
</head>
<body>
    <div class="no-print" style="text-align: right; margin-bottom: 20px;">
        <button onclick="window.print()" class="btn-print">Imprimir / Guardar como PDF</button>
        <a href="javascript:history.back()" style="margin-left: 10px; color: #666; text-decoration: none;">Volver</a>
    </div>

    <div class="header">
        <h1>Reporte de Estructura de Carpetas</h1>
        <p>Generado el: {{ date('d/m/Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%">ID</th>
                <th width="50%">Ruta Completa</th>
                <th width="45%">Descripción</th>
            </tr>
        </thead>
        <tbody>
            @foreach($folders as $folder)
                <tr>
                    <td class="text-center">{{ $folder->id }}</td>
                    <td>{{ $folder->full_path }}</td>
                    <td>{{ $folder->descripcion ?: 'N/A' }}</td>
                </tr>
            @endforeach
            @if($folders->isEmpty())
                <tr>
                    <td colspan="3" class="text-center">No se encontraron carpetas.</td>
                </tr>
            @endif
        </tbody>
    </table>
</body>
</html>
