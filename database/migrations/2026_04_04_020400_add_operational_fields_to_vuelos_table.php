<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vuelos', function (Blueprint $table) {
            $table->foreignId('ruta_id')->nullable()->after('aerolinea_id')->constrained('rutas')->nullOnDelete();
            $table->foreignId('flota_id')->nullable()->after('ruta_id')->constrained('flotas')->nullOnDelete();
            $table->dateTime('salida_programada')->nullable()->after('flota_id');
            $table->dateTime('llegada_programada')->nullable()->after('salida_programada');
        });
    }

    public function down(): void
    {
        Schema::table('vuelos', function (Blueprint $table) {
            $table->dropForeign(['ruta_id']);
            $table->dropForeign(['flota_id']);
            $table->dropColumn(['ruta_id', 'flota_id', 'salida_programada', 'llegada_programada']);
        });
    }
};
