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
         Schema::create('sucursal_user', function (Blueprint $table) {
             $table->id();
             $table->unsignedBigInteger('id_user')->nullable();
             $table->unsignedBigInteger('id_sucursal')->nullable();
             $table->timestamps();

             $table->foreign('id_user')->references('id')->on('users')->onDelete('set null');
             $table->foreign('id_sucursal')->references('id')->on('sucursales')->onDelete('set null');
         });
     }
 
     public function down()
     {
         Schema::dropIfExists('sucursal_user');
     }
};
