<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Indicador extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'indicadores';
    protected $fillable = ['id','nombre','tipo','requerido','opciones','orden'];

    public function categoria(){
        return $this->belongsTo('App\CategoriaDeInstrumento','categorias_instrumento_id','id');
    }

    public function getNombre(){
        return $this->nombre;
    }

    public function getID(){
        return $this->id;
    }

    public function getTipo(){
        return $this->tipo;
    }

    public function requerido(){
        return $this->requerido;
    }

    public function multipleField(){
        if($this->getTipo() == 'select_multiple'){
            return true;
        }
        return false;
    }

    public function getOpciones(){
        $options = [];
        if($this->opciones != null){
            $options = json_decode($this->opciones,true);
        }
        return $options;
    }

    public function getOpcionesEstructura($number){
        if($number == 1){
            return 'options';
        }elseif($number ==2){
            return 'default';
        }

        return null;
        
    }

}
