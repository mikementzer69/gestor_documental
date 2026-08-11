<?php

namespace App\Http\Controllers;

use App\Models\Folder;
use Illuminate\Http\Request;

class FolderMaintenanceController extends Controller
{
    /**
     * Mostrar la lista de carpetas para mantenimiento.
     */
    public function index()
    {
        // Traer todas las carpetas con su relación parent para armar la ruta
        $folders = Folder::with('parent')->get();
        
        // No usaremos paginación directamente si queremos que sea fácil buscar, 
        // pero podemos pasar todas o usar DataTables. Lo más simple es pasar todas.
        // Si son muchas, es mejor ordenarlas por su nombre o parent_id.
        
        return view('folders.maintenance', compact('folders'));
    }

    /**
     * Actualizar la descripción de una carpeta.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'descripcion' => 'nullable|string|max:500',
        ]);

        $folder = Folder::findOrFail($id);
        $folder->descripcion = $request->descripcion;
        $folder->save();

        return redirect()->back()->with('success', 'Descripción de la carpeta actualizada correctamente.');
    }

    /**
     * Mostrar la vista de reporte para imprimir.
     */
    public function report()
    {
        $folders = Folder::with('parent')->get();
        return view('folders.report', compact('folders'));
    }
}
