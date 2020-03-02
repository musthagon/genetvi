<?php

namespace App;

use App\Curso;

use Illuminate\Database\Eloquent\Model;

class CursoParticipante extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'cursos_participantes';

    protected $fillable = ['user_id', 'cvucv_user_id', 'cvucv_curso_id', 'cvucv_rol_id', 'user_sync', 'curso_sync','created_at','updated_at'];

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

    public function getCVUCV_ROL_ID(){
        return $this->cvucv_rol_id;
    }

    public static function cursoComoDocente($rol){
        return $rol != 5;
    }

    //Consulta las matriculaciones del usuario
    public static function matriculacionesUsuario($userCVUCV_USER_ID){
        return CursoParticipante::where('cvucv_user_id', $userCVUCV_USER_ID)->get();
    }

    public static function cursosDocente($userid){

        $cursosDocente   = collect();

        $matriculaciones = CursoParticipante::matriculacionesUsuario($userid);

        foreach($matriculaciones as $matriculacion){
            $curso = Curso::find($matriculacion->getCVUCV_CURSO_ID());
            if(!empty($curso)){
                if(CursoParticipante::cursoComoDocente($matriculacion->getCVUCV_ROL_ID())){
                    $cursosDocente [] = $curso;
                }
                
            }
        }

        return $cursosDocente;
    }

    public static function tieneAccesoCurso($userid,$curso_id){
        $estaMatriculadoDocente = CursoParticipante::where('cvucv_user_id', $userid)
        ->where('cvucv_curso_id',$curso_id)
        ->where('cvucv_rol_id','!=',CursoParticipanteRol::getRolEstudiante())
        ->first();   

        if(empty($estaMatriculadoDocente) ){
            return false;
        } 
        return true;
    }
    

}
