<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Rutas del gestor documental
    Route::get('/files', [\App\Http\Controllers\FileManagerController::class, 'index'])->name('filemanager.index');
    Route::get('/files/folder/{folderId}', [\App\Http\Controllers\FileManagerController::class, 'index'])->name('filemanager.folder');
    Route::post('/folders/create', [\App\Http\Controllers\FileManagerController::class, 'storeFolder'])->name('folders.store');
    Route::post('/folders/scan', [\App\Http\Controllers\FileManagerController::class, 'scanDrive'])->name('folders.scan');
    Route::post('/files/upload', [\App\Http\Controllers\FileManagerController::class, 'upload'])->name('filemanager.upload');
    Route::get('/files/preview/{id}', [\App\Http\Controllers\FileManagerController::class, 'preview'])->name('filemanager.preview');
    
    // Rutas de borrado
    Route::delete('/files/delete/{id}', [\App\Http\Controllers\FileManagerController::class, 'deleteFile'])->name('filemanager.deleteFile');
    Route::delete('/folders/delete/{id}', [\App\Http\Controllers\FileManagerController::class, 'deleteFolder'])->name('filemanager.deleteFolder');
    
    // Bitácora
    Route::get('/logs', [\App\Http\Controllers\ActivityLogController::class, 'index'])->name('logs.index');
    
    // Reportes
    Route::get('/reports', [\App\Http\Controllers\ReportController::class, 'index'])->name('reports.index');
    
    // Mantenimiento de Carpetas
    Route::get('/folders/maintenance', [\App\Http\Controllers\FolderMaintenanceController::class, 'index'])->name('folders.maintenance');
    Route::get('/folders/maintenance/report', [\App\Http\Controllers\FolderMaintenanceController::class, 'report'])->name('folders.report');
    Route::put('/folders/maintenance/{id}', [\App\Http\Controllers\FolderMaintenanceController::class, 'update'])->name('folders.update');
});

require __DIR__.'/auth.php';
