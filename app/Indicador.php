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
    protected $fillable = ['id','nombre','tipo','requerido','opciones','orden','created_at','updated_at'];

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

    public function esLikert(){
        $tipo_indicador = $this->getTipo();
        if ($tipo_indicador == "likert") {
            return true;
        }
        return false;
    }

    public function getRequerido(){
        return $this->requerido;
    }

    public function multipleField(){ //Los campos del formularios que se almacenan como un array name=id[]

        if($this->getTipo() == 'select_multiple'){
            return true;
        }
        return false;
    }

    //Parametros configurables
    //Retornamos los campos de las opciones del indicador del code-editor 
    //Para indicadores select dropdown y select multiple el formato es el siguiente: {"opciones" : ["Opcion1", "Opcion2", "Opcion3"], "predeterminado" : "0" } y se puede agregar la opción "medible" : true/false en caso que no se requiere que el indicador afecta la evalucación
    public function getOpcionesEstructura($number){

        switch ($number) {
            case "1":
                return 'opciones';
            break;
            case "2":
                return 'predeterminado';
            break;
            case "3":
                return 'medible';
            break;
        }

        return null;
    }

    //Retornamos las opciones del indicador que se escriben en el code-editor
    public function getOpciones($opcion_number = 0){
        $opciones = []; //Opciones predeterminadas

        //Si el campo opciones esta vacio
        if($this->opciones != null){
            $opciones = json_decode($this->opciones,true);
        }

        switch ($opcion_number) {
            case "1":
                if(isset($opciones[$this->getOpcionesEstructura($opcion_number)])){
                    if(is_array($opciones[$this->getOpcionesEstructura($opcion_number)])){
                        return $opciones[$this->getOpcionesEstructura($opcion_number)];
                    }
                }
                return ['Si','No']; 
            break;
            case "2":
                if(isset($opciones[$this->getOpcionesEstructura($opcion_number)])){
                    if(is_numeric ($opciones[$this->getOpcionesEstructura($opcion_number)])){
                        return $opciones[$this->getOpcionesEstructura($opcion_number)];
                    }
                }
                //return null; 
                return 0;
            break;
            case "3":
                if(isset($opciones[$this->getOpcionesEstructura($opcion_number)])){
                    if(is_bool($opciones[$this->getOpcionesEstructura($opcion_number)])){
                        return $opciones[$this->getOpcionesEstructura($opcion_number)];
                    }
                }
                return true; 
            break;
        }

        return $opciones;
    }

    
    public function esMedible(){//Verificamos si el indicador es una pregunta abierta o cerrada, para poder graficarlo
        $tipo_indicador = $this->getTipo();
        if ($tipo_indicador == "likert" ||
            $tipo_indicador == "select_dropdown" ) {
            if($this->getOpciones(3)){
                return true;
            }
        }
        
        return false;
    }

    public function percentilValueOpciones($likertType = 1){
        $tipo_indicador = $this->getTipo();
        $cantidad_opciones = 0;
        /*if ($tipo_indicador == "likert"){
            if($likertType == 2){
                $cantidad_opciones = 5;
            }else{
                $cantidad_opciones = 3;
            }
        }else
        */
        if ($tipo_indicador == "select_dropdown" ||
            $tipo_indicador == "select_multiple") {
            $cantidad_opciones = count($this->getOpciones(1));
        }
        
        return $cantidad_opciones;
    }

    public function percentilValueRequest($string_request, $percentilValueOpciones, $likertOpciones){
        $opcionValue = $percentilValueOpciones;
        if($opcionValue == 0){ return 0; }

        if($this->getTipo() == "likert"){
            foreach($likertOpciones as $key => $opcion){
                if($string_request == $opcion){
                    break;
                }
                $opcionValue = $opcionValue - 1;
            }
        }else{
            foreach($this->getOpciones(1) as $key => $opcion){
                if($string_request == $key){
    
                    break;
                }
                $opcionValue = $opcionValue - 1;
            }
        }

        return $opcionValue;
    }

    public function indicadorOpciones($likert){
        $tipo_indicador = $this->getTipo();
        if($this->esMedible()){
            if ($tipo_indicador == "likert"){
                return $likert;
            }else{
                return $this->getOpciones(1);
            }
        }
        return [];
    }
}
