<?php

namespace App;

use App\User;
use App\Evaluacion;
use Illuminate\Database\Eloquent\Model;

class Curso extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'cursos';

    protected $primaryKey = 'id';

    public $incrementing = false;

    protected $fillable = ['id', 'cvucv_shortname', 'cvucv_category_id', 'cvucv_fullname', 'cvucv_displayname', 'cvucv_summary', 'cvucv_link', 'cvucv_visible', 'evaluacion_activa'];

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
                                    if (!Evaluacion::cantidad_evaluaciones_realizadas($instrumento->id, $this->id, $user->id, $categoria_raiz->periodo_lectivo) >= 1){
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

    public function evaluacionProgreso(){
        return $this->evaluacion_activa;
    }
    public function actualizarEvaluacion(bool $value){
        $this->evaluacion_activa = $value;
        $this->save();
    }
}
