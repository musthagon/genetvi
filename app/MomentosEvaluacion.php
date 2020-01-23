<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MomentosEvaluacion extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'momentos_evaluacion';
    protected $fillable = ['id','nombre','nombre_corto','descripcion','opciones','created_at','updated_at'];

    public static function get_nombre_field(){
        return 'nombre';
    }
    public static function get_nombre_corto_field(){
        return 'nombre_corto';
    }
    public static function get_descripcion_field(){
        return 'descripcion';
    }
    
}
