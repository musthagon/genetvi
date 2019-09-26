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

    public function cursos(){
        return $this->belongsToMany('App\Curso','curso_periodos_lectivos','periodos_lectivo_id','curso_id')->using('App\CursoPeriodoLectivo');
    }
}
