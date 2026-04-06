<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pasajeros', function (Blueprint $table) {
            $table->string('documento')->nullable()->after('apellido');
            $table->string('email')->nullable()->after('documento');
            $table->string('telefono')->nullable()->after('email');
        });
    }

    public function down(): void
    {
        Schema::table('pasajeros', function (Blueprint $table) {
            $table->dropColumn(['documento', 'email', 'telefono']);
        });
    }
};
