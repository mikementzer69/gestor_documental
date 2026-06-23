<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Folder extends Model
{
    // Los campos que permitimos guardar en la base de datos
    protected $fillable = ['name', 'slug', 'parent_id', 'drive_id'];

    // 1. Para saber quién es la carpeta "Padre"
    public function parent()
    {
        return $this->belongsTo(Folder::class, 'parent_id');
    }

    // 2. Para obtener todas las "Subcarpetas" (Hijas)
    public function children()
    {
        return $this->hasMany(Folder::class, 'parent_id');
    }

    // 3. Para obtener todos los PDFs guardados en esta carpeta
    public function documents()
    {
        return $this->hasMany(Document::class);
    }
}