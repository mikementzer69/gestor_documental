<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'folder_id', 
        'title', 
        'renamed_title', 
        'file_path', 
        'document_type', 
        'entity_name', 
        'expiry_date',
        'content' // <-- Permiso activado para el texto interno
    ];

    public function folder()
    {
        return $this->belongsTo(Folder::class);
    }
}