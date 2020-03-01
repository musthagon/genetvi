<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Estatus extends Model
{
    protected $table = 'estatus';
    protected $fillable = ['id', 'nombre', 'nombre_corto', 'descripcion','created_at','updated_at'];

    public static function getEstatusCreada(){
        return Estatus::where('nombre','creada')->first()->getID();
    }

    public static function getEstatusAceptada(){
        return Estatus::where('nombre','aceptada')->first()->getID();
    }

    public static function getEstatusRechazada(){
        return Estatus::where('nombre','rechazada')->first()->getID();
    }

    public static function getEstatusCompletada(){
        return Estatus::where('nombre','completada')->first()->getID();
    }

    public static function getEstatusLeida(){
        return Estatus::where('nombre','leída')->first()->getID();
    }

    public static function getEstatusRevocada(){
        return Estatus::where('nombre','revocada')->first()->getID();
    }

    public static function getEstatusRecordatorio(){
        return Estatus::where('nombre','recordatorio enviado')->first()->getID();
    }

    public function getID(){
        return $this->id;
    }

    public function getNombre(){
        return $this->nombre;
    }
}
