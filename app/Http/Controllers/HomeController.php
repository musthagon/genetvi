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
use App\Charts\indicadoresChart;
use Illuminate\Support\Facades\Auth;

use App\Traits\CommonFunctionsGenetvi; 

class HomeController extends Controller
{
    use CommonFunctionsGenetvi;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){   
        
        $user = Auth::user();
        
        $informacion_pagina['titulo']       = "Principal";
        $informacion_pagina['descripcion']  = "Aquí se muestra un resumen de las acciones que puedes hacer en la aplicación";

        return view('user.principal', compact('cursosDocente','informacion_pagina'));
    }

    public function cursos(){   
        
        $user = Auth::user();

        $cursosDocente = CursoParticipante::cursosDocente($user->getCVUCV_USER_ID());
        
        $informacion_pagina['titulo']       = "Cursos";
        $informacion_pagina['descripcion']  = "Aquí se muestran las acciones que puedes realizar en tus cursos";

        return view('user.mis_cursos', compact('cursosDocente','informacion_pagina'));
    }

    public function visualizar_resultados_curso($curso_id){//Crea la vista del dashboard/graficos del curso
        $curso = Curso::find($curso_id);
        
        if(empty($curso)){
            return redirect()->back()->with(['message' => "El curso no existe", 'alert-type' => 'error']);
        }

        //Tiene permitido acceder?
        $user = Auth::user();
        $estaMatriculadoDocente = CursoParticipante::where('cvucv_user_id', $user->cvucv_id)
        ->where('cvucv_curso_id',$curso->id)
        ->where('cvucv_rol_id','!=',5)
        ->first();        
        if(empty($estaMatriculadoDocente) ){
            return redirect('/mis_cursos')->with(['message' => "Error, acceso no autorizado", 'alert-type' => 'error']);
        }

        //instrumentos con los cuales han evaluado este curso
        Evaluacion::instrumentos_de_evaluacion_del_curso($curso->id, $instrumentos_collection, $nombreInstrumentos);
        //instrumentos con los cuales han evaluado este curso2
        Evaluacion::instrumentos_de_evaluacion_del_curso($curso->id, $instrumentos_collection2, $nombreInstrumentos2, 1);
        
        //periodos lectivos con los cuales han evaluado este curso
        $periodos_collection        = Evaluacion::periodos_lectivos_de_evaluacion_del_curso($curso->id);

        //categorias con los cuales han evaluado este curso
        $categorias_collection      = Evaluacion::categorias_de_evaluacion_del_curso($curso->id);
        
        //indicadores con los cuales han evaluado este curso
        $indicadores_collection     = Evaluacion::indicadores_de_evaluacion_del_curso($curso->id);

        $indicadores_collection_charts = [];

        //Chart1. Cantidad personas que han evaluado el eva
        //Chart2. Ponderacion de la evaluacion del eva
        $cantidadEvaluacionesCursoCharts = [];
        $promedioPonderacionCurso = [];
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
                        'text' => 'Fuente: SISGEVA ©2019 Sistema de Educación a Distancia de la Universidad Central de Venezuela.'
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

                $api = route('curso.consultar_grafico_indicadores', ['curso' => $curso->id, 'periodo' => $periodo->id, 'instrumento' => $instrumento->id, 'categoria' => $categoria->id, 'indicador' => $indicador->id]);

                $opciones_instrumento = $indicador->indicadorOpciones($categoria->likertOpciones());
                $indicadores_collection_charts[$periodo_index][$instrumento_index][$categoria_index][$indicador_index]
                    ->labels(array_keys($opciones_instrumento))->load($api);
            }

        }
        }   
        }
        }

        $cantidadEvaluacionesCursoCharts= new indicadoresChart;
        $cantidadEvaluacionesCursoCharts->labels($nombreInstrumentos);
        $cantidadEvaluacionesCursoCharts->options([
            'title'=>[
                'text' => 'Cantidad de Evaluaciones de '.$curso->cvucv_fullname
            ],
            'subtitle'=>[
                'text' => 'Fuente: SISGEVA ©2019 Sistema de Educación a Distancia de la Universidad Central de Venezuela.'
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
            'yAxis'=> [
                'min'=> 0,
                'title'=> [
                    'text'=> 'Cantidad personas que evaluaron',
                    'align'=> 'high'
                ],
                'labels'=> [
                    'overflow'=> 'justify'
                ]
            ],
        ]);

        $api = route('curso.consultar_grafico_generales', ['tipo'=>1,'curso' => $curso->id]);
        $cantidadEvaluacionesCursoCharts->load($api);

        $promedioPonderacionCurso = new indicadoresChart;
        $promedioPonderacionCurso->labels($nombreInstrumentos);        
        $promedioPonderacionCurso->options([
            'title'=>[
                'text' => 'Promedio de la Ponderacion de '.$curso->cvucv_fullname
            ],
            'subtitle'=>[
                'text' => 'Fuente: SISGEVA ©2019 Sistema de Educación a Distancia de la Universidad Central de Venezuela.'
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
        
        $api = route('curso.consultar_grafico_generales', ['tipo'=>2,'curso' => $curso->id]);
        $promedioPonderacionCurso->load($api);

        
        return view('user.cursos_dashboards_test',
        compact(
            'curso',
            'periodos_collection',
            'instrumentos_collection',
            'instrumentos_collection2',
            'categorias_collection',
            'indicadores_collection',
            'indicadores_collection_charts',
            'cantidadEvaluacionesCursoCharts',
            'promedioPonderacionCurso'
        ));

    }

}
