<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'categorias';
    protected $fillable = ['id','nombre','nombre_corto','descripcion','opciones','orden'];
    
    public function indicadores(){
        return $this->belongsToMany('App\Indicador','categorias_indicadores','categoria_id','indicador_id')->using('App\CategoriaIndicador');
    }

    public function instrumento()    {
        return $this->belongsTo('App\Instrumento','instrumento_id','id');
    }

    public function esValida(){
        return !$this->indicadores->isEmpty();
    }

    public function percentilValue(){
        return $this->indicadores->count();
    }
}
