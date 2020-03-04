<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Instrumento;
use App\Curso;
use App\Evaluacion;
use App\Respuesta;
use App\Invitacion;
use App\TipoInvitacion;
use App\MomentosEvaluacion;
use App\Estatus;
use phpDocumentor\Reflection\Types\Boolean;

class PublicController extends Controller
{
    protected $respuestas_save;
    protected $percentil_total_eva;
    protected $respuestas;

    public function evaluacion(Request $request){
        $token          = $request->token;

        $preview = true;
        $preview2 = true;
        if(!isset($request->preview)){$preview = false;}
        if(!isset($request->preview2)){$preview2 = false;}

        if($preview && !$preview2){
            $curso      = null; $periodo_lectivo = null; $momentoActual= null; $usuario = null; $instrumento = $request->instrumento;
            $invitacion = Invitacion::invitacionPrevia2($curso, $request->instrumento, $periodo_lectivo, $momentoActual, $usuario);
            if($invitacion === null){
                //se crea la invitacion para preview
                $invitacion = Invitacion::invitarEvaluador($curso, $instrumento, $periodo_lectivo, $momentoActual, $usuario, TipoInvitacion::getEstatusManual());
            }
            $token = $invitacion->getToken();
        }
        
        $invitacion     = Invitacion::where('token',$token)->first();
        
        
        if (!$preview && is_null($invitacion) || empty($invitacion) || strlen($invitacion) < 1 || $invitacion == null){ 
            return $this->message("Error, invitación para evaluar curso inválida", "error");
        }
        if(!$preview && $invitacion->invitacion_revocada()){ //Invitación revocada
            return $this->message("Error, invitación revocada", "error");
        }
        if(!$preview && $invitacion->invitacion_completada()){
            return $this->message("Ya evaluaste este curso", "error");
        }

        $curso              = $invitacion->curso;
        $instrumento        = $invitacion->instrumento;
        $periodo_lectivo    = $invitacion->periodo;
        $momento_evaluacion = $invitacion->momento_evaluacion;

        if (!$preview && empty($curso)){ 
            return $this->message("Error, el curso no esta disponible en este momento", "error");
        }
        if (empty($instrumento)){ 
            return $this->message("Error, el instrumento no esta disponible en este momento", "error");
        }
        if (!$preview && empty($periodo_lectivo)){ 
            return $this->message("Error, el periodo lectivo no esta disponible en este momento", "error");
        }
        if (!$preview && empty($momento_evaluacion)){
            return $this->message("Error, el momento de evaluacion no esta disponible en este momento", "error");
        }
        if(!$instrumento->esValido()){
            return $this->message("Error, el instrumento de evaluación no se encuentra disponible en este momento, intente más tarde", "error");
        }
        if(!$preview && $instrumento->getPuedeRechazar() && $invitacion->invitacion_rechazada()){
            return $this->message("Rechazaste evaluar este curso", "error");
        }

        $categorias = $instrumento->categoriasCodificadasInstrumento();
        $CategoriasPerfilInstrumento = $categorias["perfil"];
        $CategoriasInstrumento = $categorias["instrumento"];

        //Actualizamos el estatus de la invitacion
        
        if( (!$preview && $invitacion->invitacion_aceptada() && $instrumento->getPuedeRechazar()) || ($preview && $preview2) || (!$preview && $invitacion->invitacion_aceptada() && !$instrumento->getPuedeRechazar())){
            $edit = true;
        }else{
            $invitacion->actualizar_estatus_leida();
            $edit = false;
        }

        return view('public.evaluacion_cursos_link', 
        compact(
        'invitacion',
        'curso', 
        'instrumento',
        'CategoriasPerfilInstrumento',
        'CategoriasInstrumento',
        'periodo_lectivo',
        'momento_evaluacion',
        'edit',
        'preview'));
                
    }
    public function evaluacion_procesar1($token, $invitacion_id,$preview = false,Request $request){
        
        $invitacion     = Invitacion::where('id',$invitacion_id)->first();
        
        if (is_null($invitacion) || empty($invitacion) || strlen($invitacion) < 1 || $invitacion == null){ 
            return $this->message("Error, invitación para evaluar curso inválida", "error");
        }
        
        if (!$invitacion->checkToken($token)){ //Invitación revocada
            return $this->message("Error, invitación erronea", "error");
        }
        if ($invitacion->invitacion_revocada()){ //Invitación revocada
            return $this->message("Error, invitación revocada", "error");
        }
        if($invitacion->invitacion_completada()){
            return $this->message("Ya evaluaste este curso", "error");
        }
        
        $curso              = $invitacion->curso;
        $instrumento        = $invitacion->instrumento;
        $periodo_lectivo    = $invitacion->periodo;
        $momento_evaluacion = $invitacion->momento_evaluacion;
        
        if (!$preview && empty($curso)){ 
            return $this->message("Error, el curso no esta disponible en este momento", "error");
        }
        if (empty($instrumento)){ 
            return $this->message("Error, el instrumento no esta disponible en este momento", "error");
        }
        if (!$preview && empty($periodo_lectivo)){ 
            return $this->message("Error, el periodo lectivo no esta disponible en este momento", "error");
        }
        if(!$preview && empty($momento_evaluacion)){
            return $this->message("Error, el momento de evaluacion no esta disponible en este momento", "error");
        }
        if(!$instrumento->esValido()){
            return $this->message("Error, el instrumento de evaluación no se encuentra disponible en este momento, intente más tarde", "error");
        }
        
        //Verificamos que los campos del request no esten vacíos
        //No puede haber indicadores repetidos
        $categorias                  = $instrumento->categoriasCodificadasInstrumento();
        $CategoriasPerfilInstrumento = $categorias["perfil"];
        $CategoriasInstrumento       = $categorias["instrumento"];
        
        $instrumento_categorias = $CategoriasPerfilInstrumento;
        foreach($instrumento_categorias as $categoria){
            foreach($categoria->indicadoresOrdenados() as $indicador){
                if(!isset($request->{('campo'.$indicador->getID().'_'.$categoria->getID())}) && $indicador->getRequerido() ){  
                    return redirect()
                        ->back()
                        ->withInput()
                        ->with(['message' => "Debe completar el campo: ".$indicador->nombre, 'alert-type' => 'error']);
                }

            }
        }
        
        //Acepto a realizar la evaluacion?
        $acepto = false;
        if($instrumento->getPuedeRechazar() && isset($request->aceptar)){
            if($request->aceptar == "on"){
                $acepto = true;
            }
        }elseif(!$instrumento->getPuedeRechazar()){
            $acepto = true;
        }

        if($preview){
            unset($request);

            if($instrumento->getPuedeRechazar() && !$acepto){
                return $this->message("Hasta la próxima!", "warning");
            }else{            
                return redirect()->route('evaluacion_link', ['token' => $token,'preview' => true, 'preview2' => true]);
            }
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

        $this->caculoPercentil($request, $instrumento_categorias);

        //Guardamos la evaluacion realizada
        $anonimo = $instrumento->getAnonimo();

        $evaluacion = Evaluacion::buscar_evaluacion_token(
            $anonimo, 
            $token,
            null,
            $instrumento->getID(), 
            $curso->getID(), 
            $periodo_lectivo->getID(), 
            $momento_evaluacion->getID(),
            $invitacion->getCvucv_user_id(),
            $invitacion->getUsuario_id()
        );
        if(!empty($evaluacion)){
            return $this->message("Error, no se puede reenviar la peticion, actualice la página", "error");
        }
        
        
        
        $evaluacion = Evaluacion::create(
            $anonimo, 
            $token,//$this->respuestas_save, 
            null,//$this->percentil_total_eva, 
            $instrumento->getID(), 
            $curso->getID(), 
            $periodo_lectivo->getID(), 
            $momento_evaluacion->getID(), 
            $invitacion->getCvucv_user_id() , 
            $invitacion->getUsuario_id(),
            ($instrumento->getPuedeRechazar() && !$acepto ? Estatus::getEstatusRechazada() : Estatus::getEstatusAceptada() ) ) ;

        //Guardamos las respuestas ya procesadas / calculadas
        foreach($this->respuestas as $respuesta){
            foreach($respuesta as $campos){

                Respuesta::create(
                    $campos['value_string'], 
                    $campos['value_percentil'], 
                    $campos['indicador_nombre'], 
                    $campos['indicador_id'], 
                    $campos['categoria_id'], 
                    $evaluacion->id);
            
            }
        }

        //Actualizamos el estatus de la invitacion
        $invitacion->actualizar_estatus_aceptada($acepto);

        unset($request);

        if($instrumento->getPuedeRechazar() && !$acepto){
            return $this->message("Hasta la próxima!", "warning");
        }else{            
            return redirect()->route('evaluacion_link', ['token' => $token]);
        }

        
    }
    public function evaluacion_procesar2($token, $invitacion_id,$preview = false,Request $request){

        $invitacion     = Invitacion::where('id',$invitacion_id)->first();
        
        if (is_null($invitacion) || empty($invitacion) || strlen($invitacion) < 1 || $invitacion == null){ 
            return $this->message("Error, invitación para evaluar curso inválida", "error");
        }
        if (!$invitacion->checkToken($token)){ //Invitación revocada
            return $this->message("Error, invitación erronea", "error");
        }
        if ($invitacion->invitacion_revocada()){ //Invitación revocada
            return $this->message("Error, invitación revocada", "error");
        }
        if($invitacion->invitacion_completada()){
            return $this->message("Ya evaluaste este curso", "error");
        }
        

        $curso              = $invitacion->curso;
        $instrumento        = $invitacion->instrumento;
        $periodo_lectivo    = $invitacion->periodo;
        $momento_evaluacion = $invitacion->momento_evaluacion;

        if (!$preview && empty($curso)){ 
            return $this->message("Error, el curso no esta disponible en este momento", "error");
        }
        if (empty($instrumento)){ 
            return $this->message("Error, el instrumento no esta disponible en este momento", "error");
        }
        if (!$preview && empty($periodo_lectivo)){ 
            return $this->message("Error, el periodo lectivo no esta disponible en este momento", "error");
        }
        if(!$preview && empty($momento_evaluacion)){
            return $this->message("Error, el momento de evaluacion no esta disponible en este momento", "error");
        }
        if(!$instrumento->esValido()){
            return $this->message("Error, el instrumento de evaluación no se encuentra disponible en este momento, intente más tarde", "error");
        }
        if($instrumento->getPuedeRechazar() && $invitacion->invitacion_rechazada()){
            return $this->message("Rechazaste evaluar este curso", "error");
        }
        
        //Verificamos que los campos del request no esten vacíos
        //No puede haber indicadores repetidos
        $categorias                  = $instrumento->categoriasCodificadasInstrumento();
        $CategoriasPerfilInstrumento = $categorias["perfil"];
        $CategoriasInstrumento       = $categorias["instrumento"];

        $instrumento_categorias = $CategoriasInstrumento;

        foreach($instrumento_categorias as $categoria){
            foreach($categoria->indicadoresOrdenados() as $indicador){
                if(!isset($request->{('campo'.$indicador->getID().'_'.$categoria->getID())}) && $indicador->getRequerido() ){  
                    return redirect()
                        ->back()
                        ->withInput()
                        ->with(['message' => "Debe completar el campo: ".$indicador->nombre, 'alert-type' => 'error']);
                }

            }
        }

        if($preview){
            unset($request);

            return $this->message("Evaluacion al curso Nombre del Curso realizada satisfactoriamente", "success");
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

        $this->caculoPercentil($request, $instrumento_categorias);
        
        //Guardamos la evaluacion realizada
        $anonimo = $instrumento->getAnonimo();

        $evaluacion = Evaluacion::buscar_evaluacion_token(
            $anonimo, 
            $token,
            null,
            $instrumento->getID(), 
            $curso->getID(), 
            $periodo_lectivo->getID(), 
            $momento_evaluacion->getID(),
            $invitacion->getCvucv_user_id(),
            $invitacion->getUsuario_id()
        );

        //Generalmente entra aqui cuando el instrumento no tiene categoria perfil
        if(empty($evaluacion)){
            $evaluacion = Evaluacion::create(
            $anonimo, 
            $this->respuestas_save, 
            $this->percentil_total_eva, 
            $instrumento->getID(), 
            $curso->getID(), 
            $periodo_lectivo->getID(), 
            $momento_evaluacion->getID(), 
            ($anonimo ? NULL : $invitacion->getCvucv_user_id()), 
            ($anonimo ? NULL : $invitacion->getUsuario_id()),
            Estatus::getEstatusCompletada()) ;
        }else{
            $evaluacion->actualizarEvaluacion(
            $this->respuestas_save, 
            $this->percentil_total_eva, 
            ($anonimo ? NULL : $invitacion->getCvucv_user_id()) , 
            ($anonimo ? NULL : $invitacion->getUsuario_id()),
            Estatus::getEstatusCompletada()  ) ;
        }

        //Guardamos las respuestas ya procesadas / calculadas
        foreach($this->respuestas as $respuesta){
            foreach($respuesta as $campos){

                Respuesta::create(
                    $campos['value_string'], 
                    $campos['value_percentil'], 
                    $campos['indicador_nombre'], 
                    $campos['indicador_id'], 
                    $campos['categoria_id'], 
                    $evaluacion->id);
            
            }
        }

        //Actualizamos el estatus de la invitacion
        $invitacion->actualizar_estatus_completada();
        
        unset($request);

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

    public function caculoPercentil($request, $instrumento_categorias){
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
        $this->respuestas = array();
        $i = 0;
        //Es para calcular el valor numerico de la evaluación 
        //$percentil_value_categoria = $instrumento->percentilValue();
        $this->percentil_total_eva = 0;

        //Buscamos las categorias del instrumento
        foreach($instrumento_categorias as $categoria){
            $categoria_field = array();
            $j = 0;
            $valorCategoria = $categoria->pivot->valor_porcentual;

            //Recorremos los indicadores del instrumento para calcular su valor numérico
            $categoria_likertOpciones           = $categoria->getLikertType();
            $categoria_likert_cantidad_opciones = $categoria->getLikertCantidadOpciones();

            foreach($categoria->indicadoresOrdenados() as $indicador){
                if(isset($request->{('campo'.$indicador->getID().'_'.$categoria->getID())} )){

                    $valorIndicador = $indicador->pivot->valor_porcentual;
                    $percentil_value_categoria = ($valorIndicador * $valorCategoria) / 100;

                    if($percentil_value_categoria == 0 || !$indicador->esMedible()){
                        $percentil_indicador_actual = -1;
                    }else{

                        //Cantidad de opciones
                        if($indicador->getTipo() == "likert"){
                            $opciones = $categoria_likertOpciones;
                            $percentil_value_opciones = $categoria_likert_cantidad_opciones; 
                            
                        }else{
                            $opciones = $indicador->getOpciones(1);
                            $percentil_value_opciones = count($opciones)-1;
                        }
                        
                        $value_percentil_request  = $indicador->percentilValueRequest($request->{('campo'.$indicador->getID().'_'.$categoria->getID())}, $percentil_value_opciones, $opciones);

                        $percentil_indicador_actual =($percentil_value_categoria/$percentil_value_opciones) * $value_percentil_request;
                        $this->percentil_total_eva = $this->percentil_total_eva + $percentil_indicador_actual;
                    }
                    
                    $categoria_field[$j]['indicador_nombre']= $indicador->nombre;
                    if($indicador->getTipo() != "select_multiple"){
                        $categoria_field[$j]['value_string']    = $request->{('campo'.$indicador->getID().'_'.$categoria->getID())};
                    }else{
                        $categoria_field[$j]['value_string']    = json_encode($request->{('campo'.$indicador->getID().'_'.$categoria->getID())});;
                    }
                    $categoria_field[$j]['value_percentil'] = $percentil_indicador_actual;
                    $categoria_field[$j]['indicador_id']    = $indicador->getID();
                    $categoria_field[$j]['categoria_id']    = $categoria->getID();

                    $j++;
                }
            }
            $this->respuestas[$i] = $categoria_field;
            $i++;
        }
        $this->respuestas_save = json_encode($this->respuestas);
    }
}
