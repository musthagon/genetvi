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
    protected $fillable = ['id','nombre','nombre_corto','descripcion','indicadores_medibles','opciones','orden'];
    
    public function indicadores(){
        return $this->belongsToMany('App\Indicador','categorias_indicadores','categoria_id','indicador_id')->using('App\CategoriaIndicador');
    }

    public function indicadoresOrdenados(){
        return $this->indicadores->sortBy(function($indicador){
            return $indicador->orden;
        });
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

    public function getNombre(){
        return $this->nombre;
    }
    
    public function categoriaPersonalizada(){
        return $this->indicadores_medibles;
    }

    public function existenIndicadoresObligatorios(){
        $indicadores = $this->indicadores;
        foreach($indicadores as $indicador){
            if($indicador->requerido()){
                return true;
            }
        }
        return false;
    }
}
