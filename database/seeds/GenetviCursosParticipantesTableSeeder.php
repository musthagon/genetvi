<?php

use Illuminate\Database\Seeder;
use App\CursoParticipanteRol;

class GenetviCursosParticipantesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $curso_rol = CursoParticipanteRol::firstOrNew(['id' => '1']);
        if (!$curso_rol->exists) {
            $curso_rol->fill([
                    'cvucv_name' => 'Administrador del Espacio Virtual',
                    'cvucv_shortname' => 'manager',
                ])->save();
        }

        $curso_rol = CursoParticipanteRol::firstOrNew(['id' => '2']);
        if (!$curso_rol->exists) {
            $curso_rol->fill([
                    'cvucv_name' => 'Administrador2 del Espacio Virtual',
                    'cvucv_shortname' => 'manager2',
                ])->save();
        }

        $curso_rol = CursoParticipanteRol::firstOrNew(['id' => '3']);
        if (!$curso_rol->exists) {
            $curso_rol->fill([
                    'cvucv_name' => 'Profesor editor',
                    'cvucv_shortname' => 'editingteacher',
                ])->save();
        }

        $curso_rol = CursoParticipanteRol::firstOrNew(['id' => '4']);
        if (!$curso_rol->exists) {
            $curso_rol->fill([
                    'cvucv_name' => 'Profesor Sin Permisos de Edición',
                    'cvucv_shortname' => 'teacher',
                ])->save();
        }

        $curso_rol = CursoParticipanteRol::firstOrNew(['id' => '5']);
        if (!$curso_rol->exists) {
            $curso_rol->fill([
                    'cvucv_name' => 'Estudiante',
                    'cvucv_shortname' => 'student',
                ])->save();
        }

    }
}
