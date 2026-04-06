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
        Schema::create('pasajeros', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('apellido');

            $table->foreignId('vuelo_id')
                 ->constrained('vuelos')
                  ->cascadeOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pasajeros');
    }
};