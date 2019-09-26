<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CategoriaDeInstrumento extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'categorias_instrumentos';

    public function indicadores(){
        return $this->hasMany('App\Indicador','categorias_instrumento_id','id');
    }
    
    public function instrumento()    {
        return $this->belongsTo('App\Instrumento','instrumento_id','id');
    }
}
