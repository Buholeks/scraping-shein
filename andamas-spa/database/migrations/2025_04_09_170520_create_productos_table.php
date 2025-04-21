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
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_empresa')->nullable();
            $table->unsignedBigInteger('id_sucursal')->nullable();
            $table->unsignedBigInteger('id_user')->nullable();
            $table->unsignedBigInteger('id_cliente')->nullable();
            $table->string('sku')->nullable();
            $table->string('titulo')->nullable();          // por si no lo agregaste
            $table->decimal('precio', 10, 2)->nullable();
            $table->decimal('precio_original', 10, 2)->nullable();
            $table->string('imagen')->nullable();
            $table->timestamps();
            // Foreign Keys
            $table->foreign('id_empresa')->references('id')->on('empresas')->onDelete('set null');
            $table->foreign('id_sucursal')->references('id')->on('sucursales')->onDelete('set null');
            $table->foreign('id_user')->references('id')->on('users')->onDelete('set null');
            $table->foreign('id_cliente')->references('id')->on('clientes')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->dropForeign(['id_empresa']);
            $table->dropForeign(['id_sucursal']);
            $table->dropForeign(['id_user']);
            $table->dropForeign(['id_cliente']);

            $table->dropColumn(['id_empresa', 'id_sucursal', 'id_user', 'id_cliente']);
        });
    }
};
