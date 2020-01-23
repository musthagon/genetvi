<?php

use Illuminate\Database\Seeder;
use App\MomentosEvaluacion;

class MomentosEvaluacionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $momento = MomentosEvaluacion::firstOrNew(['id' => '1']);
        if (!$momento->exists) {
            $momento->fill([
                    MomentosEvaluacion::get_nombre_field()       => 'Inicio del período lectivo',
                    MomentosEvaluacion::get_nombre_corto_field() => 'inicio_periodo',
                    MomentosEvaluacion::get_descripcion_field()  => 'Momento de Evaluación al comienzo del período lectivo'
                ])->save();
        }

        $momento = MomentosEvaluacion::firstOrNew(['id' => '2']);
        if (!$momento->exists) {
            $momento->fill([
                    MomentosEvaluacion::get_nombre_field()       => 'Mitad del período lectivo',
                    MomentosEvaluacion::get_nombre_corto_field() => 'mitad_periodo',
                    MomentosEvaluacion::get_descripcion_field()  => 'Momento de Evaluación durante la mitad del período lectivo'
                ])->save();
        }

        $momento = MomentosEvaluacion::firstOrNew(['id' => '3']);
        if (!$momento->exists) {
            $momento->fill([
                    MomentosEvaluacion::get_nombre_field()       => 'Culminación del período lectivo',
                    MomentosEvaluacion::get_nombre_corto_field() => 'culiminacion_periodo',
                    MomentosEvaluacion::get_descripcion_field()  => 'Momento de Evaluación cercano a la culminación del período lectivo'
                ])->save();
        }

    }
}
