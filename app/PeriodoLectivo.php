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

    protected $fillable = ['id', 'nombre', 'descripcion', 'opciones', 'fecha_inicio', 'fecha_fin', 'momento_evaluacion_activo_id','created_at','updated_at'];

    public function getID(){
        return $this->id;
    }
    public function getNombre(){
        return $this->nombre;
    }
    public function getDescripcion(){
        return $this->descripcion;
    }
    public function getFecha_inicio(){
        return $this->fecha_inicio;
    }
    public function getFecha_fin(){
        return $this->fecha_fin;
    }
    public function getMomento_evaluacion_activo_id(){
        return $this->momento_evaluacion_activo_id;
    }
    public function momento_evaluacion_actual(){
        return $this->belongsTo('App\MomentosEvaluacion','momento_evaluacion_activo_id','id');
    }

    public function setMomento_evaluacion_activo($momento){
        $this->momento_evaluacion_activo_id = $momento;
        $this->save();
    }

    public function momentos_evaluacion(){
        return $this->belongsToMany('App\MomentosEvaluacion','periodos_lectivos_momentos_evaluacion','periodo_lectivo_id','momento_evaluacion_id')->using('App\PeriodoLectivoMomentoEvaluacion')
        ->withPivot(
            PeriodoLectivoMomentoEvaluacion::get_fecha_inicio_field(),
            PeriodoLectivoMomentoEvaluacion::get_fecha_fin_field(),
            PeriodoLectivoMomentoEvaluacion::get_opciones_field() );
    }

    public function actualizarMomentoEvaluacion(){
        $fecha_inicio   = $this->getFecha_inicio();
        $fecha_fin      = $this->getFecha_fin();

        $momentosAsociados = $this->momentos_evaluacion;
        $fecha_actual = date("m-d-Y H:i:s", strtotime( \Carbon\Carbon::now()));
        //dd($fecha_actual);
        foreach($momentosAsociados as $index => $momento){
            $fecha_inicio_momento   = date("m-d-Y H:i:s",strtotime( $momento->pivot->get_fecha_inicio() ));
            $fecha_fin_momento      = date("m-d-Y H:i:s",strtotime( $momento->pivot->get_fecha_fin() ));

            if($fecha_actual >= $fecha_inicio_momento && $fecha_actual <= $fecha_fin_momento){
                $this->setMomento_evaluacion_activo($momento->id);
                return $this->getMomento_evaluacion_activo_id();
            }
        }
        $this->setMomento_evaluacion_activo(NULL);
        return $this->getMomento_evaluacion_activo_id();
    }

    public function cambioMomentoEvaluacion($momentoAnterior, $momentoActualizado){
        if($momentoActualizado == null){
            return false;
        }
        if($momentoAnterior == $momentoActualizado){
            return false;
        }
        return true;
    }
}
