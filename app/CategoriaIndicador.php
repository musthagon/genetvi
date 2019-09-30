<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\Pivot;

class CategoriaIndicador extends Pivot
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'categorias_indicadores';
}
