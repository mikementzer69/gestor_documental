<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bitácora de Actividad - Gestor Documental</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="bg-gray-50 min-h-screen p-8 font-sans">

    <div class="max-w-6xl mx-auto">
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-800"><i class="fas fa-history text-indigo-500 mr-2"></i> Bitácora de Actividad</h1>
                <p class="text-sm text-gray-500 mt-1">Historial completo de movimientos en el gestor documental.</p>
            </div>
            
            <a href="{{ route('filemanager.index') }}" class="bg-white border border-gray-200 text-gray-700 px-4 py-2 rounded-lg shadow-sm hover:bg-gray-50 text-sm font-medium transition flex items-center gap-2">
                <i class="fas fa-arrow-left"></i> Volver al Gestor
            </a>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200 text-xs uppercase tracking-wider text-gray-500 font-bold">
                            <th class="py-4 px-6">Fecha y Hora</th>
                            <th class="py-4 px-6">Usuario</th>
                            <th class="py-4 px-6">Acción</th>
                            <th class="py-4 px-6">Detalle</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm text-gray-700 divide-y divide-gray-100">
                        @forelse($logs as $log)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="py-4 px-6 whitespace-nowrap">
                                    <div class="font-medium text-gray-800">{{ $log->created_at->format('d/m/Y') }}</div>
                                    <div class="text-xs text-gray-400">{{ $log->created_at->format('h:i:s A') }}</div>
                                </td>
                                <td class="py-4 px-6 font-medium text-gray-900">
                                    <i class="fas fa-user-circle text-gray-400 mr-1"></i> {{ $log->user->name ?? 'Usuario Eliminado' }}
                                </td>
                                <td class="py-4 px-6">
                                    @if($log->action == 'UPLOAD')
                                        <span class="bg-blue-100 text-blue-700 px-2 py-1 rounded text-xs font-bold uppercase"><i class="fas fa-upload mr-1"></i> Subida</span>
                                    @elseif($log->action == 'DELETE_FILE')
                                        <span class="bg-red-100 text-red-700 px-2 py-1 rounded text-xs font-bold uppercase"><i class="fas fa-file-excel mr-1"></i> Archivo Eliminado</span>
                                    @elseif($log->action == 'CREATE_FOLDER')
                                        <span class="bg-green-100 text-green-700 px-2 py-1 rounded text-xs font-bold uppercase"><i class="fas fa-folder-plus mr-1"></i> Carpeta Creada</span>
                                    @elseif($log->action == 'DELETE_FOLDER')
                                        <span class="bg-orange-100 text-orange-700 px-2 py-1 rounded text-xs font-bold uppercase"><i class="fas fa-folder-minus mr-1"></i> Carpeta Eliminada</span>
                                    @else
                                        <span class="bg-gray-100 text-gray-700 px-2 py-1 rounded text-xs font-bold uppercase">{{ $log->action }}</span>
                                    @endif
                                </td>
                                <td class="py-4 px-6 text-gray-600">
                                    {{ $log->description }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-12 text-center text-gray-400">
                                    <i class="fas fa-clipboard-list text-4xl mb-3"></i>
                                    <p>Aún no hay actividad registrada en el sistema.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($logs->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                    {{ $logs->links() }}
                </div>
            @endif
        </div>
    </div>

</body>
</html>
