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

    public function construirGraficosGeneralesCurso (
        $curso, 
        &$periodos_collection, 
        &$instrumentos_collection,
        &$instrumentos_collection2,
        &$categorias_collection, 
        &$indicadores_collection,
        &$indicadores_collection_charts,
        &$cantidadEvaluacionesCursoCharts1,
        &$cantidadEvaluacionesRechazadasCursoCharts1,
        &$promedioPonderacionCurso1,
        &$cantidadEvaluacionesCursoCharts2,
        &$cantidadEvaluacionesRechazadasCursoCharts2,
        &$promedioPonderacionCurso2,
        &$dashboards_subtitle) {
        $dashboards_subtitle = 'Fuente: GENETVI ©2020 Sistema de Educación a Distancia de la Universidad Central de Venezuela.';
        //instrumentos con los cuales han evaluado este curso publicos
        Evaluacion::instrumentos_de_evaluacion_del_curso($curso->getID(), $instrumentos_collection, $nombreInstrumentos);
        //instrumentos con los cuales han evaluado este curso2 anonimos
        Evaluacion::instrumentos_de_evaluacion_del_curso($curso->getID(), $instrumentos_collection2, $nombreInstrumentos2, 1);
        
        //periodos lectivos con los cuales han evaluado este curso
        $periodos_collection        = Evaluacion::periodos_lectivos_de_evaluacion_del_curso($curso->getID(),$nombresPeriodos);

        //categorias con los cuales han evaluado este curso
        $categorias_collection      = Evaluacion::categorias_de_evaluacion_del_curso($curso->getID());
        
        //indicadores con los cuales han evaluado este curso
        $indicadores_collection     = Evaluacion::indicadores_de_evaluacion_del_curso($curso->getID());

        $cantidadEvaluacionesCursoCharts = [];
        $promedioPonderacionCurso = [];
        $indicadores_collection_charts = [];
        
        //Se inicializan los dashboards individuales por indicador
        foreach($periodos_collection as $periodo_index=>$periodo){

            //Se inicializa el Chart1. Cantidad de la evaluacion del eva
            $request = new Request;
            $request->tipo              = 3;
            $request->curso             = $curso->getID();
            $request->periodo_lectivo   = $periodo->getID();
            if($this->consultar_grafico_generales($request) != null){
                $cantidadEvaluacionesCursoCharts2[$periodo_index]= new indicadoresChart;
                $cantidadEvaluacionesCursoCharts2[$periodo_index]->options([
                    'title'=>[
                        'text' => 'Cantidad de Evaluaciones de '.$curso->getNombre().', en el periodo lectivo: '.$periodo->getNombre()
                    ],
                    'subtitle'=>[
                        'text' => $dashboards_subtitle
                    ],
                    'tooltip'=> [
                        'valueSuffix'=> ' personas'
                    ],
                    'plotOptions'=> [
                        'bar'=> [
                            'dataLabels'=> [
                                'enabled'=> true,
                            ],
                        ],
                        'series'=> [
                            'stacking'=> 'normal'
                        ],                       
                    ],
                    'xAxis' => [
                        'categories' => $nombreInstrumentos
                    ],
                    'yAxis'=> [
                        'min'=> 0,
                        'title'=> [
                            'text'=> 'Cantidad personas que han evaluado',
                            'align'=> 'high'
                        ],
                        'labels'=> [
                            'overflow'=> 'justify'
                        ]
                    ],
                ]);
                $cantidadEvaluacionesCursoCharts2[$periodo_index]->load(route('curso.consultar_grafico_generales', ['tipo'=>3,'curso' => $curso->getID(),'periodo_lectivo' => $periodo->getID()]));
            }

            //Se inicializa el Chart1. Cantidad de la evaluacion del eva Rechazadas
            $request = new Request;
            $request->tipo              = 3;
            $request->curso             = $curso->getID();
            $request->periodo_lectivo   = $periodo->getID();
            if($this->consultar_grafico_generales($request) != null){
                $cantidadEvaluacionesRechazadasCursoCharts2[$periodo_index]= new indicadoresChart;
                $cantidadEvaluacionesRechazadasCursoCharts2[$periodo_index]->options([
                    'title'=>[
                        'text' => 'Cantidad de Evaluaciones Rechazadas de '.$curso->getNombre().', en el periodo lectivo: '.$periodo->getNombre()
                    ],
                    'subtitle'=>[
                        'text' => $dashboards_subtitle
                    ],
                    'tooltip'=> [
                        'valueSuffix'=> ' personas'
                    ],
                    'plotOptions'=> [
                        'bar'=> [
                            'dataLabels'=> [
                                'enabled'=> true,
                            ],
                        ],
                        'series'=> [
                            'stacking'=> 'normal'
                        ],                       
                    ],
                    'xAxis' => [
                        'categories' => $nombreInstrumentos
                    ],
                    'yAxis'=> [
                        'min'=> 0,
                        'title'=> [
                            'text'=> 'Cantidad personas que han rechazado evaluar',
                            'align'=> 'high'
                        ],
                        'labels'=> [
                            'overflow'=> 'justify'
                        ]
                    ],
                ]);
                $cantidadEvaluacionesRechazadasCursoCharts2[$periodo_index]->load(route('curso.consultar_grafico_generales', ['tipo'=>6,'curso' => $curso->getID(),'periodo_lectivo' => $periodo->getID()]));
            }

            //Se inicializa el Chart2. Ponderacion de la evaluacion del eva
            $request = new Request;
            $request->tipo              = 4;
            $request->curso             = $curso->getID();
            $request->periodo_lectivo   = $periodo->getID();
            if($this->consultar_grafico_generales($request) != null){
                $promedioPonderacionCurso2[$periodo_index] = new indicadoresChart;
                $promedioPonderacionCurso2[$periodo_index]->options([
                    'title'=>[
                        'text' => 'Promedio de la Ponderacion de '.$curso->getNombre().', en el periodo lectivo: '.$periodo->getNombre()
                    ],
                    'subtitle'=>[
                        'text' => $dashboards_subtitle
                    ],
                    'tooltip'=> [
                        'valueSuffix'=> ' %'
                    ],
                    'plotOptions'=> [
                        'bar'=> [
                            'dataLabels'=> [
                                'enabled'=> true,
                            ],
                        ],                                          
                    ],
                    'xAxis' => [
                        'categories' => $nombreInstrumentos
                    ],
                    'yAxis'=> [
                        'min'=> 0,
                        'title'=> [
                            'text'=> 'Promedio ponderacion del curso',
                            'align'=> 'high'
                        ],
                        'labels'=> [
                            'overflow'=> 'justify'
                        ]
                    ],
                ]);
                $promedioPonderacionCurso2[$periodo_index]->load(route('curso.consultar_grafico_generales', ['tipo'=>4,'curso' => $curso->getID(),'periodo_lectivo' => $periodo->getID()]));
            }

        foreach($instrumentos_collection as $instrumento_index=>$instrumento){
        foreach($instrumento->categorias as $categoria_index=>$categoria){
            
            //Obtenenos las opciones de la escala de likert
            $opciones_instrumento = $categoria->getLikertType();

        foreach($categoria->indicadores as $indicador_index=>$indicador){

            $request = new Request;
            $request->curso_id          = $curso->getID();
            $request->periodo_id        = $periodo->getID();
            $request->instrumento_id    = $instrumento->getID();
            $request->categoria_id      = $categoria->getID();
            $request->indicador_id      = $indicador->getID();
            if($indicador->esMedible() && $this->consultar_grafico_indicadores($request) != null){
                $indicadores_collection_charts[$periodo_index][$instrumento_index][$categoria_index][$indicador_index] = new indicadoresChart;

                $indicadores_collection_charts[$periodo_index][$instrumento_index][$categoria_index][$indicador_index]
                ->options([
                    "color"=>["#90ed7d", "#7cb5ec", "#f7a35c", "#8085e9", "#f15c80", "#e4d354", "#2b908f", "#f45b5b", "#91e8e1"],
                    'title'=>[
                        'text' => 'Respuestas del indicador: '.$indicador->getNombre().'<br>
                                    Del Instrumento: '.$instrumento->getNombre().'<br>
                                    En el periodo lectivo: '.$periodo->getNombre()
                    ],
                    'subtitle'=>[
                        'text' => $dashboards_subtitle
                    ],
                    'tooltip'=> [
                        'valueSuffix'=> ' veces'
                    ],
                    'plotOptions'=> [
                        'bar'=> [
                            'dataLabels'=> [
                                'enabled'=> true,
                            ],
                        ],
                        'series'=> [
                            'stacking'=> 'normal'
                        ],                           
                    ],
                    'xAxis' => [
                        'categories' => $opciones_instrumento
                    ],
                    'yAxis'=> [
                        'min'=> 0,
                        'title'=> [
                            'text'=> 'Cantidad respuestas',
                            'align'=> 'high'
                        ],
                        'labels'=> [
                            'overflow'=> 'justify'
                        ]
                    ],
                    
                ]);
                $indicadores_collection_charts[$periodo_index][$instrumento_index][$categoria_index][$indicador_index]
                    ->load(route('curso.consultar_grafico_indicadores', 
                        ['curso_id' => $curso->getID(), 'periodo_id' => $periodo->getID(), 'instrumento_id' => $instrumento->getID(), 'categoria_id' => $categoria->getID(), 'indicador_id' => $indicador->getID()])); 
            }

        }
        }   
        }
        }


        //Se inicializa el Chart1. Total Cantidad de la evaluacion del eva por periodo lectivo
        $request = new Request;
        $request->tipo       = 1;
        $request->curso      = $curso->getID();
        $request->periodos   = $periodos_collection;
        if($this->consultar_grafico_generales($request) != null){
            $cantidadEvaluacionesCursoCharts1= new indicadoresChart;
            $cantidadEvaluacionesCursoCharts1->options([
                'title'=>[
                    'text' => 'Cantidad de Evaluaciones de '.$curso->getNombre()
                ],
                'subtitle'=>[
                    'text' => $dashboards_subtitle
                ],
                'tooltip'=> [
                    'valueSuffix'=> ' personas'
                ],
                'plotOptions'=> [
                    'bar'=> [
                        'dataLabels'=> [
                            'enabled'=> true,
                        ],
                    ],                     
                ],
                'xAxis' => [
                    'categories' => $nombresPeriodos
                ],
                'yAxis'=> [
                    'min'=> 0,
                    'title'=> [
                        'text'=> 'Cantidad personas que han evaluado',
                        'align'=> 'high'
                    ],
                    'labels'=> [
                        'overflow'=> 'justify'
                    ]
                ],
            ]);
            $cantidadEvaluacionesCursoCharts1->load(route('curso.consultar_grafico_generales', ['tipo'=>1,'curso' => $curso->getID(),'periodos' => $periodos_collection]));
        }

        //Se inicializa el Chart1. Total Cantidad de la evaluacion del eva por periodo lectivo Rechazadas
        $request = new Request;
        $request->tipo       = 1;
        $request->curso      = $curso->getID();
        $request->periodos   = $periodos_collection;
        if($this->consultar_grafico_generales($request) != null){
            $cantidadEvaluacionesRechazadasCursoCharts1= new indicadoresChart;
            $cantidadEvaluacionesRechazadasCursoCharts1->options([
                'title'=>[
                    'text' => 'Cantidad de Evaluaciones Rechazadas del '.$curso->getNombre()
                ],
                'subtitle'=>[
                    'text' => $dashboards_subtitle
                ],
                'tooltip'=> [
                    'valueSuffix'=> ' personas'
                ],
                'plotOptions'=> [
                    'bar'=> [
                        'dataLabels'=> [
                            'enabled'=> true,
                        ],
                    ],                     
                ],
                'xAxis' => [
                    'categories' => $nombresPeriodos
                ],
                'yAxis'=> [
                    'min'=> 0,
                    'title'=> [
                        'text'=> 'Cantidad personas que han rechazado evaluar',
                        'align'=> 'high'
                    ],
                    'labels'=> [
                        'overflow'=> 'justify'
                    ]
                ],
            ]);
            $cantidadEvaluacionesRechazadasCursoCharts1->load(route('curso.consultar_grafico_generales', ['tipo'=>5,'curso' => $curso->getID(),'periodos' => $periodos_collection]));
        }

        //Se inicializa el Chart2. Total Ponderacion de la evaluacion del eva por periodo lectivo
        $request = new Request;
        $request->tipo       = 2;
        $request->curso      = $curso->getID();
        $request->periodos   = $periodos_collection;
        if($this->consultar_grafico_generales($request) != null){
            $promedioPonderacionCurso1 = new indicadoresChart;
            $promedioPonderacionCurso1->options([
                'title'=>[
                    'text' => 'Promedio de la Ponderacion de '.$curso->getNombre()
                ],
                'subtitle'=>[
                    'text' => $dashboards_subtitle
                ],
                'tooltip'=> [
                    'valueSuffix'=> ' %'
                ],
                'plotOptions'=> [
                    'bar'=> [
                        'dataLabels'=> [
                            'enabled'=> true,
                        ],
                    ],                                          
                ],
                'xAxis' => [
                    'categories' => $nombresPeriodos
                ],
                'yAxis'=> [
                    'min'=> 0,
                    'title'=> [
                        'text'=> 'Promedio ponderacion del curso',
                        'align'=> 'high'
                    ],
                    'labels'=> [
                        'overflow'=> 'justify'
                    ]
                ],
            ]);
            $promedioPonderacionCurso1->load(route('curso.consultar_grafico_generales', ['tipo'=>2,'curso' => $curso->getID(),'periodos' => $periodos_collection]));
        }

        
    }

    public function construir_resultados_curso_respuesta_publica(
        $categoria_id, 
        $curso_id, 
        Request $request, 
        $curso,
        &$periodos_collection,
        &$instrumentos_collection2,
        &$evaluacion,
        &$usuario_id,
        &$periodo_lectivo,
        &$instrumento,
        &$momentos_evaluacion_collection){

        $periodo_lectivo_id = $request->periodo_lectivo;
        $instrumento_id     = $request->instrumento;
        $usuario_id         = $request->user;
        $momento_evaluacion_id = $request->momento_evaluacion;
        if(!isset($periodo_lectivo_id) || !isset($instrumento_id)  || !isset($usuario_id) || !isset($momento_evaluacion_id) ){
            return ['status'=> 'error', 'message' => "Faltan campos obligatorios", 'alert-type' => 'error'];
        }
        
        if(!$curso->es_categoria_del_curso($categoria_id)){
            return ['status'=> 'error', 'message' => "La dependencia del curso es incorrecta", 'alert-type' => 'error'];
        }
        
        
        $periodo_lectivo = PeriodoLectivo::find($periodo_lectivo_id);
        $instrumento = Instrumento::find($instrumento_id);

        if(empty($periodo_lectivo)){
            return ['status'=> 'error', 'message' => "El periodo lectivo no existe", 'alert-type' => 'error'];
        }

        if(empty($instrumento)){
            return ['status'=> 'error', 'message' => "El instrumento no existe", 'alert-type' => 'error'];
        }

        if($instrumento->getAnonimo()){
            return ['status'=> 'error', 'message' => "Las respuestas de este instrumento son anónimas", 'alert-type' => 'error'];
        }

        $evaluacion = Evaluacion::buscar_evaluacion($curso->getID(), $periodo_lectivo_id, $instrumento_id, $usuario_id,$momento_evaluacion_id);
        if(empty($evaluacion)){
            return ['status'=> 'error', 'message' => "Error, este usuario no ha evaluado este curso", 'alert-type' => 'error'];
        }
        //instrumentos con los cuales han evaluado este curso2
        Evaluacion::instrumentos_de_evaluacion_del_curso($curso->id, $instrumentos_collection2, $nombreInstrumentos2, 1);
        
        //periodos lectivos con los cuales han evaluado este curso
        $periodos_collection        = Evaluacion::periodos_lectivos_de_evaluacion_del_curso($curso->getID());
        $momentos_evaluacion_collection = Evaluacion::momentos_de_evaluacion_del_curso($curso->getID());

        return ['status'=> 'success', 'message' => "Petición completada", 'alert-type' => 'success'];
    }

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