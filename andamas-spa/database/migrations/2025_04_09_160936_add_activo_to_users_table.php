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
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('activo')->default(false);
            $table->unsignedBigInteger('id_empresa')->nullable()->after('id');
            $table->unsignedBigInteger('id_sucursal')->nullable()->after('id_empresa');

             // Foreign Keys
             $table->foreign('id_empresa')->references('id')->on('empresas')->onDelete('set null');
             $table->foreign('id_sucursal')->references('id')->on('sucursales')->onDelete('set null');
             
        });

        
    }
    
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('activo');
        });
    }
    
};
