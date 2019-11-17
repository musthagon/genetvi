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

        //Verificamos la invitación
        if (empty($invitacion)){ 
            return $this->message("Error, invitación para evaluar curso inválida", "error");
        }
        if ($invitacion->invitacion_revocada()){ //Invitación revocada
            return $this->message("Error, invitación revocada", "error");
        }
        if($invitacion->invitacion_completada()){
            return $this->message("Ya evaluaste este curso", "error");
        }

        $curso          = $invitacion->curso;
        $instrumento    = $invitacion->instrumento;
        $periodo        = $invitacion->periodo;

        if (empty($curso) || empty($instrumento) || empty($periodo)){ 
            return $this->message("Error, el curso o instrumento no estan disponibles en este momento", "error");
        }

        if(!$instrumento->esValido()){
            return $this->message("Error, el instrumento de evaluación no se encuentra disponible en este momento", "error");
        }

        //Actualizamos el estatus de la invitacion
        $invitacion->actualizar_estatus_leida();
        
        return view('public.evaluacion_cursos_link', compact('invitacion','curso', 'instrumento','periodo'));
                
    }
    public function evaluacion_procesar($invitacion_id,Request $request){
        $invitacion     = Invitacion::find($invitacion_id);
        
        if (empty($invitacion)){ 
            return $this->message("Error, invitación para evaluar curso inválida", "error");
        }
        if ($invitacion->invitacion_revocada()){ 
            return $this->message("Error, invitación revocada", "error");
        }
        if($invitacion->invitacion_completada()){
            return $this->message("Ya evaluaste este curso", "error");
        }

        $curso          = Curso::find($invitacion->curso_id);
        $instrumento    = Instrumento::find($invitacion->instrumento_id);
        
        //1. Verificamos que el curso y el instrumento existan
        if (empty($curso) || empty($instrumento)){ 
            return $this->message("Error, el curso o instrumento no estan disponibles en este momento", "error");
        }

        //Verificamos que los campos del request no esten vacíos
        $instrumento_categorias = $instrumento->categoriasOrdenadas();
        foreach($instrumento_categorias as $categorias){
            foreach($categorias->indicadoresOrdenados() as $indicador){
                if(!isset($request->{($indicador->id)}) && $indicador->getRequerido() ){  
                    return redirect()
                        ->back()
                        ->withInput()
                        ->with(['message' => "Debe completar el campo: ".$indicador->nombre, 'alert-type' => 'error']);
                }

            }
        }
        //3. Verificamos que el instrumento sea valido
        if(!$instrumento->esValido()){
            return $this->message("Error, el instrumento de evaluación no se encuentra disponible en este momento", "error");
        }

        $categoria_raiz             = $curso->categoria->categoria_raiz;
        $instrumentos_habilitados   = $categoria_raiz->instrumentos_habilitados;

        //4. Verificamos que la categoria del curso tenga instrumentos habilitados para evaluar
        if(empty($instrumentos_habilitados)){
            return $this->message("Error, el instrumento de evaluación no se encuentra disponible en este momento", "error");
        }

        $instrumento_disponible = false;
        foreach($instrumentos_habilitados as $actual){
            //5. Verificamos que el curso puede ser evaluado por el instrumento (pasado por parametro)
            if($actual->id == $instrumento->id){
                $instrumento_disponible = true;
            }
        } 
        if(!$instrumento_disponible){
            return $this->message("Error, el instrumento de evaluación no se encuentra disponible en este momento", "error");
        }

        
        /***********
         *  Cálculo del valor porcentual de los indicadores
         *  Supongamos que tenemos el siguiente instrumento:
         * 
         * Instrumento1
         * 
         *      Total categorias debe ser 100%
         *  -Categoria1 (Valor porcentual 40%)
         * 
         *        Total indicadores debe ser 100%
         *      .Indicador1 (Valor porcentual 40%) (Tiene 2 opciones de respuesta)
         *      .Indicador2 (Valor porcentual 60%) (Tiene 4 opciones de respuesta)
         * 
         *  -Categoria2 (Valor porcentual 30%)
         * 
         *        Total indicadores debe ser 100%
         *      .Indicador3 (Valor porcentual 100%) (Tiene 3 opciones de respuesta)
         *      .Indicador4 (Valor porcentual 0%) (Tiene 3 opciones de respuesta)
         * 
         *  -Categoria3 (Valor porcentual 30%)
         * 
         *        Total indicadores debe ser 100%
         *      .Indicador5 (Valor porcentual 20%) (Tiene 2 opciones de respuesta)
         *      .Indicador6 (Valor porcentual 80%) (Tiene 1 opciones de respuesta)
         * 
         * -----------------------------------------------------------------------------
         * Entonces calculamos,
         * 
         *  INDICADOR1 (Valor porcentual 40%) (Tiene 2 opciones de respuesta, percentil_value_opciones = 2)
         *
         *  percentil_value_categoria1 = Indicador1 * Categoria1 / 100% ------------> Una regla de tres
         *  percentil_value_categoria1 = (40% * 40%) / 100% 
         *  percentil_value_categoria1 = 16
         * 
         *  percentil_value_opciones1 = percentil_value_categoria1 / (percentil_value_opciones - 1)
         *  percentil_value_opciones1 = 16 / (2 - 1)
         *  percentil_value_opciones1 = 16 / 1
         *  percentil_value_opciones1 = 16
         * 
         *  Ahora calculamos el valor dependiendo de la opcion selecciona:
         *  Recorremos las opciones desde 0 hasta la cantidad de opciones - 1 (percentil_value_opciones)
         * 
         *  Opcion1 (value_percentil_request = 0):
         *  percentil_indicador_actual1 = percentil_value_opciones1 * value_percentil_request --------> Respuesta seleccionada
         *  percentil_indicador_actual1 = percentil_value_opciones * 0 (Representa que respondio la primera opción = NO, ejem)
         *  percentil_indicador_actual1 = 16 * 0
         *  percentil_indicador_actual1 = 0 -> Si responde no, entonces el valor de la respuesta es 0
         * 
         *  Opcion2 (value_percentil_request = 1):
         *  percentil_indicador_actual1 = percentil_value_opciones1 * value_percentil_request --------> Respuesta seleccionada
         *  percentil_indicador_actual1 = percentil_value_opciones * 1 (Representa que respondio la primera opción = SI, ejem)
         *  percentil_indicador_actual1 = 16 * 1
         *  percentil_indicador_actual1 = 16 -> Si responde si, entonces el valor de la respuesta es 16
         * 
         * 
         *  INDICADOR2 (Valor porcentual 60%) (Tiene 4 opciones de respuesta, percentil_value_opciones = 4)
         * 
         *  percentil_value_categoria2 = Indicador2 * Categoria1 / 100% ------------> Una regla de tres
         *  percentil_value_categoria2 = (60% * 40%) / 100% 
         *  percentil_value_categoria2 = 24
         * 
         *  percentil_value_opciones2 = percentil_value_categoria2 / (percentil_value_opciones - 1)
         *  percentil_value_opciones2 = 24 / (4 - 1)
         *  percentil_value_opciones2 = 24 / 3
         *  percentil_value_opciones2 = 8
         * 
         *  Ahora calculamos el valor dependiendo de la opcion selecciona:
         *  Recorremos las opciones desde 0 hasta la cantidad de opciones - 1 (percentil_value_opciones)
         * 
         *  Opcion1 (value_percentil_request = 0):
         *  percentil_indicador_actual2 = percentil_value_opciones2 * value_percentil_request --------> Respuesta seleccionada
         *  percentil_indicador_actual2 = percentil_value_opciones2 * 0 (Representa que respondio la primera opción = Nunca, ejem)
         *  percentil_indicador_actual2 = 8 * 0
         *  percentil_indicador_actual2 = 0 
         * 
         *  Opcion2 (value_percentil_request = 1):
         *  percentil_indicador_actual2 = percentil_value_opciones2 * value_percentil_request --------> Respuesta seleccionada
         *  percentil_indicador_actual2 = percentil_value_opciones2 * 1 (Representa que respondio la primera opción = A veces, ejem)
         *  percentil_indicador_actual2 = 8 * 1
         *  percentil_indicador_actual2 = 8 
         * 
         *  Opcion3 (value_percentil_request = 2):
         *  percentil_indicador_actual2 = percentil_value_opciones2 * value_percentil_request --------> Respuesta seleccionada
         *  percentil_indicador_actual2 = percentil_value_opciones2 * 2 (Representa que respondio la primera opción = La mayoría del tiempo, ejem)
         *  percentil_indicador_actual2 = 8 * 2
         *  percentil_indicador_actual2 = 16 
         * 
         *  Opcion4 (value_percentil_request = 3):
         *  percentil_indicador_actual2 = percentil_value_opciones2 * value_percentil_request --------> Respuesta seleccionada
         *  percentil_indicador_actual2 = percentil_value_opciones2 * 3 (Representa que respondio la primera opción = SI, ejem)
         *  percentil_indicador_actual2 = 8 * 3
         *  percentil_indicador_actual2 = 24 
         */
                            
        //Procesamos las respuestas
        $respuestas = array();
        $i = 0;
        //Es para calcular el valor numerico de la evaluación 
        //$percentil_value_categoria = $instrumento->percentilValue();
        $percentil_total_eva = 0;

        //Buscamos las categorias del instrumento
        foreach($instrumento_categorias as $categoria){
            $categoria_field = array();
            $j = 0;
            //$categoria_percentilValue = $categoria->percentilValue();

            $valorCategoria = $categoria->pivot->valor_porcentual;
            $percentil_value_indicadores = 0;
            /*if($categoria_percentilValue != 0){
                $percentil_value_indicadores = $percentil_value_categoria/$categoria_percentilValue;
            }*/

            //Recorremos los indicadores del instrumento para calcular su valor numérico
            $categoria_likertType = $categoria->likertType();
            $categoria_likertOpciones = $categoria->likertOpciones();
            foreach($categoria->indicadoresOrdenados() as $indicador){
                if(isset($request->{($indicador->id)} )){

                    $valorIndicador = $indicador->pivot->valor_porcentual;
                    $percentil_value_categoria = ($valorIndicador * $valorCategoria) / 100;

                    /*if($categoria_percentilValue == 0 || !$indicador->esMedible()){
                        $percentil_indicador_actual = -1;
                    }
                    */
                    if($percentil_value_categoria == 0 || !$indicador->esMedible()){
                        $percentil_indicador_actual = -1;
                    }else{

                        $percentil_value_opciones = $indicador->percentilValueOpciones($categoria_likertType); //Cantidad de opciones
                        $value_percentil_request = $indicador->percentilValueRequest($request->{($indicador->id)}, $percentil_value_opciones, $categoria_likertOpciones);

                        $percentil_indicador_actual =($percentil_value_categoria/$percentil_value_opciones) * $value_percentil_request;
                        $percentil_total_eva = $percentil_total_eva + $percentil_indicador_actual;
                    }
                    
                    $categoria_field[$j]['indicador_nombre']= $indicador->nombre;
                    if($indicador->getTipo() != "select_multiple"){
                        $categoria_field[$j]['value_string']    = $request->{($indicador->id)};
                        /*$categoria_field[$j]['value_request']   = $request->{($indicador->id)};*/
                    }else{
                        $categoria_field[$j]['value_string']    = json_encode($request->{($indicador->id)});;
                        /*$categoria_field[$j]['value_request']   = "";*/
                    }
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
        $evaluacion->cvucv_user_id       = $invitacion->cvucv_user_id;
        $evaluacion->periodo_lectivo_id  = $categoria_raiz->periodo_lectivo;

        $evaluacion->save();

        //Guardamos las respuestas ya procesadas / calculadas
        foreach($respuestas as $respuesta){
            foreach($respuesta as $campos){
                $respuesta = new Respuesta;

                $respuesta->value_string     = $campos['value_string'];
                /*$respuesta->value_request    = $campos['value_request'];*/
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

        return $this->message("Evaluacion al curso ".$curso->cvucv_fullname." realizada satisfactoriamente", "success");
        
    
    }

    public function message($message, $alert_type){
        
        $message    = $message;
        $alert_type = $alert_type; 

        if( !isset($message) || !isset($alert_type)){
            $message    = "La página que buscas, ya no se encuentra disponible.";
            $alert_type = "Error"; 
        }
        return view('public.evaluacion_cursos_link', compact('message','alert_type'));
    }
}
