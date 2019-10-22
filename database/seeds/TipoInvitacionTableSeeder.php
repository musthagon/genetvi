<?php

use Illuminate\Database\Seeder;
use App\TipoInvitacion;

class TipoInvitacionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $estatus = TipoInvitacion::firstOrNew(['id' => '1']);
        if (!$estatus->exists) {
            $estatus->fill([
                    'nombre'        => 'manual',
                    'nombre_corto'  => 'manual',
                    'descripcion'   => 'la invitación es realizada de forma manual'
                ])->save();
        }

        $estatus = TipoInvitacion::firstOrNew(['id' => '2']);
        if (!$estatus->exists) {
            $estatus->fill([
                    'nombre'        => 'automática',
                    'nombre_corto'  => 'automática',
                    'descripcion'   => 'la invitación es realizada de forma automática'
                ])->save();
        }
    }
}
