<?php

namespace App\Http\Controllers;

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


use App\Traits\CommonFunctionsGenetvi; 

class ChartsController extends Controller
{
    use CommonFunctionsGenetvi;

    protected $dashboards_subtitle = 'Fuente: GENETVI ©2019 Sistema de Educación a Distancia de la Universidad Central de Venezuela.';

    /*
    * Para visualizar los dashboards de la evaluacion de los cursos
    * Y lo relacionado a ese curso
    *
    */

    public function visualizar_resultados_curso($categoria_id, $curso_id){//Crea la vista del dashboard/graficos del curso
        $curso = Curso::find($curso_id);
        
        if(empty($curso)){
            return redirect()->back()->with(['message' => "El curso no existe", 'alert-type' => 'error']);
        }

        //Tiene permitido acceder a la categoria?
        Gate::allows('checkAccess_ver',[$curso]);

        //instrumentos con los cuales han evaluado este curso publicos
        Evaluacion::instrumentos_de_evaluacion_del_curso($curso->id, $instrumentos_collection, $nombreInstrumentos);
        //instrumentos con los cuales han evaluado este curso2 anonimos
        Evaluacion::instrumentos_de_evaluacion_del_curso($curso->id, $instrumentos_collection2, $nombreInstrumentos2, 1);
        
        //periodos lectivos con los cuales han evaluado este curso
        $periodos_collection        = Evaluacion::periodos_lectivos_de_evaluacion_del_curso($curso->getID());

        //categorias con los cuales han evaluado este curso
        $categorias_collection      = Evaluacion::categorias_de_evaluacion_del_curso($curso->getID());
        
        //indicadores con los cuales han evaluado este curso
        $indicadores_collection     = Evaluacion::indicadores_de_evaluacion_del_curso($curso->getID());

        $indicadores_collection_charts = [];

        //Se inicializan los dashboards individuales por indicador
        foreach($periodos_collection as $periodo_index=>$periodo){
        foreach($instrumentos_collection as $instrumento_index=>$instrumento){
        foreach($instrumento->categorias as $categoria_index=>$categoria){
        foreach($categoria->indicadores as $indicador_index=>$indicador){

            if($indicador->esMedible()){
                $indicadores_collection_charts[$periodo_index][$instrumento_index][$categoria_index][$indicador_index] = new indicadoresChart;

                $indicadores_collection_charts[$periodo_index][$instrumento_index][$categoria_index][$indicador_index] ->options([
                    "color"=>["#90ed7d", "#7cb5ec", "#f7a35c", "#8085e9", "#f15c80", "#e4d354", "#2b908f", "#f45b5b", "#91e8e1"],
                    'title'=>[
                        'text' => 'Respuestas del indicador: '.$indicador->nombre.'<br>
                                    Del Instrumento: '.$instrumento->nombre.'<br>
                                    En el periodo lectivo: '.$periodo->nombre
                    ],
                    'subtitle'=>[
                        'text' => $this->dashboards_subtitle
                    ],
                    'tooltip'=> [
                        'pointFormat'=> '{series.name}: <b>{point.percentage:.1f}%</b>'
                    ],
                    'plotOptions'=> [
                        'pie'=> [
                            'allowPointSelect'=> true,
                            'cursor'=> 'pointer',
                            'dataLabels'=> [
                                'enabled'=> true,
                                'format'=> '<b>{point.name}</b>: {point.percentage:.1f} %'
                            ],
                        ],                            
                    ],
                ]);

                $opciones_instrumento = $categoria->getLikertType();
                $indicadores_collection_charts[$periodo_index][$instrumento_index][$categoria_index][$indicador_index]
                    ->labels($opciones_instrumento)->load(route('curso.consultar_grafico_indicadores', ['curso' => $curso->id, 'periodo' => $periodo->id, 'instrumento' => $instrumento->id, 'categoria' => $categoria->id, 'indicador' => $indicador->id]));
            }

        }
        }   
        }
        }

        $cantidadEvaluacionesCursoCharts = [];
        $cantidadEvaluacionesCursoCharts= new indicadoresChart;
        $cantidadEvaluacionesCursoCharts->options([
            'title'=>[
                'text' => 'Cantidad de Evaluaciones de '.$curso->getNombre()
            ],
            'subtitle'=>[
                'text' => $this->dashboards_subtitle
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
        $cantidadEvaluacionesCursoCharts->load(route('curso.consultar_grafico_generales', ['tipo'=>1,'curso' => $curso->getID()]));

        //Se inicializa el Chart2. Ponderacion de la evaluacion del eva
        $promedioPonderacionCurso = [];
        $promedioPonderacionCurso = new indicadoresChart;
        $promedioPonderacionCurso->options([
            'title'=>[
                'text' => 'Promedio de la Ponderacion de '.$curso->getNombre()
            ],
            'subtitle'=>[
                'text' => $this->dashboards_subtitle
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
        $promedioPonderacionCurso->load(route('curso.consultar_grafico_generales', ['tipo'=>2,'curso' => $curso->getID()]));

        return view('vendor.voyager.gestion.cursos_dashboards',
        compact(
            'curso',
            'periodos_collection',
            'instrumentos_collection',
            'instrumentos_collection2',
            'categorias_collection',
            'indicadores_collection',
            'indicadores_collection_charts',
            'cantidadEvaluacionesCursoCharts',
            'promedioPonderacionCurso',
            'dashboards_subtitle'
        ));

    }

    public function visualizar_resultados_curso_respuesta_publica($categoria_id, $curso_id, Request $request){
        

        $curso = Curso::find($curso_id);

        if(!isset($request->periodo_lectivo) || !isset($request->instrumento)  || !isset($request->user)){
            return redirect()->back()->with(['message' => "Faltan campos obligatorios", 'alert-type' => 'error']);
        }
        
        if(empty($curso)){
            return redirect()->back()->with(['message' => "El curso no existe", 'alert-type' => 'error']);
        }
        if(!$curso->es_categoria_del_curso($categoria_id)){
            return redirect()->back()->with(['message' => "La dependencia del curso es incorrecta", 'alert-type' => 'error']);
        }

        
        $periodo_lectivo_id = $request->periodo_lectivo;
        $instrumento_id     = $request->instrumento;
        $usuario_id         = $request->user;

        $periodo_lectivo = PeriodoLectivo::find($periodo_lectivo_id);
        $instrumento = Instrumento::find($instrumento_id);

        if(empty($periodo_lectivo)){
            return redirect()->back()->with(['message' => "El periodo lectivo no existe", 'alert-type' => 'error']);
        }

        if(empty($instrumento)){
            return redirect()->back()->with(['message' => "El instrumento no existe", 'alert-type' => 'error']);
        }

        if($instrumento->getAnonimo()){
            return redirect()->back()->with(['message' => "Las respuestas de este instrumento son anónimas", 'alert-type' => 'error']);
        }

        $evaluacion = Evaluacion::buscar_evaluacion($curso->id, $periodo_lectivo_id, $instrumento_id, $usuario_id);
        if(empty($evaluacion)){
            return redirect()->back()->with(['message' => "Error, este usuario no ha evaluado este curso", 'alert-type' => 'error']);
        }
        //instrumentos con los cuales han evaluado este curso2
        Evaluacion::instrumentos_de_evaluacion_del_curso($curso->id, $instrumentos_collection2, $nombreInstrumentos2, 1);
        
        //periodos lectivos con los cuales han evaluado este curso
        $periodos_collection        = Evaluacion::periodos_lectivos_de_evaluacion_del_curso($curso->id);

        $usuario = $this->cvucv_get_profile($usuario_id );

        return view('vendor.voyager.gestion.cursos_evaluaciones_publicas',
        compact(
            'curso',
            'periodos_collection',
            'instrumentos_collection2',
            'evaluacion',
            'usuario',
            'periodo_lectivo',
            'instrumento'
        ));
    }
    public function consultar_grafico(Request $request, $id){//Crea la data de los dashboards consultados dinamicamente fetch() JS
        $chart = new indicadoresChart;

        if(isset($request->number)){
            $array = [3,4,(int)$request->number];
            $array2 = [(int)$request->number,4,3];
            $chart->dataset('Sample Test', 'bar', $array );
            $chart->dataset('Sample Test', 'line', $array2 );
        }else{
            $chart->dataset('Sample Test', 'bar', [3,4,1]);
            $chart->dataset('Sample Test', 'line', [1,4,3]);
        }
        

        return $chart->api();
    }
    public function consultar_grafico_indicadores($curso_id, $periodo_id, $instrumento_id, $categoria_id, $indicador_id){//Crea la data de los dashboards consultados dinamicamente fetch() JS
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
        //$opciones_instrumento = $indicador->indicadorOpciones($categoria->likertOpciones());
        
        $opciones_instrumento = $categoria->getLikertType();
                
        $k=0;
        $opciones_indicador = [];
        foreach($opciones_instrumento as $key=>$opcion){
            $valor = Evaluacion::cantidad_respuestas_para_indicador($curso, $instrumento, $periodo, $indicador, $key);
            $opciones_indicador[$k] = $valor;
            $k++;
        }

        $chart->dataset($indicador->nombre.' Instrumento: '.$instrumento->id.' Periodo Lectivo: '.$periodo->id, 'bar',$opciones_indicador);
        
        $chart->dataset($indicador->nombre.' Instrumento: '.$instrumento->id.' Periodo Lectivo: '.$periodo->id, 'line',$opciones_indicador);


        return $chart->api();
    }
    public function consultar_tabla_indicador($curso_id, $periodo_id, $instrumento_id, $categoria_id, $indicador_id){//Crea datatable de indicadores noMedibles Text, textarea
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
    /*public function consultar_grafico_generales($curso_id, $tipo){//Crea la data de los dashboards consultados dinamicamente fetch() JS
        if(!isset($curso_id) || !isset($tipo)){
            return json_encode (json_decode ("{}"));
        }

        //$tipo = (int)$tipo ;
        if($tipo != "1" && $tipo != "2"){
            
            $chart = new indicadoresChart;
            $chart->dataset('Sample Test', 'bar', [3,4,1]);
            $chart->dataset('Sample Test', 'line', [1,4,3]);
            return $chart->api();
        }


        $curso = Curso::find($curso_id);
        
        if(empty($curso)){
            return json_encode (json_decode ("{}"));
        }

        //instrumentos con los cuales han evaluado este curso
        Evaluacion::instrumentos_de_evaluacion_del_curso($curso->id, $instrumentos_collection, $nombreInstrumentos);
        
        //periodos lectivos con los cuales han evaluado este curso
        $periodos_collection = Evaluacion::periodos_lectivos_de_evaluacion_del_curso($curso->id);

        $chart = new indicadoresChart;
        
        
        //Charts por indicadores de categora, en instrumento en un periodo lectivo
        $query = [];
        foreach($periodos_collection as $periodo_index=>$periodo){
            foreach($instrumentos_collection as $instrumento_index=>$instrumento){
                if($tipo == "1"){
                    $query [$periodo_index][$instrumento_index] = Evaluacion::where('periodo_lectivo_id',$periodo->getID())
                        ->where('instrumento_id',$instrumento->id)
                        ->where('curso_id',$curso->id)->count();

                }elseif($tipo == "2"){
                    $query [$periodo_index][$instrumento_index] = Evaluacion::where('periodo_lectivo_id',$periodo->getID())
                        ->where('instrumento_id',$instrumento->id)
                        ->where('curso_id',$curso->id)
                        ->avg('percentil_eva');
                }
            }

            $chart->dataset($periodo->nombre, 'bar', $query[$periodo_index]);
            
        }

        return $chart->api();

    }*/

    public function consultar_grafico_generales($curso_id, $tipo){//Crea la data de los dashboards consultados dinamicamente fetch() JS
        if(!isset($curso_id) || !isset($tipo)){
            return json_encode (json_decode ("{}"));
        }

        $curso = Curso::find($curso_id);
        
        if(empty($curso)){
            return json_encode (json_decode ("{}"));
        }

        //instrumentos con los cuales han evaluado este curso
        Evaluacion::instrumentos_de_evaluacion_del_curso($curso->id, $instrumentos_collection, $nombreInstrumentos);
        
        //periodos lectivos con los cuales han evaluado este curso
        $periodos_collection            = Evaluacion::periodos_lectivos_de_evaluacion_del_curso($curso->id);

        //Momentos de evaluación
        $momentos_evaluacion_collection = Evaluacion::momentos_de_evaluacion_del_curso($curso->id);

        $chart = new indicadoresChart;
        
        
        //Charts por indicadores de categora, en instrumento en un periodo lectivo
        $query = [];
        foreach($periodos_collection as $periodo_index=>$periodo){
            foreach($momentos_evaluacion_collection as $momentoIndex => $momento){

                foreach($instrumentos_collection as $instrumento_index=>$instrumento){
                    if($tipo == "1"){
                        $query [$periodo_index][$instrumento_index] = Evaluacion::cantidad_evaluaciones1($periodo,$momento,$instrumento,$curso);
                    }elseif($tipo == "2"){
                        $query [$periodo_index][$instrumento_index] = Evaluacion::promedio_evaluaciones1($periodo,$momento,$instrumento,$curso);
                    }
                }
    
                $chart->dataset($momento->getNombre(), 'bar', $query[$periodo_index]);

            }
                        
        }

        return $chart->api();

    }
}