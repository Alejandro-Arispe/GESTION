<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bitacora', function (Blueprint $table) {
            $table->id('id_bitacora');
            $table->unsignedBigInteger('id_usuario')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable();
            $table->string('accion', 255);
            $table->longText('descripcion')->nullable();
            $table->string('ip_origen', 45)->nullable();
            $table->string('navegador', 255)->nullable();
            $table->string('tabla_afectada', 100)->nullable();
            $table->unsignedBigInteger('registro_id')->nullable();
            
            $table->foreign('id_usuario')->references('id_usuario')->on('usuario')
                  ->onDelete('set null')->onUpdate('cascade');
            
            $table->index('id_usuario');
            $table->index('created_at');
            $table->index('accion');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bitacora');
    }
};