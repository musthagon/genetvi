<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Instrumento extends Model
{
    protected $fillable = ['id','nombre','nombre_corto','descripcion','habilitar'];
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'instrumentos';

    public function categorias(){
        return $this->belongsToMany('App\Categoria','categorias_instrumentos','instrumento_id','categoria_id')->using('App\CategoriaInstrumento');
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

    public function percentilValue(){
        $cantidad = $this->categorias->count();
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
}
