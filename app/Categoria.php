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
    protected $fillable = ['id','nombre','nombre_corto','descripcion','opciones','orden','created_at','updated_at'];
    
    //Opciones del campo opciones
    protected $likertOption     = "likert";
    protected $categoriaPerfil  = "perfil";

    public function indicadores(){
        return $this->belongsToMany('App\Indicador','categorias_indicadores','categoria_id','indicador_id')->using('App\CategoriaIndicador')->withPivot('valor_porcentual');
    }

    public function indicadoresOrdenados(){
        return $this->indicadores->sortBy(function($indicador){
            return $indicador->orden;
        });
    }

    public function instrumento(){
        return $this->belongsTo('App\Instrumento','instrumento_id','id');
    }

    public function esValida(){
        return !$this->indicadores->isEmpty();
    }

    public function likertDefault(){
        $likert = [
            "Totalmente de acuerdo",
            "De acuerdo",
            "Ni de acuerdo ni en desacuerdo",
            "En desacuerdo",
            "Totalmente en desacuerdo",          
        ];
        return $likert;
    }

    public function perfilDefault(){
        return false;
    }

    public static function checkLikertOption($request){
        $opciones = json_decode($request,true);

        if(!isset( $opciones[(new self)->likertOption] )){
            return true;
        }

        $likert = $opciones[(new self)->likertOption];
        
        if(!is_array($likert)){
            return false;
        }
        
        $index = 0;
        foreach($likert as $key => $opcion){
            if($index != $key || !is_string($opcion) ){
                return false;
            }
            $index++;
        }

        return true;
    }

    public static function checkCategoriaPerfil($request){
        $opciones = json_decode($request,true);

        if(!isset( $opciones[(new self)->categoriaPerfil] )){
            return true;
        }

        $perfil = $opciones[(new self)->categoriaPerfil];
        
        if(!is_bool($perfil)){
            return false;
        }

        return true;
    }

    public function getLikertType(){
        $opciones = $this->getOpciones();

        if(!isset( $opciones[$this->likertOption] )){
            return $this->likertDefault();
        }else{
            return $opciones[$this->likertOption];
        }
    }

    public function getLikertCantidadOpciones(){
        $likert = $this->getLikertType();
        return count($likert)-1;
    }

    public function getID(){
        return $this->id;
    }
    
    public function getPerfil(){
        $opciones = $this->getOpciones();

        if(!isset( $opciones[$this->categoriaPerfil] )){
            return $this->perfilDefault();
        }else{
            return $opciones[$this->categoriaPerfil];
        }
    }

    public function percentilValue(){
 
        $cantidad = 0;

        foreach($this->indicadores as $indicador){
            if($indicador->esMedible()){
                $cantidad++;
            }
        }
        
        return $cantidad;
              
    }
    public function esMedible(){

        foreach($this->indicadores as $indicador){
            if($indicador->esMedible() && $indicador->pivot->valor_porcentual != 0){
                return true;
            }
        }
    
        return false;
              
    }
    public function tieneIndicadoresLikert(){

        foreach($this->indicadores as $indicador){
            if($indicador->esLikert()){
                return true;
            }
        }
        return false;
              
    }
    public function getNombre(){
        return $this->nombre;
    }
    public function getNombreCorto(){
        return $this->nombre_corto;
    }
    
    public function existenIndicadoresObligatorios(){
        $indicadores = $this->indicadores;
        foreach($indicadores as $indicador){
            if($indicador->getRequerido()){
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

        if($likert_type != "2"){ //Likerts disponibles
            return "1";
        }
        return $likert_type;
    }

    public function likertOpciones(){

        $likert_type = $this->likertType();

        switch ($likert_type) {
            case "2":
                $likert = [ 
                    "Totalmente de acuerdo"             => "Totalmente de acuerdo",
                    "De acuerdo"                        => "De acuerdo",
                    "Ni de acuerdo ni en desacuerdo"    => "Ni de acuerdo ni en desacuerdo",
                    "En desacuerdo"                     => "En desacuerdo",
                    "Totalmente en desacuerdo"          => "Totalmente en desacuerdo"];
            break;
            default:
                $likert = [ 
                    "Siempre"   => "Siempre", 
                    "A veces"   => "A veces", 
                    "Nunca"     => "Nunca" ];
            break;
            
        }

        return $likert;
    }
}
