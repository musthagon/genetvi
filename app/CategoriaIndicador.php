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
    protected $fillable = ['id','valor_porcentual','categoria_id','indicador_id','created_at','updated_at'];

}
