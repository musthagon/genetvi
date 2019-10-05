<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Instrumento extends Model
{
    protected $fillable = ['nombre','descripcion','habilitar','opciones','rol_id'];
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'instrumentos';

    public function categorias(){
        return $this->belongsToMany('App\Categoria','categorias_instrumentos','instrumento_id','categoria_id')->using('App\CategoriaInstrumento');
    }

    public function evaluaciones(){
        return $this->hasMany('App\Evaluacion','instrumento_id','id');
    }

    public function esValido(){
        $categorias = $this->categorias;
        if(!$categorias->isEmpty()){
            foreach($this->categorias as $categoria){
                if (!$categoria->esValida()){
                    return false;
                }
            }
        }else{
            return false;
        }
        return true;
    }

    public function percentilValue(){
        $cantidad = $this->categorias->count();
        if($cantidad != 0){
            return 100/$cantidad;
        }
        return 0;
    }

}
