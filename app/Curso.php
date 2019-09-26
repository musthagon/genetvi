<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Curso extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'cursos';

    public function evaluaciones(){
        return $this->hasMany('App\Evaluacion','curso_id','id');
    }

    public function categoria(){
        return $this->belongsTo('App\CategoriaDeCurso','categoria_id','id');
    }

    public function periodos_lectivos(){
        return $this->belongsToMany('App\PeriodoLectivo','curso_periodos_lectivos','curso_id','periodos_lectivo_id')->using('App\CursoPeriodoLectivo');
    }
}
