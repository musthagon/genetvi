<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EstatusInvitacion extends Model
{
    protected $table = 'estatus_invitaciones';
    protected $fillable = ['id', 'nombre', 'nombre_corto', 'descripcion','created_at','updated_at'];

    public static function getEstatusCreada(){
        return EstatusInvitacion::where('nombre','creada')->first()->getID();
    }

    public function getID(){
        return $this->id;
    }

    public function getNombre(){
        return $this->nombre;
    }
}
