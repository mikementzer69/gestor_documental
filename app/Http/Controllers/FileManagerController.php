<?php

namespace App\Http\Controllers;

use App\Models\Folder;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Smalot\PdfParser\Parser; // <-- La librería mágica instalada

class FileManagerController extends Controller
{
    public function index(Request $request, $folderId = null)
    {
        // Alertas de Vencimiento Críticas
        $expiredDocuments = Document::whereNotNull('expiry_date')
            ->where('expiry_date', '<', Carbon::today())
            ->get();

        $soonToExpireDocuments = Document::whereNotNull('expiry_date')
            ->where('expiry_date', '>=', Carbon::today())
            ->where('expiry_date', '<=', Carbon::today()->addDays(30))
            ->orderBy('expiry_date', 'asc')
            ->get();

        // Lógica del Buscador Inteligente
        $search = $request->input('q');

        if ($search) {
            $currentFolder = null;
            $folders = collect(); 
            
            // Ahora busca en títulos, tipos, clientes y ADENTRO DEL PDF (content)
            $documents = Document::where('title', 'LIKE', "%{$search}%")
                ->orWhere('renamed_title', 'LIKE', "%{$search}%")
                ->orWhere('entity_name', 'LIKE', "%{$search}%")
                ->orWhere('document_type', 'LIKE', "%{$search}%")
                ->orWhere('content', 'LIKE', "%{$search}%") 
                ->get();
        } else {
            $currentFolder = $folderId ? Folder::findOrFail($folderId) : null;
            $folders = Folder::where('parent_id', $folderId)->get();
            $documents = Document::where('folder_id', $folderId)->get();
        }

        return view('file-manager', compact(
            'currentFolder', 
            'folders', 
            'documents', 
            'search', 
            'expiredDocuments', 
            'soonToExpireDocuments'
        ));
    }

    public function storeFolder(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        Folder::create([
            'name' => $request->name,
        ]);

        return back()->with('success', 'Carpeta creada exitosamente.');
    }

    public function upload(Request $request)
    {
        $request->validate([
            'document' => 'required|mimes:pdf|max:10240',
            'folder_id' => 'required|exists:folders,id',
            'document_type' => 'required|string',
            'entity_name' => 'nullable|string|max:255',
            'expiry_date' => 'nullable|date',
        ]);

        $file = $request->file('document');
        $originalName = $file->getClientOriginalName();
        
        // --- MAGIA: EXTRAER EL TEXTO INTERNO DEL PDF ---
        try {
            $pdfParser = new Parser();
            $pdf = $pdfParser->parseFile($file->getPathname());
            $extractedText = $pdf->getText();
        } catch (\Exception $e) {
            // Si el PDF es una foto escaneada sin texto digital, no rompemos el sistema
            $extractedText = null;
        }

        // Estructura de carpetas
        $folder = Folder::findOrFail($request->folder_id);
        $folderName = $folder->name; 

        $typeSlug = Str::upper(Str::slug($request->document_type, '_'));
        $entitySlug = $request->entity_name ? Str::upper(Str::slug($request->entity_name, '_')) : 'GENERAL';
        $dateStamp = date('Ymd_His');
        
        $standardName = "{$typeSlug}_{$entitySlug}_{$dateStamp}.pdf";

        $path = $file->storeAs($folderName, $standardName, 'google');

        // Guardamos todo indexado
        Document::create([
            'folder_id' => $request->folder_id,
            'title' => $originalName,
            'renamed_title' => $standardName,
            'file_path' => $path,
            'document_type' => $request->document_type,
            'entity_name' => $request->entity_name,
            'expiry_date' => $request->expiry_date,
            'content' => $extractedText, // <-- Guardamos el texto extraído
        ]);

        return back()->with('success', '¡Documento clasificado, leído por la IA y subido a Drive!');
    }

    public function preview($id)
    {
        $document = Document::findOrFail($id);

        try {
            $file = \Illuminate\Support\Facades\Storage::disk('google')->get($document->file_path);

            return response($file, 200)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'inline; filename="' . $document->renamed_title . '"');
        } catch (\Exception $e) {
            return back()->withErrors(['Error al obtener el documento de Google Drive: ' . $e->getMessage()]);
        }
    }
}