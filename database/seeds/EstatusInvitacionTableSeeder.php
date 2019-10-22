<?php

use Illuminate\Database\Seeder;
use App\EstatusInvitacion;

class EstatusInvitacionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $estatus = EstatusInvitacion::firstOrNew(['id' => '1']);
        if (!$estatus->exists) {
            $estatus->fill([
                    'nombre'        => 'creada',
                    'nombre_corto'  => 'creada',
                    'descripcion'   => 'estatus cuando se crea la invitación'
                ])->save();
        }

        $estatus = EstatusInvitacion::firstOrNew(['id' => '2']);
        if (!$estatus->exists) {
            $estatus->fill([
                    'nombre'        => 'enviada',
                    'nombre_corto'  => 'enviada',
                    'descripcion'   => 'estatus cuando se envía la invitación, luego de ser creada'
                ])->save();
        }

        $estatus = EstatusInvitacion::firstOrNew(['id' => '3']);
        if (!$estatus->exists) {
            $estatus->fill([
                    'nombre'        => 'recordatorio enviado',
                    'nombre_corto'  => 'recordatorio enviado',
                    'descripcion'   => 'estatus cuando se envía un recordatorio de la invitación'
                ])->save();
        }

        $estatus = EstatusInvitacion::firstOrNew(['id' => '4']);
        if (!$estatus->exists) {
            $estatus->fill([
                    'nombre'        => 'aceptada',
                    'nombre_corto'  => 'aceptada',
                    'descripcion'   => 'estatus cuando se acepta la invitación, (sólo si el instrumento puede ser aceptado/rechazado)'
                ])->save();
        }

        $estatus = EstatusInvitacion::firstOrNew(['id' => '5']);
        if (!$estatus->exists) {
            $estatus->fill([
                    'nombre'        => 'rechazada',
                    'nombre_corto'  => 'rechazada',
                    'descripcion'   => 'estatus cuando se rechaza la invitación, (sólo si el instrumento puede ser aceptado/rechazado)'
                ])->save();
        }

        $estatus = EstatusInvitacion::firstOrNew(['id' => '6']);
        if (!$estatus->exists) {
            $estatus->fill([
                    'nombre'        => 'leída',
                    'nombre_corto'  => 'leída',
                    'descripcion'   => 'estatus cuando la invitación es leída por el usuario pero no se ha completado'
                ])->save();
        }

        $estatus = EstatusInvitacion::firstOrNew(['id' => '7']);
        if (!$estatus->exists) {
            $estatus->fill([
                    'nombre'        => 'completada',
                    'nombre_corto'  => 'completada',
                    'descripcion'   => 'estatus cuando se completa la invitación'
                ])->save();
        }

        $estatus = EstatusInvitacion::firstOrNew(['id' => '8']);
        if (!$estatus->exists) {
            $estatus->fill([
                    'nombre'        => 'revocada',
                    'nombre_corto'  => 'revocada',
                    'descripcion'   => 'estatus cuando se revoca la invitación a eliminar a dicho usuario'
                ])->save();
        }


    }
}
