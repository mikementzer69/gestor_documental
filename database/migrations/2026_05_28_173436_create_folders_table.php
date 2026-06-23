<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up(): void
{
    Schema::create('folders', function (Blueprint $table) {
        $table->id();
        $table->string('name'); // Nombre de la carpeta (ej: Plan Trifinio)
        $table->string('slug')->nullable(); // Identificador interno corto (ej: plan_trifinio)
        
        // Relación para subcarpetas (una carpeta padre puede tener carpetas hijas)
        $table->foreignId('parent_id')->nullable()->constrained('folders')->onDelete('cascade');
        
        $table->string('drive_id')->nullable(); // Aquí guardaremos el ID único de Google Drive más adelante
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('folders');
    }
};
