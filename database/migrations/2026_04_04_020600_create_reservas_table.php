<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservas', function (Blueprint $table) {
            $table->id();
            $table->string('codigo')->unique();
            $table->foreignId('pasajero_id')->constrained('pasajeros')->cascadeOnDelete();
            $table->foreignId('vuelo_id')->constrained('vuelos')->cascadeOnDelete();
            $table->string('clase')->default('economica');
            $table->string('estado')->default('confirmada');
            $table->string('asiento')->nullable();
            $table->decimal('precio', 10, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservas');
    }
};
