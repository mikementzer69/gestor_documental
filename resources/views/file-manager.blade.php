<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestor de Documentos ✨</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body {
            background-color: #fdf4ff;
            background-image: radial-gradient(at 0% 0%, hsla(253,16%,7%,0) 0, transparent 50%), radial-gradient(at 50% 0%, hsla(225,39%,30%,0) 0, transparent 50%), radial-gradient(at 100% 0%, hsla(339,49%,30%,0) 0, transparent 50%);
            background-attachment: fixed;
        }
        .glass-panel {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.07);
        }
        .btn-gradient-blue {
            background: linear-gradient(135deg, #0ea5e9, #3b82f6);
            box-shadow: 0 4px 14px 0 rgba(59, 130, 246, 0.39);
            color: white;
            border: none;
        }
        .btn-gradient-green {
            background: linear-gradient(135deg, #10b981, #059669);
            box-shadow: 0 4px 14px 0 rgba(16, 185, 129, 0.39);
            color: white;
            border: none;
        }
        .btn-gradient-purple {
            background: linear-gradient(135deg, #8b5cf6, #d946ef);
            box-shadow: 0 4px 14px 0 rgba(217, 70, 239, 0.39);
            color: white;
            border: none;
        }
        .btn-gradient-red {
            background: linear-gradient(135deg, #ef4444, #f43f5e);
            box-shadow: 0 4px 14px 0 rgba(239, 68, 68, 0.39);
            color: white;
            border: none;
        }
        .btn-gradient-dark {
            background: linear-gradient(135deg, #334155, #0f172a);
            box-shadow: 0 4px 14px 0 rgba(15, 23, 42, 0.39);
            color: white;
            border: none;
        }
        .folder-icon {
            color: #fbbf24;
            filter: drop-shadow(0 2px 4px rgba(251, 191, 36, 0.4));
        }
        .pdf-icon {
            color: #ef4444;
            filter: drop-shadow(0 2px 4px rgba(239, 68, 68, 0.4));
        }
        .hover-lift {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .hover-lift:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body class="min-h-screen p-8 font-sans">

    <div class="max-w-7xl mx-auto">
        
        @if(session('success'))
            <div class="mb-6 glass-panel border-l-4 p-4 rounded-r-2xl" style="border-left-color: #10b981;">
                <div class="flex items-center">
                    <i class="fas fa-check-circle text-emerald-500 mr-3 text-xl"></i>
                    <p class="text-sm text-emerald-800 font-bold">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        <!-- Menú de Usuario y Bitácora -->
        <div class="flex justify-end items-center mb-8 gap-4">
            <a href="{{ route('reports.index') }}" class="btn-gradient-blue px-5 py-2.5 rounded-full text-xs font-bold transition flex items-center hover-lift">
                <i class="fas fa-chart-pie mr-2 text-lg"></i> Reportes
            </a>

            <a href="{{ route('folders.maintenance') }}" class="btn-gradient-green px-5 py-2.5 rounded-full text-xs font-bold transition flex items-center hover-lift">
                <i class="fas fa-magic mr-2 text-lg"></i> Mantenimiento
            </a>
            
            <a href="{{ route('logs.index') }}" class="btn-gradient-purple px-5 py-2.5 rounded-full text-xs font-bold transition flex items-center hover-lift">
                <i class="fas fa-clipboard-list mr-2 text-lg"></i> Bitácora
            </a>
            
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="bg-white border-2 border-gray-200 text-gray-600 hover:text-red-500 hover:border-red-400 px-5 py-2.5 rounded-full text-xs font-bold transition flex items-center shadow-sm cursor-pointer hover-lift">
                    <i class="fas fa-sign-out-alt mr-2 text-lg"></i> Salir
                </button>
            </form>
        </div>

        @if($errors->any())
            <div class="mb-6 glass-panel border-l-4 p-4 rounded-r-2xl" style="border-left-color: #ef4444;">
                <div class="flex items-center mb-2">
                    <i class="fas fa-exclamation-circle text-rose-500 mr-3 text-xl"></i>
                    <p class="text-sm text-rose-800 font-bold">¡Uy! Algo no salió como esperábamos:</p>
                </div>
                <ul class="list-disc list-inside text-xs text-rose-600 ml-8 space-y-1 font-medium">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        <form action="{{ route('filemanager.index') }}" method="GET" class="mb-10 flex justify-center">
            <div class="relative w-full max-w-2xl">
                <input type="text" name="q" value="{{ $search ?? '' }}" placeholder="🔍 Busca por nombre, cliente o tipo..." 
                       class="w-full rounded-full border-2 border-purple-100 py-4 px-6 pr-14 text-sm focus:outline-none focus:ring-4 focus:ring-purple-200 focus:border-purple-400 shadow-lg bg-white/90 text-gray-700 transition font-medium backdrop-blur-sm">
                <button type="submit" class="absolute right-3 top-2.5 bg-purple-100 hover:bg-purple-200 text-purple-600 rounded-full p-2.5 transition cursor-pointer">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </form>

        @if(isset($search) && $search)
            <div class="mb-8 flex justify-between items-center glass-panel p-5 rounded-2xl">
                <div>
                    <h3 class="text-sm font-extrabold text-purple-800 uppercase tracking-wide">✨ Resultados Mágicos ✨</h3>
                    <p class="text-xs text-purple-600 mt-1 font-medium">Encontramos {{ $documents->count() }} resultados para: <strong>"{{ $search }}"</strong></p>
                </div>
                <a href="{{ route('filemanager.index') }}" class="bg-white/80 border-2 border-purple-200 text-purple-600 px-4 py-2 rounded-full shadow-sm hover:bg-white text-xs font-bold transition hover-lift">
                    <i class="fas fa-broom mr-1"></i> Limpiar
                </a>
            </div>
        @else
            <div class="mb-8 text-sm text-gray-600 flex items-center gap-2 glass-panel px-6 py-4 rounded-full font-medium shadow-sm">
                <a href="{{ route('filemanager.index') }}" class="hover:text-purple-600 transition flex items-center {{ !isset($currentFolder) ? 'text-purple-600 font-bold' : '' }}">
                    <i class="fas fa-home mr-2 text-lg"></i> Inicio
                </a>
                @if(isset($breadcrumbs) && count($breadcrumbs) > 0)
                    @foreach($breadcrumbs as $crumb)
                        <span class="text-gray-300 mx-1"><i class="fas fa-chevron-right text-xs"></i></span>
                        @if($loop->last)
                            <span class="text-gray-800 font-extrabold">{{ $crumb->name }}</span>
                        @else
                            <a href="{{ route('filemanager.folder', $crumb->id) }}" class="hover:text-purple-600 transition">{{ $crumb->name }}</a>
                        @endif
                    @endforeach
                @endif
            </div>

            <div class="mb-10 grid grid-cols-1 lg:grid-cols-2 gap-6">
                
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center glass-panel p-6 rounded-2xl gap-4 hover-lift">
                    <div>
                        <h3 class="text-base font-extrabold text-gray-800 mb-1">Nueva Carpeta 📁</h3>
                        <p class="text-xs font-medium text-gray-500">Mantén todo en perfecto orden.</p>
                    </div>
                    
                    <div class="flex flex-wrap items-center gap-3 w-full sm:w-auto">
                        <form action="{{ route('folders.scan') }}" method="POST">
                            @csrf
                            <button type="submit" onclick="return confirm('¿Desea traer toda la magia desde Google Drive?')" class="btn-gradient-blue px-4 py-2.5 rounded-xl text-xs font-bold transition whitespace-nowrap">
                                <i class="fab fa-google-drive mr-1 text-base"></i> Sincronizar
                            </button>
                        </form>

                        <form action="{{ route('folders.store') }}" method="POST" class="flex items-center gap-2 w-full sm:w-auto">
                            @csrf
                            @if(isset($currentFolder) && $currentFolder)
                                <input type="hidden" name="parent_id" value="{{ $currentFolder->id }}">
                            @endif
                            <input type="text" name="name" placeholder="Ej. 2027" required
                                   class="w-full sm:w-32 md:w-auto text-sm text-gray-700 border-2 border-gray-100 rounded-xl py-2 px-3 focus:outline-none focus:border-purple-300 focus:ring-2 focus:ring-purple-100">
                            
                            <button type="submit" class="btn-gradient-dark px-4 py-2.5 rounded-xl text-xs font-bold transition whitespace-nowrap cursor-pointer">
                                <i class="fas fa-plus mr-1"></i> Crear
                            </button>
                        </form>
                    </div>
                </div>

                @if(isset($currentFolder) && $currentFolder)
                    <div class="glass-panel p-6 rounded-2xl hover-lift">
                        <div class="mb-4">
                            <h3 class="text-base font-extrabold text-gray-800 mb-1">Subir Documento 🚀</h3>
                            <p class="text-xs font-medium text-gray-500">Agrega un PDF a esta carpeta.</p>
                        </div>
                        
                        <form action="{{ route('filemanager.upload') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                            @csrf
                            <input type="hidden" name="folder_id" value="{{ $currentFolder->id }}">
                            
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wide mb-1.5">Tipo *</label>
                                    <select name="document_type" required class="w-full text-xs font-medium text-gray-700 border-2 border-gray-100 rounded-lg p-2 bg-white focus:border-blue-400 focus:ring-2 focus:ring-blue-100 focus:outline-none transition">
                                        <option value="">Elegir...</option>
                                        <option value="Factura">Factura</option>
                                        <option value="Contrato">Contrato</option>
                                        <option value="Credito Fiscal">Crédito Fiscal</option>
                                        <option value="Planilla">Planilla</option>
                                        <option value="Identificacion">DUI / Pasaporte</option>
                                        <option value="Otro">Otro</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wide mb-1.5">Cliente 👤</label>
                                    <input type="text" name="entity_name" placeholder="Ej: Juan Pérez" class="w-full text-xs font-medium text-gray-700 border-2 border-gray-100 rounded-lg p-2 bg-white focus:border-blue-400 focus:ring-2 focus:ring-blue-100 focus:outline-none transition">
                                </div>

                                <div>
                                    <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wide mb-1.5">Vence ⏰</label>
                                    <input type="date" name="expiry_date" class="w-full text-xs font-medium text-gray-700 border-2 border-gray-100 rounded-lg p-2 bg-white focus:border-blue-400 focus:ring-2 focus:ring-blue-100 focus:outline-none transition">
                                </div>
                            </div>

                            <div class="flex flex-col sm:flex-row items-center justify-between pt-2 gap-3">
                                <input type="file" name="document" accept=".pdf" required
                                       class="w-full sm:w-auto text-xs font-medium text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-bold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 transition cursor-pointer">
                                
                                <button type="submit" class="w-full sm:w-auto btn-gradient-purple px-6 py-2.5 rounded-full text-xs font-bold transition whitespace-nowrap cursor-pointer">
                                    <i class="fas fa-cloud-upload-alt mr-2 text-base"></i> Subir PDF
                                </button>
                            </div>
                        </form>
                    </div>
                @else
                    <div class="glass-panel p-6 rounded-2xl flex flex-col items-center justify-center text-center h-full hover-lift">
                        <span class="text-5xl mb-4 folder-icon"><i class="fas fa-folder-open"></i></span>
                        <h3 class="text-base font-extrabold text-gray-800">¡Sube la Magia! ✨</h3>
                        <p class="text-xs font-medium text-gray-500 mt-2">Crea o entra a una carpeta para habilitar la subida de PDFs.</p>
                    </div>
                @endif
                
            </div>
        @endif

        @if(!isset($search) || !$search)
            @if(isset($folders) && $folders->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-5">
                    @foreach($folders as $folder)
                        <div class="glass-panel p-5 rounded-2xl hover-lift group relative flex items-center justify-between">
                            
                            <a href="{{ route('filemanager.folder', $folder->id) }}" class="flex items-center gap-3 truncate w-full pr-8">
                                <span class="text-4xl flex-shrink-0 folder-icon group-hover:scale-110 transition-transform">
                                    <i class="fas fa-folder"></i>
                                </span>
                                <span class="text-sm font-bold text-gray-700 truncate" title="{{ $folder->name }}">
                                    {{ $folder->name }}
                                </span>
                            </a>

                            <form action="{{ route('filemanager.deleteFolder', $folder->id) }}" method="POST" class="absolute right-4 top-1/2 transform -translate-y-1/2" onsubmit="return confirm('⚠️ ¿Estás seguro de desaparecer esta carpeta y TODOS sus secretos permanentemente?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-8 h-8 rounded-full bg-white border border-gray-100 flex items-center justify-center text-gray-400 hover:text-white hover:bg-rose-500 hover:border-rose-500 transition cursor-pointer shadow-sm" title="Eliminar Carpeta">
                                    <i class="fas fa-trash-alt text-xs"></i>
                                </button>
                            </form>
                        </div>
                    @endforeach
                </div>
            @else
                @if(isset($documents) && $documents->count() == 0)
                    <div class="text-center py-16 glass-panel rounded-3xl">
                        <i class="fas fa-wind text-6xl text-gray-300 mb-4"></i>
                        <h3 class="text-lg font-bold text-gray-600">Todo muy tranquilo por aquí...</h3>
                        <p class="text-sm text-gray-400 mt-2 font-medium">Esta carpeta está vacía.</p>
                    </div>
                @endif
            @endif
        @endif

        @if(isset($search) && $search && $documents->count() == 0)
            <div class="text-center py-16 glass-panel rounded-3xl mt-4">
                <span class="text-6xl text-purple-200 mb-4 inline-block"><i class="fas fa-ghost"></i></span>
                <h3 class="text-lg font-bold text-gray-600">¡Ups! No hay nada por aquí.</h3>
                <p class="text-sm text-gray-400 mt-2 font-medium">Prueba con otras palabras o revisa la ortografía.</p>
            </div>
        @endif

        @if(isset($documents) && $documents->count() > 0)
            <div class="mt-12">
                <h3 class="text-sm font-extrabold text-gray-800 uppercase tracking-widest mb-6 flex items-center">
                    <span class="bg-gradient-to-r from-red-500 to-rose-500 w-2 h-6 rounded-full mr-3 inline-block"></span>
                    {{ (isset($search) && $search) ? 'Documentos Mágicos' : 'Tus Archivos' }}
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($documents as $doc)
                        <div class="glass-panel p-5 rounded-2xl flex flex-col hover-lift relative overflow-hidden group">
                            
                            <div class="absolute -right-4 -top-4 text-gray-50 opacity-50 text-8xl transform rotate-12 pointer-events-none group-hover:scale-110 transition-transform">
                                <i class="fas fa-file-pdf"></i>
                            </div>

                            <div class="flex items-center gap-4 mb-4 relative z-10">
                                <span class="text-4xl pdf-icon"><i class="fas fa-file-pdf"></i></span>
                                <div class="truncate">
                                    <p class="text-sm font-extrabold text-gray-800 truncate" title="{{ $doc->renamed_title ?? $doc->title }}">
                                        {{ $doc->renamed_title ?? $doc->title }}
                                    </p>
                                    <p class="text-[10px] text-gray-400 font-medium truncate mt-1">Ori: {{ $doc->title }}</p>
                                </div>
                            </div>
                            
                            <div class="flex flex-wrap gap-2 mt-auto relative z-10">
                                @if($doc->document_type)
                                    <span class="bg-blue-100 text-blue-700 text-[10px] font-bold px-2.5 py-1 rounded-full uppercase tracking-wider">
                                        {{ $doc->document_type }}
                                    </span>
                                @endif
                                @if($doc->entity_name)
                                    <span class="bg-purple-100 text-purple-700 text-[10px] font-bold px-2.5 py-1 rounded-full uppercase tracking-wider truncate max-w-[130px]">
                                        <i class="fas fa-user-astronaut mr-1"></i>{{ $doc->entity_name }}
                                    </span>
                                @endif
                                @if($doc->expiry_date)
                                    <span class="bg-rose-100 text-rose-700 text-[10px] font-bold px-2.5 py-1 rounded-full uppercase tracking-wider">
                                        <i class="fas fa-hourglass-half mr-1"></i>{{ \Carbon\Carbon::parse($doc->expiry_date)->format('d/m/Y') }}
                                    </span>
                                @endif
                            </div>
                            
                            <div class="mt-5 pt-4 border-t border-gray-100 flex gap-3 relative z-10">
                                <button onclick="openPreviewModal('{{ route('filemanager.preview', $doc->id) }}', '{{ $doc->renamed_title ?? $doc->title }}')" class="flex-1 text-center bg-indigo-50 hover:bg-indigo-100 text-indigo-700 py-2 rounded-xl text-xs font-bold transition flex justify-center items-center gap-2 cursor-pointer">
                                    <i class="fas fa-eye text-sm"></i> Ver
                                </button>
                                <a href="{{ route('filemanager.preview', $doc->id) }}" download="{{ $doc->renamed_title ?? $doc->title }}" class="w-10 h-10 bg-emerald-50 hover:bg-emerald-100 text-emerald-600 rounded-xl flex justify-center items-center transition cursor-pointer" title="Descargar">
                                    <i class="fas fa-download"></i>
                                </a>
                                <form action="{{ route('filemanager.deleteFile', $doc->id) }}" method="POST" class="flex" onsubmit="return confirm('¿Seguro que deseas eliminar esta maravilla permanentemente?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-10 h-10 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-xl flex justify-center items-center transition cursor-pointer" title="Eliminar">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

    </div>

    <!-- Modal de Previsualización -->
    <div id="previewModal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 transition-all duration-300" style="background: rgba(15, 23, 42, 0.8); backdrop-filter: blur(8px);">
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-6xl h-[88vh] flex flex-col overflow-hidden transform scale-95 opacity-0 transition-all duration-300" id="previewModalContent">
            <!-- Header -->
            <div class="flex justify-between items-center bg-gray-50/80 backdrop-blur-md border-b border-gray-100 px-8 py-5">
                <div class="flex items-center gap-4">
                    <div class="bg-rose-100 text-rose-500 p-2.5 rounded-xl shadow-inner">
                        <i class="fas fa-file-pdf text-2xl"></i>
                    </div>
                    <h3 id="previewTitle" class="text-xl font-extrabold text-gray-800 truncate max-w-3xl tracking-tight">Cargando Magia...</h3>
                </div>
                <div class="flex gap-3">
                    <a id="previewDownloadBtn" href="#" download class="w-10 h-10 bg-white border border-gray-200 text-gray-500 hover:text-emerald-500 hover:border-emerald-200 hover:bg-emerald-50 rounded-full flex items-center justify-center transition-all shadow-sm cursor-pointer" title="Descargar PDF">
                        <i class="fas fa-download"></i>
                    </a>
                    <button onclick="closePreviewModal()" class="w-10 h-10 bg-white border border-gray-200 text-gray-500 hover:text-rose-500 hover:border-rose-200 hover:bg-rose-50 rounded-full flex items-center justify-center transition-all shadow-sm cursor-pointer" title="Cerrar">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>
            </div>
            <!-- Contenido del PDF -->
            <div class="flex-1 bg-gray-100/50 relative">
                <!-- Loader -->
                <div id="previewLoader" class="absolute inset-0 flex flex-col items-center justify-center">
                    <div class="relative">
                        <i class="fas fa-circle-notch fa-spin text-5xl text-purple-500"></i>
                        <i class="fas fa-magic absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 text-purple-300 text-sm"></i>
                    </div>
                    <p class="text-purple-600 text-sm font-bold mt-4 tracking-wide">Invocando el documento...</p>
                </div>
                <iframe id="previewIframe" class="w-full h-full relative z-10 hidden" src="" onload="document.getElementById('previewLoader').classList.add('hidden'); this.classList.remove('hidden');" frameborder="0" allowfullscreen></iframe>
            </div>
        </div>
    </div>

    <script>
        function openPreviewModal(url, title) {
            document.getElementById('previewTitle').innerText = title;
            document.getElementById('previewIframe').src = url + '#toolbar=0'; 
            document.getElementById('previewDownloadBtn').href = url;
            document.getElementById('previewIframe').classList.add('hidden');
            document.getElementById('previewLoader').classList.remove('hidden');
            
            const modal = document.getElementById('previewModal');
            const content = document.getElementById('previewModalContent');
            
            modal.classList.remove('hidden');
            
            // Animación suave
            setTimeout(() => {
                modal.classList.remove('opacity-0');
                content.classList.remove('scale-95');
                content.classList.remove('opacity-0');
                content.classList.add('scale-100');
                content.classList.add('opacity-100');
            }, 10);
            
            document.body.style.overflow = 'hidden';
        }

        function closePreviewModal() {
            const modal = document.getElementById('previewModal');
            const content = document.getElementById('previewModalContent');
            
            content.classList.remove('scale-100');
            content.classList.remove('opacity-100');
            content.classList.add('scale-95');
            content.classList.add('opacity-0');
            
            setTimeout(() => {
                modal.classList.add('hidden');
                document.getElementById('previewIframe').src = '';
            }, 300);
            
            document.body.style.overflow = 'auto';
        }
        
        document.getElementById('previewModal').addEventListener('click', function(e) {
            if (e.target === this) closePreviewModal();
        });
        
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && !document.getElementById('previewModal').classList.contains('hidden')) {
                closePreviewModal();
            }
        });
    </script>
</body>
</html>