<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-transparent bg-clip-text bg-gradient-to-r from-purple-600 to-pink-500 leading-tight" style="background-image: linear-gradient(to right, #9333ea, #ec4899); -webkit-background-clip: text; color: transparent;">
            ✨ {{ __('Mantenimiento de Carpetas') }} ✨
        </h2>
    </x-slot>

    <div class="py-12" style="background-color: #f8fafc;">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden sm:rounded-2xl" style="box-shadow: 0 10px 25px -5px rgba(236, 72, 153, 0.1), 0 8px 10px -6px rgba(236, 72, 153, 0.1);">
                <div class="p-8 text-gray-900">
                    
                    @if (session('success'))
                        <div class="mb-6 border-l-4 px-4 py-3 rounded-r shadow-sm" style="background-color: #ecfdf5; border-color: #10b981; color: #065f46;" role="alert">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-2 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <span class="block sm:inline font-medium">{{ session('success') }}</span>
                            </div>
                        </div>
                    @endif

                    <div class="flex justify-end mb-6">
                        <a href="{{ route('folders.report') }}" target="_blank" class="inline-flex items-center px-5 py-2.5 border border-transparent rounded-full font-bold text-xs text-white uppercase tracking-wider transition ease-in-out duration-150" style="background: linear-gradient(135deg, #0ea5e9, #3b82f6); box-shadow: 0 4px 14px 0 rgba(59, 130, 246, 0.39);">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                            Generar Reporte
                        </a>
                    </div>

                    <div class="overflow-x-auto rounded-xl border border-gray-100 shadow-sm">
                        <table class="min-w-full bg-white">
                            <thead>
                                <tr style="background: linear-gradient(90deg, #fdf4ff, #f0fdfa);">
                                    <th class="px-6 py-4 text-left text-xs font-extrabold uppercase tracking-wider" style="color: #c026d3;">ID</th>
                                    <th class="px-6 py-4 text-left text-xs font-extrabold uppercase tracking-wider" style="color: #0d9488;">Ruta Completa</th>
                                    <th class="px-6 py-4 text-left text-xs font-extrabold uppercase tracking-wider" style="color: #2563eb;" colspan="2">Descripción y Acción</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach ($folders as $folder)
                                    <tr class="hover:bg-gray-50 transition-colors duration-150">
                                        <td class="px-6 py-5 whitespace-nowrap text-sm font-medium" style="color: #d946ef;">
                                            #{{ $folder->id }}
                                        </td>
                                        <td class="px-6 py-5 text-sm font-bold text-gray-700">
                                            <div class="flex items-center">
                                                <svg class="w-5 h-5 mr-2 text-yellow-400" fill="currentColor" viewBox="0 0 20 20"><path d="M2 6a2 2 0 012-2h5l2 2h5a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"></path></svg>
                                                {{ $folder->full_path }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-5 text-sm text-gray-500" colspan="2">
                                            <form action="{{ route('folders.update', $folder->id) }}" method="POST" class="flex items-center space-x-4">
                                                @csrf
                                                @method('PUT')
                                                <input type="text" name="descripcion" value="{{ $folder->descripcion }}" class="focus:ring-2 focus:outline-none rounded-lg shadow-inner sm:text-sm w-full min-w-[250px] transition-all" style="border: 1px solid #e2e8f0; padding: 0.5rem 1rem; border-color: #cbd5e1; focus:border-color: #8b5cf6;" placeholder="✍️ Escribe una descripción genial...">
                                                <button type="submit" class="inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-lg font-bold text-xs text-white uppercase tracking-widest transition-all ease-in-out duration-200" style="margin-left: 12px; flex-shrink: 0; white-space: nowrap; background: linear-gradient(135deg, #8b5cf6, #d946ef); box-shadow: 0 4px 10px 0 rgba(217, 70, 239, 0.3);">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                    Guardar
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        
                        @if($folders->isEmpty())
                            <div class="p-12 text-center">
                                <svg class="w-16 h-16 mx-auto mb-4" style="color: #fca5a5;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                                <p class="text-lg font-medium" style="color: #64748b;">¡Aún no hay carpetas aquí!</p>
                                <p class="text-sm" style="color: #94a3b8;">Sincroniza con Google Drive</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
