<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CursoParticipante extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'cursos_participantes';

    protected $fillable = ['user_id', 'cvucv_user_id', 'cvucv_curso_id', 'cvucv_rol_id', 'user_sync', 'curso_sync'];

    public function cursos(){
        return $this->hasMany('App\Curso','categoria_id','id');
    }

    public function usuario(){
        return $this->belongsTo('App\User','user_id','id');
    }

    public static function estaMatriculado($cvucv_id,$curso_id){
        return CursoParticipante::where('cvucv_user_id', $cvucv_id)
        ->where('cvucv_curso_id', $curso_id)
        ->first();
    }

    public function getCVUCV_CURSO_ID(){
        return $this->cvucv_curso_id;
    }

}
