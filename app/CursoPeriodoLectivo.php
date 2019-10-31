<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\Pivot;

class CursoPeriodoLectivo extends Pivot
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'curso_periodos_lectivos';
}
