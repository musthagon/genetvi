<?php 

namespace App\Traits;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
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
use App\Charts\indicadoresChart;
use Yajra\Datatables\Datatables;

trait CommonFunctionsCharts
{

	protected $dashboards_subtitle = 'Fuente: GENETVI ©2020 Sistema de Educación a Distancia de la Universidad Central de Venezuela.';



	public function consultar_grafico_indicadores(Request $request){//Crea la data de los dashboards consultados dinamicamente fetch() JS
        $curso_id       = $request->curso_id;
        $periodo_id     = $request->periodo_id;
        $instrumento_id = $request->instrumento_id;
        $categoria_id   = $request->categoria_id;
        $indicador_id   = $request->indicador_id;
        
        $chart = new indicadoresChart;
        
        if(!isset($curso_id) || !isset($periodo_id) || !isset($instrumento_id) || !isset($categoria_id) || !isset($indicador_id)){
            return json_encode (json_decode ("{}"));
        }
        
        $curso          = Curso::find($curso_id);
        $instrumento    = Instrumento::find($instrumento_id);
        $periodo        = PeriodoLectivo::find($periodo_id);
        $categoria      = Categoria::find($categoria_id);
        $indicador      = Indicador::find($indicador_id);

        if(empty($categoria) || empty($indicador) || empty($periodo) || empty($instrumento) || empty($curso)){
            return json_encode (json_decode ("{}"));
        }
        
        //Momentos de evaluación
        $momentos_evaluacion_collection = Evaluacion::momentos_de_evaluacion_del_curso($curso->getID());

        if($indicador->esMedible() && $indicador->esLikert()){
            $opciones_instrumento = $categoria->getLikertType();
        }elseif($indicador->esMedible() && !$indicador->esLikert()){
            $opciones_instrumento = $indicador->getOpciones(1);   
        }else{
            return null;
        }

        $countEmpty = 0;
        $countTotal = 0;

        foreach($momentos_evaluacion_collection as $momentoIndex => $momento){
            $k=0;
            $query = [];

            
            foreach($opciones_instrumento as $key=>$opcion){
                $valor = Evaluacion::cantidad_respuestas_para_indicador($curso, $instrumento, $periodo, $momento, $indicador, $opcion);
                
                $query[$k] = $valor;
                if($valor == null){
                    $countEmpty++;
                }
                $countTotal++;
                $k++;
                
            }

            $chart->dataset($momento->getNombre(), 'bar',$query);
            
        }
        
        if($countEmpty == $countTotal){
            return null;
        }
        
        return $chart->api();
    }

    public function consultar_tabla_indicador($curso_id,$periodo_id,$instrumento_id,$categoria_id,$indicador_id){//Crea datatable de indicadores noMedibles Text, textarea
        
        $result = collect();

        if(!isset($curso_id) || !isset($periodo_id) || !isset($instrumento_id) || !isset($categoria_id) || !isset($indicador_id)){
            return $result;
        }
        $curso          = Curso::find($curso_id);
        $instrumento    = Instrumento::find($instrumento_id);
        $periodo        = PeriodoLectivo::find($periodo_id);
        $categoria      = Categoria::find($categoria_id);
        $indicador      = Indicador::find($indicador_id);

        if(empty($categoria) || empty($indicador) || empty($periodo) || empty($instrumento) || empty($curso)){
            return $result;
        }
        //$opciones_instrumento = $indicador->indicadorOpciones($categoria->likertOpciones());
        
        $respuestas = Evaluacion::respuestas_del_indicador($curso_id, $periodo_id, $instrumento_id, $categoria_id, $indicador_id);

        return Datatables::of($respuestas)->make(true);;
        
        
    }
    public function consultar_grafico_generales(Request $request){//Crea la data de los dashboards consultados dinamicamente fetch() JS
        $curso_id           = $request->curso;
        $tipo               = $request->tipo;
        $periodo_lectivo_id = $request->periodo_lectivo;
        $periodos_collection = $request->periodos;

        if(!isset($curso_id) || !isset($tipo)){
            return json_encode (json_decode ("{}"));
        }

        if(!isset($periodo_lectivo_id) && !isset($periodos_collection)){
            return json_encode (json_decode ("{}"));
        }

        $curso    = Curso::find($curso_id);
        if(empty($curso) ){
            return json_encode (json_decode ("{}"));
        }

        //instrumentos con los cuales han evaluado este curso
        Evaluacion::instrumentos_de_evaluacion_del_curso($curso->getID(), $instrumentos_collection, $nombreInstrumentos);
        
        
        $chart = new indicadoresChart;
        
        //Charts por indicadores de categora, en instrumento en un periodo lectivo
        $query = [];
        $countEmpty = 0;
        $countTotal = 0;
        if($tipo == "1" || $tipo == "2" || $tipo == "5"){
            //periodos lectivos con los cuales han evaluado este curso
            $periodos_collection = Evaluacion::periodos_lectivos_de_evaluacion_del_curso($curso->getID());
            foreach($instrumentos_collection as $instrumento_index=>$instrumento){
                foreach($periodos_collection as $periodo_index=>$periodo){
                
                    if($tipo == "1"){
                        $query[$instrumento_index][$periodo_index] = Evaluacion::cantidad_evaluaciones0($periodo,$instrumento,$curso);
                    }elseif($tipo == "2"){
                        $query[$instrumento_index][$periodo_index] = Evaluacion::promedio_evaluaciones0($periodo,$instrumento,$curso);
                    }elseif($tipo == "5"){
                        $query[$instrumento_index][$periodo_index] = Evaluacion::cantidad_evaluaciones00($periodo,$instrumento,$curso);
                    }

                    if($query == null){
                        $countEmpty++;
                    }
                    $countTotal++;

                }
                $chart->dataset($instrumento->getNombre(), 'bar', $query[$instrumento_index]);
                
            }
        }elseif($tipo == "3" || $tipo == "4" || $tipo == "6"){
            //Momentos de evaluación
            $momentos_evaluacion_collection = Evaluacion::momentos_de_evaluacion_del_curso($curso->getID());
            $periodo  = PeriodoLectivo::find($periodo_lectivo_id);
            if(empty($periodo)){
                return json_encode (json_decode ("{}"));
            }
            foreach($momentos_evaluacion_collection as $momentoIndex => $momento){
                foreach($instrumentos_collection as $instrumento_index=>$instrumento){   

                    if($tipo == "3"){
                        $query [$instrumento_index] = Evaluacion::cantidad_evaluaciones1($periodo,$momento,$instrumento,$curso);
                    }elseif($tipo == "4"){
                        $query [$instrumento_index] = Evaluacion::promedio_evaluaciones1($periodo,$momento,$instrumento,$curso);
                    }elseif($tipo == "6"){
                        $query [$instrumento_index] = Evaluacion::cantidad_evaluaciones11($periodo,$momento,$instrumento,$curso);
                    }
                    if($query == null){
                        $countEmpty++;
                    }
                    $countTotal++;

                }

                $chart->dataset($momento->getNombre(), 'bar', $query);
            }
        }

        if($countEmpty == $countTotal){
            return null;
        }
                        
        return $chart->api();

    }

}