<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Instrumento extends Model
{
    protected $fillable = ['id','nombre','nombre_corto','descripcion','instrucciones','habilitar','anonimo','formato_evaluacion','opciones','invitacion_automatica','created_at','updated_at'];
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'instrumentos';

    public function categorias(){
        return $this->belongsToMany('App\Categoria','categorias_instrumentos','instrumento_id','categoria_id')->using('App\CategoriaInstrumento')->withPivot('valor_porcentual');
    }

    public function categoriasOrdenadas(){
        return $this->categorias->sortBy(function($categoria){
            return $categoria->orden;
        });
    }

    public function categoriasCodificadasInstrumento() {
        $categorias = $this->categoriasOrdenadas();

        $cat['perfil'] = array();
        $cat['instrumento'] = array();

        foreach($categorias as $categoria){
            if ($categoria->getPerfil()){ //Categoria es del perfil
                array_push($cat['perfil'],$categoria);
            }else{
                array_push($cat['instrumento'],$categoria);
            }
        }
        return $cat;
    }

    public function roles_dirigido(){
        return $this->belongsToMany('App\CursoParticipanteRol','instrumentos_cursos_participantes_roles','instrumento_id','curso_participante_rol_id')->using('App\InstrumentoCursoParticipanteRol');
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

    public function percentilValue(){ //Buscamos las categorias con idicadores que se puedan evaluar, si una categoria no tiene indicadores medibles -> no la contamos

        $categorias = $this->categorias;
        $cantidad = 0;

        foreach($categorias as $categoria){
            foreach($categoria->indicadores as $indicador){
                if($indicador->esMedible()){
                    $cantidad++;
                    break;
                }
            }
        }
        
        if($cantidad != 0){
            return 100/$cantidad;
        }
        return 0;
    }

    public function instrumentoDirigidoaRol($rolUsuario){
        $roles = $this->roles_dirigido;
        foreach($roles as $rol){
            if($rol->id == $rolUsuario){
                return true;
            }
        }
        return false;
    }

    public function getNombre(){
        return $this->nombre;
    }

    public function getDescripcion(){
        return $this->descripcion;
    }

    public function getInstrucciones(){
        return $this->instrucciones;
    }

    public function getAnonimo(){
        return $this->anonimo;
    }

    public function getPuedeRechazar(){
        return $this->formato_evaluacion;
    }
    

    public function getID(){
        return $this->id;
    }

    public function getInvitacionAutomatica(){
        return $this->invitacion_automatica;
    }

}
