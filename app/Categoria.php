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
 
        $cantidad = 0;

        foreach($this->indicadores as $indicador){
            if($indicador->esMedible()){
                $cantidad++;
                break;
            }
        }
        
        return $cantidad;
              
    }

    public function getNombre(){
        return $this->nombre;
    }
    
    public function categoriaPersonalizada(){
        return !$this->indicadores_medibles;
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


    public function getOpciones(){//Retornamos las opciones del indicador que se escriben en el code-editor
        $options = [];
        if($this->opciones != null){
            $options = json_decode($this->opciones,true);
        }
        return $options;
    }

    public function getOpcionesEstructura($number){//Retornamos los campos de las opciones del indicador del code-editor 

        switch ($number) {
            case "1":
                return 'likert';
            break;
        }

        return null;
    }
    public function likertType(){
        if( isset($this->getOpciones()[$this->getOpcionesEstructura(1)]) ){   
            $likert_type = $this->getOpciones()[$this->getOpcionesEstructura(1)];
        }else{ 
            return "1";
        }

        if($likert_type != "2"){
            return "1";
        }
        return $likert_type;
    }

    public function likertOpciones(){

        $likert_type = $this->likertType();

        switch ($likert_type) {
            case "2":
                $likert = [ 
                    "likert_4" => "Totalmente de acuerdo",
                    "likert_3" => "De acuerdo",
                    "likert_2" => "Ni de acuerdo ni en desacuerdo",
                    "likert_1" => "En desacuerdo",
                    "likert_0" => "Totalmente en desacuerdo"];
            break;
            default:
                $likert = [ 
                    "likert_2" => "Siempre", 
                    "likert_1" => "A veces", 
                    "likert_0" => "Nunca" ];
            break;
            
        }

        return $likert;
    }
}
