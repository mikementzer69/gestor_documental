<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

use App\Http\Controllers\FileManagerController;

// Ruta para la pantalla principal (Raíz)
Route::get('/files', [FileManagerController::class, 'index'])->name('filemanager.index');

// Ruta para entrar a una carpeta específica usando su ID
Route::get('/files/folder/{folderId}', [FileManagerController::class, 'index'])->name('filemanager.folder');
Route::post('/folders/create', [App\Http\Controllers\FileManagerController::class, 'storeFolder'])->name('folders.store');
Route::post('/files/upload', [FileManagerController::class, 'upload'])->name('filemanager.upload');