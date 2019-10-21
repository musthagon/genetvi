<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Instrumento;
use App\Curso;
use App\CategoriaDeCurso;
use App\CursoParticipante;
use App\Evaluacion;
use App\Respuesta;
use App\PeriodoLectivo;
use App\Categoria;
use App\Indicador;
use App\User;
use App\Invitacion;


class PublicController extends Controller
{

    public function evaluacion($token){
        $invitacion     = Invitacion::where('token',$token)->first();
        

        if (empty($invitacion)){ 
            return redirect()->route('evaluacion_erronea')->withInput()->with(['message' => "Error, invitación para evaluar curso inválida", 'alert-type' => 'error']);
        }
        if($invitacion->invitacionCompletada()){
            return redirect()->route('evaluacion_erronea')->withInput()->with(['message' => "Ya evaluaste este curso", 'alert-type' => 'error']);
        }

        $curso          = Curso::find($invitacion->curso_id);
        $instrumento    = Instrumento::find($invitacion->instrumento_id);
        $periodo        = PeriodoLectivo::find($invitacion->periodo_lectivo_id);

        if (empty($curso) || empty($instrumento) || empty($periodo)){ 
            return redirect()->route('evaluacion_erronea')->withInput()->with(['message' => "Error, el curso o instrumento no estan disponibles en este momento", 'alert-type' => 'error']);
        }

        if(!$instrumento->esValido()){
            return redirect()->route('evaluacion_erronea')->withInput()->with(['message' => "Error, el instrumento de evaluación no se encuentra disponible en este momento", 'alert-type' => 'error']);
        }

        //Actualizamos el estatus de la invitacion
        if($instrumento->puede_rechazar){
            $invitacion->estatus_invitacion_id = 4; //Invitacion aceptada
        }else{
            $invitacion->estatus_invitacion_id = 6; // Invitacion leída
        }
        $invitacion->save();
        
        return view('user.evaluacion_cursos_link', compact('invitacion','instrumento','curso'));
                
    }
    public function evaluacion_procesar($invitacion_id,Request $request){
        $invitacion     = Invitacion::find($invitacion_id);
        
        if (empty($invitacion)){ 
            return redirect()->route('evaluacion_erronea')->with(['message' => "Error, invitación para evaluar curso inválida", 'alert-type' => 'error']);
        }
        if($invitacion->invitacionCompletada()){
            return redirect()->route('evaluacion_erronea')->with(['message' => "Ya evaluaste este curso", 'alert-type' => 'error']);
        }

        $curso          = Curso::find($invitacion->curso_id);
        $instrumento    = Instrumento::find($invitacion->instrumento_id);
        
        //1. Verificamos que el curso y el instrumento existan
        if (empty($curso) || empty($instrumento)){ 
            return redirect()->route('evaluacion_erronea')->with(['message' => "Error, el curso o instrumento no estan disponibles en este momento", 'alert-type' => 'error']);
        }

        //Verificamos que los campos del request no esten vacíos
        foreach($instrumento->categorias as $categorias){
            foreach($categorias->indicadores as $indicador){
                if(!isset($request->{($indicador->id)} )){  
                    return redirect()->route('evaluacion_erronea')->with(['message' => "Debe completar el campo: ".$indicador->nombre, 'alert-type' => 'error']);
                }

            }
        }

        //3. Verificamos que el instrumento sea valido
        if(!$instrumento->esValido()){
            return redirect()->route('evaluacion_erronea')->with(['message' => "Error, el instrumento de evaluación no se encuentra disponible en este momento", 'alert-type' => 'error']);
        }

        $categoria_raiz             = $curso->categoria->categoria_raiz;
        $instrumentos_habilitados   = $categoria_raiz->instrumentos_habilitados;

        //4. Verificamos que la categoria del curso tenga instrumentos habilitados para evaluar
        if(!empty($instrumentos_habilitados)){
            foreach($instrumentos_habilitados as $actual){

                //5. Verificamos que el curso puede ser evaluado por el instrumento (pasado por parametro)
                if($actual->id == $instrumento->id){
                             
                    //Procesamos las respuestas
                    $respuestas = array();
                    $i = 0;
                    //Es para calcular el valor numerico de la evaluación 
                    $percentil_value_categoria = $instrumento->percentilValue();
                    $percentil_total_eva = 0;
                    $percentil_value_opciones = 2; //Opciones (Siempre y a veces) el Nunca, es 0
                    foreach($instrumento->categorias as $categoria){
                        $categoria_field = array();
                        $j = 0;
                        $percentil_value_indicadores = $percentil_value_categoria/$categoria->percentilValue();
                        foreach($categoria->indicadores as $indicador){
                            if(isset($request->{($indicador->id)} )){
                                
                                $value_string = "Nunca";
                                $value_percentil_request = 0;
                                switch ($request->{($indicador->id)}) {
                                    case "2":
                                        $value_string = "Siempre";
                                        $value_percentil_request = 2;
                                    break;
                                    case "1":
                                        $value_string = "A veces";
                                        $value_percentil_request = 1;
                                    break;
                                }
                                $percentil_indicador_actual =($percentil_value_indicadores/$percentil_value_opciones) * $value_percentil_request;
                                $percentil_total_eva = $percentil_total_eva + $percentil_indicador_actual;

                                $categoria_field[$j]['indicador_nombre']= $indicador->nombre;
                                $categoria_field[$j]['value_string']    = $value_string;
                                $categoria_field[$j]['value_request']   = $request->{($indicador->id)};
                                $categoria_field[$j]['value_percentil'] = $percentil_indicador_actual;
                                $categoria_field[$j]['indicador_id']    = $indicador->id;
                                $categoria_field[$j]['categoria_id']    = $categoria->id;

                                $j++;
                            }
                        }
                        $respuestas[$i] = $categoria_field;
                        $i++;
                    }
                    $respuestas_save = json_encode($respuestas);

                    //Guardamos la evaluacion realizada
                    $evaluacion = new Evaluacion;

                    $evaluacion->respuestas          = $respuestas_save;
                    $evaluacion->percentil_eva       = $percentil_total_eva;
                    $evaluacion->instrumento_id      = $instrumento->id;
                    $evaluacion->curso_id            = $curso->id;
                    /*$evaluacion->usuario_id          = $user->id;*/
                    $evaluacion->cvucv_user_id          = $invitacion->cvucv_user_id;
                    $evaluacion->periodo_lectivo_id  = $categoria_raiz->periodo_lectivo;

                    $evaluacion->save();

                    foreach($respuestas as $respuesta){
                        foreach($respuesta as $campos){
                            $respuesta = new Respuesta;

                            $respuesta->value_string     = $campos['value_string'];
                            $respuesta->value_request    = $campos['value_request'];
                            $respuesta->value_percentil  = $campos['value_percentil'];
                            $respuesta->indicador_nombre = $campos['indicador_nombre'] ;
                            $respuesta->indicador_id     = $campos['indicador_id'];
                            $respuesta->categoria_id     = $campos['categoria_id'] ;
                            $respuesta->evaluacion_id    = $evaluacion->id;
                            
                            $respuesta->save();
                        }
                    }


                    //Actualizamos el estatus de la invitacion
                    $invitacion->estatus_invitacion_id = 7; //Invitacion completada
                    $invitacion->save();

                    return redirect()->route('evaluacion_satisfactoria')->with(['message' => "Evaluacion al curso ".$curso->cvucv_fullname." realizada satisfactoriamente", 'alert-type' => 'gracias']);
                }
            }
            
        }
    }

    public function message(){
        
        $message    = session()->get('message');
        $alert_type = session()->get('alert-type'); 

        if( !isset($message) || !isset($alert_type)){
            $message    = "La página que buscas, ya no se encuentra disponible.";
            $alert_type = "Error"; 
        }
        return view('user.evaluacion_cursos_link', compact('message','alert_type'));
    }
}
