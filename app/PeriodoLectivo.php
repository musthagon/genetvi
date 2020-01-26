<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PeriodoLectivo extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'periodos_lectivos';

    protected $fillable = ['id', 'nombre', 'descripcion', 'opciones','created_at','updated_at'];

    public function getNombre(){
        return $this->nombre;
    }
    public function getDescripcion(){
        return $this->descripcion;
    }

    public function momentos_evaluacion(){
        return $this->belongsToMany('App\MomentosEvaluacion','periodos_lectivos_momentos_evaluacion','periodo_lectivo_id','momento_evaluacion_id')->using('App\PeriodoLectivoMomentoEvaluacion')
        ->withPivot(
            PeriodoLectivoMomentoEvaluacion::get_fecha_inicio_field(),
            PeriodoLectivoMomentoEvaluacion::get_fecha_fin_field(),
            PeriodoLectivoMomentoEvaluacion::get_opciones_field() );
    }
}
