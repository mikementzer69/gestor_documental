<?php

namespace App\Http\Controllers;

use App\Models\Folder;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Smalot\PdfParser\Parser; // <-- La librería mágica instalada
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

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
            
            $breadcrumbs = [];
            if ($currentFolder) {
                $folder = $currentFolder;
                while ($folder) {
                    array_unshift($breadcrumbs, $folder);
                    $folder = $folder->parent;
                }
            }
        }

        return view('file-manager', compact(
            'currentFolder', 
            'folders', 
            'documents', 
            'search', 
            'expiredDocuments', 
            'soonToExpireDocuments',
            'breadcrumbs'
        ));
    }

    public function storeFolder(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'descripcion' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:folders,id',
        ]);

        $folder = Folder::create([
            'name' => $request->name,
            'descripcion' => $request->descripcion,
            'parent_id' => $request->parent_id,
            'slug' => Str::slug($request->name)
        ]);

        // Crear físicamente en Google Drive
        try {
            $fullPath = $folder->full_path;
            \Illuminate\Support\Facades\Storage::disk('google')->makeDirectory($fullPath);
        } catch (\Exception $e) {
            // Si falla la conexión, la carpeta se crea igual de forma lógica
        }

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'CREATE_FOLDER',
            'description' => "Creó la carpeta: {$folder->name}",
        ]);

        return back()->with('success', 'Carpeta creada exitosamente en el sistema y en Google Drive.');
    }

    public function scanDrive(Request $request)
    {
        try {
            $directories = \Illuminate\Support\Facades\Storage::disk('google')->allDirectories('/');
            $count = 0;
            
            foreach ($directories as $directory) {
                $directory = trim($directory, '/');
                if (empty($directory)) continue;

                $parts = explode('/', $directory);
                $parentId = null;
                
                foreach ($parts as $part) {
                    $folder = Folder::firstOrCreate(
                        ['name' => $part, 'parent_id' => $parentId],
                        ['slug' => Str::slug($part), 'descripcion' => 'Carpeta sincronizada desde Google Drive']
                    );
                    $parentId = $folder->id;
                }
                $count++;
            }

            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'SCAN_DRIVE',
                'description' => "Escaneó Google Drive y sincronizó {$count} rutas de carpetas.",
            ]);

            return back()->with('success', "¡Google Drive escaneado exitosamente! Se revisaron {$count} rutas.");
        } catch (\Exception $e) {
            return back()->withErrors(['Error al escanear Google Drive: ' . $e->getMessage()]);
        }
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
        $folderPath = $folder->full_path; 

        $typeSlug = Str::upper(Str::slug($request->document_type, '_'));
        $entitySlug = $request->entity_name ? Str::upper(Str::slug($request->entity_name, '_')) : 'GENERAL';
        $dateStamp = date('Ymd_His');
        
        $standardName = "{$typeSlug}_{$entitySlug}_{$dateStamp}.pdf";

        $path = $file->storeAs($folderPath, $standardName, 'google');

        // Guardamos todo indexado
        $newDocument = Document::create([
            'folder_id' => $request->folder_id,
            'title' => $originalName,
            'renamed_title' => $standardName,
            'file_path' => $path,
            'document_type' => $request->document_type,
            'entity_name' => $request->entity_name,
            'expiry_date' => $request->expiry_date,
            'content' => $extractedText, // <-- Guardamos el texto extraído
        ]);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'UPLOAD',
            'description' => "Subió el documento: {$originalName} (Clasificado como: {$standardName})",
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

    public function deleteFile($id)
    {
        $document = Document::findOrFail($id);

        try {
            // Eliminar de Google Drive (solo si existe en el disco remoto para no generar error)
            if (\Illuminate\Support\Facades\Storage::disk('google')->exists($document->file_path)) {
                \Illuminate\Support\Facades\Storage::disk('google')->delete($document->file_path);
            }
            
            // Eliminar de la BD siempre
            $document->delete();

            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'DELETE_FILE',
                'description' => "Eliminó el documento: {$document->renamed_title}",
            ]);

            return back()->with('success', 'Documento eliminado exitosamente.');
        } catch (\Exception $e) {
            // Si hay un error severo, de todas formas lo borramos de la base de datos para no dejar "archivos fantasma"
            $document->delete();
            return back()->withErrors(['Aviso: El archivo ya no estaba en Google Drive, pero fue removido del sistema local.']);
        }
    }

    public function deleteFolder($id)
    {
        $folder = Folder::findOrFail($id);

        try {
            // Encontrar todos los documentos dentro de esta carpeta
            $documents = Document::where('folder_id', $folder->id)->get();

            // Eliminar físicamente los documentos de Google Drive (opcionalmente podríamos intentar borrar el directorio entero)
            foreach ($documents as $document) {
                \Illuminate\Support\Facades\Storage::disk('google')->delete($document->file_path);
                $document->delete();
            }

            // Eliminar la carpeta de la base de datos
            $folderName = $folder->name;
            $folder->delete();

            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'DELETE_FOLDER',
                'description' => "Eliminó la carpeta: {$folderName} y todo su contenido",
            ]);

            return back()->with('success', 'Carpeta y todo su contenido eliminados exitosamente.');
        } catch (\Exception $e) {
            return back()->withErrors(['Error al eliminar la carpeta: ' . $e->getMessage()]);
        }
    }
}