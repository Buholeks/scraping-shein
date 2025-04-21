<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('sucursales', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_empresa');
            $table->string('nombre');
            $table->string('direccion')->nullable();
            $table->string('numero_tel')->nullable();
            $table->string('prefijo_folio_sucursal')->nullable();
            $table->timestamps();
    
            // Clave foránea
            $table->foreign('id_empresa')->references('id')->on('empresas')->onDelete('cascade');
        });
    }
    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sucursals');
    }
};
