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

    public function multipleField(){ //Los campos del formularios que se almacenan como un array name=id[]

        if($this->getTipo() == 'select_multiple'){
            return true;
        }
        return false;
    }

    public function getOpciones($opcion_number = 0){//Retornamos las opciones del indicador que se escriben en el code-editor
        $opciones = []; //Opciones predeterminadas
        if($this->opciones != null){
            $opciones = json_decode($this->opciones,true);
        }
        
        switch ($opcion_number) {
            case "1":
                if(isset($opciones[$this->getOpcionesEstructura($opcion_number)])){
                    return $opciones[$this->getOpcionesEstructura($opcion_number)];
                }
                return ['Si' => 'Si', 'No' => 'No']; //Opciones predeterminadas
            break;
            case "2":
                if(isset($opciones[$this->getOpcionesEstructura($opcion_number)])){
                    return $opciones[$this->getOpcionesEstructura($opcion_number)];
                }
                return null; //Opciones predeterminadas
            break;
            case "3":
                if(isset($opciones[$this->getOpcionesEstructura($opcion_number)])){
                    return $opciones[$this->getOpcionesEstructura($opcion_number)];
                }
                return true; //Opciones predeterminadas
            break;
        }

        return $opciones;
    }

    public function getOpcionesEstructura($number){//Retornamos los campos de las opciones del indicador del code-editor 

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

    public function percentilValue($likertType = 1){
        $tipo_indicador = $this->getTipo();
        $cantidad_opciones = 0;
        if ($tipo_indicador == "likert"){
            $cantidad_opciones = 2;
            if($likertType == 2){
                $cantidad_opciones = 5;
            }
        }elseif ($tipo_indicador == "select_dropdown" ||
            $tipo_indicador == "select_multiple") {
            $cantidad_opciones = count($this->getOpciones(1));
        }
        
        return $cantidad_opciones;
    }

}
