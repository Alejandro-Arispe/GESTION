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
        // Verificar si la tabla existe y si faltan columnas
        if (Schema::hasTable('bitacora')) {
            Schema::table('bitacora', function (Blueprint $table) {
                // Agregar created_at si no existe
                if (!Schema::hasColumn('bitacora', 'created_at')) {
                    $table->timestamp('created_at')->useCurrent()->after('registro_id');
                }
                
                // Agregar updated_at si no existe
                if (!Schema::hasColumn('bitacora', 'updated_at')) {
                    $table->timestamp('updated_at')->nullable()->after('created_at');
                }
                
                // Agregar tabla_afectada si no existe
                if (!Schema::hasColumn('bitacora', 'tabla_afectada')) {
                    $table->string('tabla_afectada', 100)->nullable()->after('navegador');
                }
                
                // Agregar registro_id si no existe
                if (!Schema::hasColumn('bitacora', 'registro_id')) {
                    $table->unsignedBigInteger('registro_id')->nullable()->after('tabla_afectada');
                }
                
                // Agregar índice a created_at si no existe
                try {
                    $table->index('created_at');
                } catch (\Exception $e) {
                    // El índice ya puede existir
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('bitacora')) {
            Schema::table('bitacora', function (Blueprint $table) {
                // Revertir cambios si es necesario
                // Nota: dropIndex puede causar error si el índice no existe
                try {
                    $table->dropIndex('bitacora_created_at_index');
                } catch (\Exception $e) {
                    // Ignorar si el índice no existe
                }
            });
        }
    }
};
