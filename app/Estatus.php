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

    public function getID(){
        return $this->id;
    }

    public function getNombre(){
        return $this->nombre;
    }
}
