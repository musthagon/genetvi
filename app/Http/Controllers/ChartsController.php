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
use Yajra\Datatables\Datatables;

use App\Traits\CommonFunctionsGenetvi; 
use App\Traits\CommonFunctionsCharts;

class ChartsController extends Controller
{
    use CommonFunctionsGenetvi;
    use CommonFunctionsCharts;

    
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
                        'text' => $this->dashboards_subtitle
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

        $dashboards_subtitle = $this->dashboards_subtitle;
        
        return view('vendor.voyager.gestion.cursos_dashboards',
        compact(
            'curso',
            'periodos_collection',
            'instrumentos_collection',
            'instrumentos_collection2',
            'categorias_collection',
            'indicadores_collection',
            'indicadores_collection_charts',
            'cantidadEvaluacionesCursoCharts1',
            'cantidadEvaluacionesRechazadasCursoCharts1',
            'promedioPonderacionCurso1',
            'cantidadEvaluacionesCursoCharts2',
            'cantidadEvaluacionesRechazadasCursoCharts2',
            'promedioPonderacionCurso2',
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
    
}