<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Crear tabla roles
        Schema::create('roles', function (Blueprint $table) {
            $table->id('id_rol');
            $table->string('nombre_rol');
            $table->string('descripcion')->nullable();
        });

        // Insertar roles básicos por defecto
        DB::table('roles')->insert([
            ['id_rol' => 1, 'nombre_rol' => 'Usuario', 'descripcion' => 'Usuario regular'],
            ['id_rol' => 2, 'nombre_rol' => 'Administrador', 'descripcion' => 'Administrador del sistema'],
        ]);

        // 2. Crear tabla accidentes
        Schema::create('accidentes', function (Blueprint $table) {
            $table->id('id_accidente');
            $table->string('id_caso')->unique();
            $table->string('tipo_accidente');
            $table->date('fecha_incidente');
            $table->time('hora_aproximada');
            $table->string('gravedad');
            $table->string('direccion')->nullable();
            $table->string('municipio')->nullable();
            $table->text('descripcion')->nullable();
            $table->string('condicion_climatica')->nullable();
            $table->string('tipo_via')->nullable();
            $table->string('estado_pavimento')->nullable();
            $table->text('declaracion_involucrados')->nullable();
            $table->unsignedBigInteger('id_usuario');
            $table->foreign('id_usuario')->references('id_usuario')->on('usuarios')->onDelete('cascade');
        });

        // 3. Crear tabla vehiculos_involucrados
        Schema::create('vehiculos_involucrados', function (Blueprint $table) {
            $table->id('id_vehiculo');
            $table->unsignedBigInteger('id_accidente');
            $table->string('marca')->nullable();
            $table->string('modelo')->nullable();
            $table->string('tipo_vehiculo')->nullable();
            $table->integer('anio')->nullable();
            $table->string('propietario')->nullable();
            $table->foreign('id_accidente')->references('id_accidente')->on('accidentes')->onDelete('cascade');
        });

        // 4. Crear tabla personas_involucradas
        Schema::create('personas_involucradas', function (Blueprint $table) {
            $table->id('id_persona');
            $table->unsignedBigInteger('id_accidente');
            $table->string('nombre_completo');
            $table->string('estado_persona')->nullable();
            $table->text('observaciones')->nullable();
            $table->foreign('id_accidente')->references('id_accidente')->on('accidentes')->onDelete('cascade');
        });

        // 5. Crear tabla evidencias
        Schema::create('evidencias', function (Blueprint $table) {
            $table->id('id_evidencia');
            $table->unsignedBigInteger('id_accidente');
            $table->string('url_archivo');
            $table->string('tipo_evidencia')->nullable();
            $table->foreign('id_accidente')->references('id_accidente')->on('accidentes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evidencias');
        Schema::dropIfExists('personas_involucradas');
        Schema::dropIfExists('vehiculos_involucrados');
        Schema::dropIfExists('accidentes');
        Schema::dropIfExists('roles');
    }
};
