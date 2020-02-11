<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\CommonFunctionsGenetvi; 

class PeriodoLectivo extends Model
{
    use CommonFunctionsGenetvi;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'periodos_lectivos';

    protected $fillable = ['id', 'nombre', 'descripcion', 'opciones', 'fecha_inicio', 'fecha_fin', 'momento_evaluacion_activo_id','created_at','updated_at'];

    public function getID(){
        return $this->id;
    }
    public function getNombre(){
        return $this->nombre;
    }
    public function getDescripcion(){
        return $this->descripcion;
    }
    public function getFecha_inicio(){
        return $this->fecha_inicio;
    }
    public function getFecha_fin(){
        return $this->fecha_fin;
    }
    public function getMomento_evaluacion_activo_id(){
        return $this->momento_evaluacion_activo_id;
    }

    public function categorias(){
        return $this->hasMany('App\CategoriaDeCurso','periodo_lectivo_id','id');
    }

    public function momento_evaluacion_actual(){
        return $this->belongsTo('App\MomentosEvaluacion','momento_evaluacion_activo_id','id');
    }

    public function setMomento_evaluacion_activo($momento){
        $this->momento_evaluacion_activo_id = $momento;
        $this->save();
    }

    public function momentos_evaluacion(){
        return $this->belongsToMany('App\MomentosEvaluacion','periodos_lectivos_momentos_evaluacion','periodo_lectivo_id','momento_evaluacion_id')->using('App\PeriodoLectivoMomentoEvaluacion')
        ->withPivot(
            PeriodoLectivoMomentoEvaluacion::get_fecha_inicio_field(),
            PeriodoLectivoMomentoEvaluacion::get_fecha_fin_field(),
            PeriodoLectivoMomentoEvaluacion::get_opciones_field() );
    }

    public function actualizarMomentoEvaluacion(){
        $fecha_inicio   = $this->getFecha_inicio();
        $fecha_fin      = $this->getFecha_fin();

        $momentosAsociados = $this->momentos_evaluacion;
        $fecha_actual = date("m-d-Y H:i:s", strtotime( \Carbon\Carbon::now()));
        //dd($fecha_actual);
        foreach($momentosAsociados as $index => $momento){
            $fecha_inicio_momento   = date("m-d-Y H:i:s",strtotime( $momento->pivot->get_fecha_inicio() ));
            $fecha_fin_momento      = date("m-d-Y H:i:s",strtotime( $momento->pivot->get_fecha_fin() ));

            if($fecha_actual >= $fecha_inicio_momento && $fecha_actual <= $fecha_fin_momento){
                $this->setMomento_evaluacion_activo($momento->id);
                return $this->getMomento_evaluacion_activo_id();
            }
        }
        $this->setMomento_evaluacion_activo(NULL);
        return $this->getMomento_evaluacion_activo_id();
    }

    public function cambioMomentoEvaluacion($momentoAnterior, $momentoActualizado){
        if($momentoActualizado == null){
            return false;
        }
        if($momentoAnterior == $momentoActualizado){
            return false;
        }
        return true;
    }

    public function invitacionMasivaAutomatica(){
        $categorias = $this->categorias;
        $momento_evaluacion_activo = $this->momento_evaluacion_actual;

        foreach($categorias as $index => $categoriaPrincipal){

            $categoria_raiz             = $categoriaPrincipal->categoria_raiz;
            $instrumentos_habilitados   = $categoria_raiz->instrumentos_habilitados;
            $categorias_raiz_hijas      = $categoria_raiz->categoria_raiz_hijos;
            foreach($categorias_raiz_hijas  as $index2 => $categoria){
                $cursos                     = $categoria->cursos;
                //1. Buscamos la categoria que tiene este periodo
                foreach($cursos  as $cursoIndex => $curso){
                    //2. Buscamos los cursos con evaluacion activa
                    if($curso->getEvaluacionActiva()){
                        //Buscamos los participantes
                        $participantes = $this->cvucv_get_participantes_curso($curso->getID());
                        foreach($instrumentos_habilitados as $instrumentoIndex => $instrumento){
                            //El instrumento es de matriculacion automatica
                            if($instrumento->getInvitacionAutomatica()){ 
                                foreach($participantes as $indexParticipante => $participante){
                                    //Verificamos que el instrumento va a dirigido al usuario
                                    if(isset($participante['roles']) && !empty($participante['roles'])){
                                        $rolUsuarioCurso = $participante['roles'][0]['roleid'];
                                        //Verificamos que el instrumento va a dirigido al usuario
                                        if ($instrumento->instrumentoDirigidoaRol($rolUsuarioCurso)){
                                            //3. Realizamos las invitaciones si no tiene
                                            if(!Invitacion::invitacionPrevia($curso->getID(), $instrumento->getID(), $this->getID(), $momento_evaluacion_activo->getID(), $participante['id']) ){   
                                                //dd('ye');          
                                                Invitacion::invitarEvaluador($curso->getID(), $instrumento->getID(), $this->getID(), $momento_evaluacion_activo->getID(), $participante['id'], TipoInvitacion::getEstatusAutomatica());
                                            }
                                            //dd('nouuu'); 
                                            //4. Enviamos el mensaje de invitacion
                                        }

                                    }
                                }
                            
                            }
                            
                        }
                        
                        

                        
                        //4. Correo al profesor del curso 
                        //5. Correo al administrador de la categoria?

                        //6. Verificamos instrumentos de invitacion manual
                    }
                }
            }
        }
    }
}
