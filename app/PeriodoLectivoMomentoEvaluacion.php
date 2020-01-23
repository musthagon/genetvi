<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\Pivot;

class PeriodoLectivoMomentoEvaluacion extends Pivot
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'periodos_lectivos_momentos_evaluacion';
    protected $fillable = ['id','periodo_lectivo_id','momento_evaluacion','fecha_inicio','fecha_fin','opciones','created_at','updated_at'];
}
