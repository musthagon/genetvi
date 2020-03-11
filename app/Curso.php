<?php

namespace App;

use App\User;
use App\Evaluacion;
use Illuminate\Database\Eloquent\Model;
use App\Traits\CommonFunctionsGenetvi; 
use App\TipoInvitacion;
class Curso extends Model
{
    use CommonFunctionsGenetvi;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'cursos';

    protected $primaryKey = 'id';

    public $incrementing = false;

    protected $fillable = ['id', 'cvucv_shortname', 'cvucv_category_id', 'cvucv_fullname', 'cvucv_displayname', 'cvucv_summary', 'cvucv_link', 'cvucv_visible', 'evaluacion_activa','created_at','updated_at'];

    public static function existe_curso($cvucv_course_id){
        return Curso::query()
            ->where('id', $cvucv_course_id)
            ->first();
    }

    public static function create($id, $cvucv_shortname, $cvucv_category_id, $cvucv_fullname, $cvucv_displayname, $cvucv_summary, $cvucv_visible)    {
        $new = new self();

        $new->id                   = $id;
        $new->cvucv_shortname      = $cvucv_shortname;
        $new->cvucv_category_id    = $cvucv_category_id;
        $new->cvucv_fullname       = $cvucv_fullname;
        $new->cvucv_displayname    = $cvucv_displayname;
        $new->cvucv_summary        = $cvucv_summary;
        $new->cvucv_visible        = $cvucv_visible;
        
        return $new;
    }

    public function categoria(){
        return $this->belongsTo('App\CategoriaDeCurso','cvucv_category_id','id');
    }
    public function es_categoria_del_curso($categoria_id){
        return ($this->categoria->id == $categoria_id);
    }

    public function instrumentos_disponibles_usuario($user_id, $curso_id){

        $instrumentos_disponibles = collect();
        $user          = User::find($user_id);

        if(!empty($this) && !empty($user)){
            $categoria = $this->categoria;
            
            if(!empty($categoria)){ 
                $categoria_raiz = $this->categoria->categoria_raiz;
                if(!empty($categoria_raiz)){ 
                    $instrumentos_habilitados = $categoria_raiz->instrumentos_habilitados;
                    //4. Verificamos que la categoria del curso tenga instrumentos habilitados para evaluar
                    if(!empty($instrumentos_habilitados)){ 

                        foreach($instrumentos_habilitados as $instrumento){
                            //3. Verificamos que el instrumento sea valido
                            if($instrumento->esValido()){
                                //6. Verificamos que el instrumento va dirigido al usuario con ese rol
                                $rolUsuarioCurso = CursoParticipante::where('cvucv_user_id', $user->cvucv_id)
                                ->where('cvucv_curso_id',$curso_id)
                                ->first()->cvucv_rol_id;

                                $instrumento_dirigido_usuario = $instrumento->instrumentoDirigidoaRol($rolUsuarioCurso);

                                if($instrumento_dirigido_usuario){


                                    //7. Verificamos la cantidad de intentos de evaluacion del instrumento
                                    if (!Evaluacion::cantidad_evaluaciones_realizadas($instrumento->id, $this->id, $user->cvucv_user_id, $categoria_raiz->periodo_lectivo) >= 1){
                                        $instrumentos_disponibles->push($instrumento);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        return $instrumentos_disponibles;
    }

    public function participantes(){
        return $this->hasMany('App\CursoParticipante','cvucv_curso_id','id');
    }

    public function responsablesCurso(){
        return $this->belongsToMany('App\User','cursos_participantes','cvucv_curso_id','user_id')->using('App\CursoParticipante');
    }

    public function getEvaluacionActiva(){
        return $this->evaluacion_activa;
    }
    public function actualizarEvaluacion(bool $value){
        $this->evaluacion_activa = $value;
        $this->save();
    }
    public function periodo_lectivo_actual(){
        return $this->categoria->categoria_raiz->periodo_lectivo_actual;
    }

    public function getNombre(){
        return $this->cvucv_fullname;
    }
    public function getDescripcion(){
        return $this->cvucv_summary;
    }

    public function getID(){
        return $this->id;
    }

    public function verificarInvitacionesAlMomentoActual($instrumentos_habilitados, $periodo_lectivo, $momento_evaluacion_activo){
        //Buscamos los participantes
        $participantes = $this->cvucv_get_participantes_curso($this->getID());
        
        foreach($instrumentos_habilitados as $instrumento){
            //El instrumento es de matriculacion automatica
            if($instrumento->getInvitacionAutomatica()){ 
                
                foreach($participantes as $indexParticipante => $participante){
                    
                    //Verificamos que el instrumento va a dirigido al usuario
                    if(isset($participante['roles']) && !empty($participante['roles'])){
                        
                            $rolUsuarioCurso = $participante['roles'][0]['roleid'];
                            
                            //Verificamos que el instrumento va a dirigido al usuario
                            if ($instrumento->instrumentoDirigidoaRol($rolUsuarioCurso)){
                                
                                //Verificamos que no tenga invitación previa
                                if(!Invitacion::invitacionPrevia($this->getID(), $instrumento->getID(), $periodo_lectivo->getID(), $momento_evaluacion_activo->getID(), $participante['id']) ){
                                    
                                    Invitacion::invitarEvaluador($this->getID(), $instrumento->getID(), $periodo_lectivo->getID(), $momento_evaluacion_activo->getID(), $participante['id'], TipoInvitacion::getEstatusAutomatica());
                                }
                            }
                        
                    }

                }
            }
        }
        
    }

    public static function CantidadCursosEvaluacionesActivas($nombre = null){
        if($nombre == null){
            return Curso::where('evaluacion_activa','1')
            ->count();
        }

        $cursos = Curso::where('evaluacion_activa','1')->get();
        $id_categoria_padre = CategoriaDeCurso::getCategoriaPorNombre($nombre);
        $count = 0;
        foreach($cursos as $curso){
            if($curso->categoria->categoria_raiz->getID() == $id_categoria_padre){
                $count++;
            }
        }

        return $count;
    }

    public static function CursosEvaluacionesActivas($nombre = null){
        if($nombre == null){
            return Curso::where('evaluacion_activa','1')
            ->get();
        }

        $cursos = Curso::where('evaluacion_activa','1')->get();
        $id_categoria_padre = CategoriaDeCurso::getCategoriaPorNombre($nombre);
        $cursos_evaluaciones_activas = [];
        foreach($cursos as $curso){
            if($curso->categoria->categoria_raiz->getID() == $id_categoria_padre){
                $cursos_evaluaciones_activas[] = $curso;
            }
        }

        return $cursos_evaluaciones_activas;
    }

}
