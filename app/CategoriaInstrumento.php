<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\Pivot;

class CategoriaInstrumento extends Pivot
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'categorias_instrumentos';
}
