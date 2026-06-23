<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestor de Documentos</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="bg-gray-50 min-h-screen p-8 font-sans">

    <div class="max-w-7xl mx-auto">
        
        @if(session('success'))
            <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-r-lg shadow-sm">
                <div class="flex items-center">
                    <i class="fas fa-check-circle text-green-500 mr-3 text-lg"></i>
                    <p class="text-sm text-green-700 font-medium">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if($errors->any())
            <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg shadow-sm">
                <div class="flex items-center mb-2">
                    <i class="fas fa-exclamation-circle text-red-500 mr-3 text-lg"></i>
                    <p class="text-sm text-red-700 font-bold">El sistema detectó algunos inconvenientes:</p>
                </div>
                <ul class="list-disc list-inside text-xs text-red-600 ml-8 space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        <form action="{{ route('filemanager.index') }}" method="GET" class="mb-8 flex justify-center">
            <div class="relative w-full max-w-2xl">
                <input type="text" name="q" value="{{ $search ?? '' }}" placeholder="Buscar documentos por nombre, cliente o tipo..." 
                       class="w-full rounded-full border border-gray-200 py-3 px-6 pr-12 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 shadow-sm bg-white text-gray-700 transition">
                <button type="submit" class="absolute right-5 top-3.5 text-blue-500 hover:text-blue-700 transition cursor-pointer">
                    <i class="fas fa-search text-lg"></i>
                </button>
            </div>
        </form>

        @if(isset($search) && $search)
            <div class="mb-6 flex justify-between items-center bg-blue-50 border border-blue-100 p-4 rounded-xl shadow-sm">
                <div>
                    <h3 class="text-sm font-bold text-blue-800">Resultados de búsqueda</h3>
                    <p class="text-xs text-blue-600 mt-1">Buscando: <strong>"{{ $search }}"</strong> ({{ $documents->count() }} resultados encontrados)</p>
                </div>
                <a href="{{ route('filemanager.index') }}" class="bg-white border border-blue-200 text-blue-600 px-4 py-2 rounded-lg shadow-sm hover:bg-blue-100 text-xs font-bold transition">
                    <i class="fas fa-times mr-1"></i> Limpiar búsqueda
                </a>
            </div>
        @else
            <div class="flex justify-between items-center mb-6 text-sm text-gray-500 border-b pb-4">
                <button class="flex items-center gap-2 border border-gray-200 rounded-lg px-4 py-1.5 bg-white hover:bg-gray-50 shadow-sm">
                    <i class="fas fa-sort-amount-down text-gray-400"></i> 
                    <span class="font-medium text-gray-700">Nombre</span> 
                    <i class="fas fa-chevron-down text-xs text-gray-400"></i>
                </button>
                <div class="flex gap-2">
                    <button class="p-2 border border-gray-200 rounded-lg bg-white text-gray-700 shadow-sm"><i class="fas fa-th-list"></i></button>
                    <button class="p-2 border border-gray-200 rounded-lg bg-white text-gray-400 shadow-sm"><i class="fas fa-ellipsis-h"></i></button>
                </div>
            </div>

            <div class="mb-6 text-sm text-gray-500 flex items-center gap-2">
                <a href="{{ route('filemanager.index') }}" class="hover:text-blue-600 font-medium {{ !isset($currentFolder) ? 'text-blue-600' : '' }}">
                    <i class="fas fa-home mr-1"></i> Raíz
                </a>
                @if(isset($currentFolder) && $currentFolder)
                    <span class="text-gray-300">/</span>
                    <span class="text-gray-700 font-semibold">{{ $currentFolder->name }}</span>
                @endif
            </div>

            <div class="mb-8 grid grid-cols-1 lg:grid-cols-2 gap-4">
                
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center bg-white p-4 rounded-xl border border-gray-200 shadow-sm gap-4">
                    <div>
                        <h3 class="text-sm font-bold text-gray-700">Nueva carpeta</h3>
                        <p class="text-xs text-gray-400">Organiza tus documentos.</p>
                    </div>
                    
                    <form action="{{ route('folders.store') }}" method="POST" class="flex items-center gap-2 w-full sm:w-auto">
                        @csrf
                        <input type="text" name="name" placeholder="Nombre..." required
                               class="w-full sm:w-32 md:w-auto text-sm text-gray-700 border border-gray-200 rounded-lg py-2 px-3 focus:outline-none focus:ring-2 focus:ring-gray-200 focus:border-gray-400">
                        
                        <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded-lg hover:bg-gray-900 text-sm font-medium transition shadow-sm whitespace-nowrap">
                            <i class="fas fa-folder-plus mr-1"></i> Crear
                        </button>
                    </form>
                </div>

                @if(isset($currentFolder) && $currentFolder)
                    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
                        <div class="mb-3">
                            <h3 class="text-sm font-bold text-gray-700">Subir y clasificar documento</h3>
                        </div>
                        
                        <form action="{{ route('filemanager.upload') }}" method="POST" enctype="multipart/form-data" class="space-y-3">
                            @csrf
                            <input type="hidden" name="folder_id" value="{{ $currentFolder->id }}">
                            
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                <div>
                                    <label class="block text-[10px] font-semibold text-gray-500 uppercase mb-1">Tipo *</label>
                                    <select name="document_type" required class="w-full text-xs text-gray-700 border border-gray-200 rounded p-1.5 bg-gray-50 focus:ring-1 focus:ring-blue-400 focus:outline-none">
                                        <option value="">Seleccione...</option>
                                        <option value="Factura">Factura</option>
                                        <option value="Contrato">Contrato</option>
                                        <option value="Credito Fiscal">Crédito Fiscal</option>
                                        <option value="Planilla">Planilla</option>
                                        <option value="Identificacion">DUI / Pasaporte</option>
                                        <option value="Otro">Otro</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-[10px] font-semibold text-gray-500 uppercase mb-1">Cliente</label>
                                    <input type="text" name="entity_name" placeholder="Ej: Juan Pérez" class="w-full text-xs text-gray-700 border border-gray-200 rounded p-1.5 bg-gray-50 focus:ring-1 focus:ring-blue-400 focus:outline-none">
                                </div>

                                <div>
                                    <label class="block text-[10px] font-semibold text-gray-500 uppercase mb-1">Vence (Opcional)</label>
                                    <input type="date" name="expiry_date" class="w-full text-xs text-gray-700 border border-gray-200 rounded p-1.5 bg-gray-50 focus:ring-1 focus:ring-blue-400 focus:outline-none">
                                </div>
                            </div>

                            <div class="flex flex-col sm:flex-row items-center justify-between pt-2 border-t border-gray-100 gap-2">
                                <input type="file" name="document" accept=".pdf" required
                                       class="w-full sm:w-auto text-xs text-gray-500 file:mr-2 file:py-1 file:px-2 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer">
                                
                                <button type="submit" class="w-full sm:w-auto bg-blue-600 text-white px-4 py-1.5 rounded hover:bg-blue-700 text-xs font-medium transition shadow-sm whitespace-nowrap">
                                    <i class="fas fa-cloud-upload-alt mr-1"></i> Subir a Drive
                                </button>
                            </div>
                        </form>
                    </div>
                @else
                    <div class="bg-gray-50 p-4 rounded-xl border border-gray-200 border-dashed flex flex-col items-center justify-center text-center h-full">
                        <span class="text-gray-300 text-3xl mb-2"><i class="fas fa-folder-open"></i></span>
                        <h3 class="text-sm font-bold text-gray-600">Sube tus documentos con orden</h3>
                        <p class="text-xs text-gray-400 mt-1">Crea o entra a una carpeta para habilitar la subida de PDFs.</p>
                    </div>
                @endif
                
            </div>
        @endif

        @if(!isset($search) || !$search)
            @if(isset($folders) && $folders->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
                    @foreach($folders as $folder)
                        <div class="flex items-center justify-between p-4 bg-white border border-gray-200 rounded-xl shadow-sm hover:shadow-md hover:border-blue-300 transition group relative">
                            
                            <a href="{{ route('filemanager.folder', $folder->id) }}" class="flex items-center gap-3 truncate w-full pr-6">
                                <span class="text-gray-400 text-xl flex-shrink-0 group-hover:text-yellow-500 transition">
                                    <i class="fas fa-folder"></i>
                                </span>
                                <span class="text-sm font-medium text-gray-700 truncate" title="{{ $folder->name }}">
                                    {{ $folder->name }}
                                </span>
                            </a>

                            <button class="text-gray-400 hover:text-gray-600 absolute right-3 top-4 px-1" title="Opciones">
                                <i class="fas fa-ellipsis-v text-xs"></i>
                            </button>
                        </div>
                    @endforeach
                </div>
            @else
                @if(isset($documents) && $documents->count() == 0)
                    <div class="text-center py-12 text-gray-400">
                        <i class="fas fa-folder-open text-4xl mb-3"></i>
                        <p class="text-sm">Esta carpeta está vacía.</p>
                    </div>
                @endif
            @endif
        @endif

        @if(isset($search) && $search && $documents->count() == 0)
            <div class="text-center py-12 text-gray-400 bg-white rounded-xl border border-gray-200 mt-4 shadow-sm">
                <i class="fas fa-search-minus text-4xl mb-3 text-blue-200"></i>
                <p class="text-sm text-gray-500">No se encontraron resultados para "{{ $search }}".</p>
                <p class="text-xs mt-1">Prueba con otras palabras, tipos de documento o revisa la ortografía.</p>
            </div>
        @endif

        @if(isset($documents) && $documents->count() > 0)
            <div class="mt-10">
                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4">
                    {{ (isset($search) && $search) ? 'Documentos Encontrados' : 'Archivos PDFs' }}
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($documents as $doc)
                        <div class="flex flex-col p-4 bg-white border border-gray-100 rounded-xl shadow-sm hover:shadow-md transition">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center gap-3 truncate">
                                    <span class="text-red-500 text-2xl"><i class="fas fa-file-pdf"></i></span>
                                    <div class="truncate">
                                        <p class="text-sm font-bold text-gray-700 truncate" title="{{ $doc->renamed_title ?? $doc->title }}">
                                            {{ $doc->renamed_title ?? $doc->title }}
                                        </p>
                                        <p class="text-xs text-gray-400 truncate mt-0.5">Original: {{ $doc->title }}</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="flex flex-wrap gap-2 mt-2 pt-3 border-t border-gray-50">
                                @if($doc->document_type)
                                    <span class="bg-blue-50 text-blue-600 text-[10px] font-bold px-2 py-1 rounded uppercase">
                                        {{ $doc->document_type }}
                                    </span>
                                @endif
                                @if($doc->entity_name)
                                    <span class="bg-gray-100 text-gray-600 text-[10px] font-bold px-2 py-1 rounded uppercase truncate max-w-[120px]">
                                        <i class="fas fa-building mr-1"></i>{{ $doc->entity_name }}
                                    </span>
                                @endif
                                @if($doc->expiry_date)
                                    <span class="bg-red-50 text-red-600 text-[10px] font-bold px-2 py-1 rounded uppercase">
                                        <i class="fas fa-clock mr-1"></i>Vence: {{ \Carbon\Carbon::parse($doc->expiry_date)->format('d/m/Y') }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

    </div>

</body>
</html>