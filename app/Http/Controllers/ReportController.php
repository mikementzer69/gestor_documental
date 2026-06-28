<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Document;

class ReportController extends Controller
{
    public function index()
    {
        // Obtener todos los documentos con su respectiva carpeta
        $documents = Document::with('folder')->orderBy('created_at', 'desc')->get();
        
        // Agruparlos por el tipo de documento (Factura, Contrato, etc.)
        $documentsByType = $documents->groupBy('document_type');
        
        // Total general
        $totalDocuments = $documents->count();

        return view('reports', compact('documentsByType', 'totalDocuments'));
    }
}
