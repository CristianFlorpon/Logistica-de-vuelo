<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('flotas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('aerolinea_id')->constrained('aerolineas')->cascadeOnDelete();
            $table->string('matricula')->unique();
            $table->string('modelo');
            $table->unsignedInteger('capacidad');
            $table->unsignedInteger('alcance_km')->nullable();
            $table->string('tipo');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('flotas');
    }
};
