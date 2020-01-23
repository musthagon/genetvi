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
    protected $fillable = ['id','valor_porcentual','categoria_id','instrumento_id','created_at','updated_at'];

}
