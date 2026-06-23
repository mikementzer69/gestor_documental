<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('folder_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->string('renamed_title');
            $table->string('file_path');
            $table->string('document_type');
            $table->string('entity_name')->nullable();
            $table->date('expiry_date')->nullable();
            
            // --- NUEVA COLUMNA: Aquí guardaremos el texto extraído del PDF ---
            $table->longText('content')->nullable(); 
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};