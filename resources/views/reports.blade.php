<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Documentos</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        /* Estilos específicos para impresión */
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background-color: white !important;
                padding: 0 !important;
            }
            .shadow-sm {
                box-shadow: none !important;
            }
            .border {
                border-color: #ddd !important;
            }
            .print-break-inside-avoid {
                break-inside: avoid;
            }
            h1, h2, h3, th, td {
                color: #000 !important;
            }
            .print-container {
                max-width: 100% !important;
                margin: 0 !important;
            }
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen p-8 font-sans">

    <div class="max-w-6xl mx-auto print-container">
        
        <!-- Encabezado (Oculto en parte al imprimir) -->
        <div class="flex justify-between items-center mb-8 no-print">
            <a href="{{ route('filemanager.index') }}" class="bg-white border border-gray-200 text-gray-700 px-4 py-2 rounded-lg shadow-sm hover:bg-gray-50 text-sm font-medium transition flex items-center gap-2">
                <i class="fas fa-arrow-left"></i> Volver al Gestor
            </a>
            
            <button onclick="window.print()" class="bg-blue-600 text-white px-5 py-2 rounded-lg shadow-sm hover:bg-blue-700 text-sm font-bold transition flex items-center gap-2 cursor-pointer">
                <i class="fas fa-print"></i> Imprimir Reporte
            </button>
        </div>

        <!-- Título del Reporte -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-8 mb-8 text-center">
            <h1 class="text-3xl font-bold text-gray-800 uppercase tracking-wider mb-2">Reporte de Archivos Físicos y Digitales</h1>
            <p class="text-gray-500">Gestor Documental Inteligente</p>
            <div class="mt-4 pt-4 border-t border-gray-100 flex justify-center gap-8">
                <div class="text-center">
                    <p class="text-xs text-gray-400 font-bold uppercase">Total Documentos</p>
                    <p class="text-2xl font-black text-blue-600">{{ $totalDocuments }}</p>
                </div>
                <div class="text-center">
                    <p class="text-xs text-gray-400 font-bold uppercase">Categorías</p>
                    <p class="text-2xl font-black text-gray-700">{{ $documentsByType->count() }}</p>
                </div>
                <div class="text-center">
                    <p class="text-xs text-gray-400 font-bold uppercase">Fecha de Emisión</p>
                    <p class="text-lg font-bold text-gray-700 mt-1">{{ \Carbon\Carbon::now()->format('d/m/Y') }}</p>
                </div>
            </div>
        </div>

        <!-- Contenido Agrupado -->
        @forelse($documentsByType as $type => $documents)
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm mb-8 overflow-hidden print-break-inside-avoid">
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                    <h2 class="text-lg font-bold text-gray-800 uppercase flex items-center gap-2">
                        <i class="fas fa-folder-open text-blue-500"></i> {{ $type ?: 'Sin Categoría' }}
                    </h2>
                    <span class="bg-blue-100 text-blue-800 text-xs font-bold px-3 py-1 rounded-full">
                        {{ $documents->count() }} archivos
                    </span>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-white border-b border-gray-100 text-xs uppercase tracking-wider text-gray-400 font-bold">
                                <th class="py-3 px-6">Archivo</th>
                                <th class="py-3 px-6">Cliente / Entidad</th>
                                <th class="py-3 px-6">Ubicación (Carpeta)</th>
                                <th class="py-3 px-6 text-right">Fecha Subida</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm text-gray-700 divide-y divide-gray-50">
                            @foreach($documents as $doc)
                                <tr class="hover:bg-gray-50">
                                    <td class="py-3 px-6">
                                        <div class="font-bold text-gray-800 truncate max-w-xs" title="{{ $doc->renamed_title ?? $doc->title }}">
                                            {{ $doc->renamed_title ?? $doc->title }}
                                        </div>
                                        <div class="text-xs text-gray-400 truncate max-w-xs">{{ $doc->title }}</div>
                                    </td>
                                    <td class="py-3 px-6 font-medium">
                                        {{ $doc->entity_name ?: 'N/A' }}
                                    </td>
                                    <td class="py-3 px-6">
                                        <span class="inline-flex items-center gap-1 text-gray-600 bg-gray-100 px-2 py-0.5 rounded text-xs">
                                            <i class="fas fa-folder text-gray-400"></i> {{ $doc->folder->name ?? 'Raíz' }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-6 text-right text-gray-500">
                                        {{ $doc->created_at->format('d/m/Y') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @empty
            <div class="text-center py-12 bg-white rounded-xl border border-gray-200">
                <i class="fas fa-box-open text-4xl text-gray-300 mb-3"></i>
                <p class="text-gray-500 font-medium">No hay documentos en el sistema para generar un reporte.</p>
            </div>
        @endforelse

    </div>

</body>
</html>
