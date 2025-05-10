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
    Schema::table('productos', function (Blueprint $table) {
        $table->string('marca')->nullable()->after('sku');
        $table->string('modelo')->nullable()->after('marca');
        $table->decimal('comision', 10, 2)->default(0)->after('precio_original'); // puedes ajustar la precisión si manejas decimales más grandes
        $table->decimal('total', 10, 2)->default(0)->after('comision');
    });
}

public function down()
{
    Schema::table('productos', function (Blueprint $table) {
        $table->dropColumn(['marca', 'modelo', 'comision', 'total']);
    });
}

};
