<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rutas', function (Blueprint $table) {
            $table->id();
            $table->string('codigo')->unique();
            $table->string('pais')->nullable();
            $table->foreignId('origen_airport_id')->constrained('aeropuertos')->cascadeOnDelete();
            $table->foreignId('destino_airport_id')->constrained('aeropuertos')->cascadeOnDelete();
            $table->unsignedInteger('distancia_km')->nullable();
            $table->unsignedInteger('tiempo_estimado_min')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rutas');
    }
};
