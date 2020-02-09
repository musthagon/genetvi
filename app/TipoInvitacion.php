<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TipoInvitacion extends Model
{
    protected $table = 'tipo_invitaciones';
    protected $fillable = ['id', 'nombre', 'nombre_corto', 'descripcion','created_at','updated_at'];

    public static function getEstatusAutomatica(){
        return TipoInvitacion::where('nombre','automática')->first()->getID();
    }
    public function getID(){
        return $this->id;
    }
}
