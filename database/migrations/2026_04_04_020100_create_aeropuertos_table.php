<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('aeropuertos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('codigo_iata')->unique();
            $table->string('ciudad');
            $table->string('pais');
            $table->decimal('latitud', 10, 7);
            $table->decimal('longitud', 10, 7);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('aeropuertos');
    }
};
