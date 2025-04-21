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
        Schema::table('productos', function (Illuminate\Database\Schema\Blueprint $table) {
            $table->unsignedBigInteger('id_venta')->nullable()->after('estado'); // o donde te convenga
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('productos', function (Illuminate\Database\Schema\Blueprint $table) {
            $table->dropColumn('id_venta');
        });
    }
};
