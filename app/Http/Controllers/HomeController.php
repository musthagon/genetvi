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

        $cursosDocente = CursoParticipante::cursosDocente($user->getCVUCV_USER_ID());
                
        return view('user.panel', compact('cursosDocente'));
    }

    /*
    * Para visualizar los dashboards de la evaluacion de los cursos
    * Y lo relacionado a ese curso
    *
    */
    /*public function visualizar_curso($id){        
        
        $curso = Curso::find($id);
        
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
            return redirect('/home')->with(['message' => "Error, acceso no autorizado", 'alert-type' => 'error']);
        }

        //periodos lectivos con los cuales han evaluado este curso
        $periodos_curso = Evaluacion::where('curso_id', $curso->id)->distinct('periodo_lectivo_id')->get(['periodo_lectivo_id']);
        $periodos_collection = [];
        foreach($periodos_curso as $periodo_index=>$periodo){
            $actual = PeriodoLectivo::find($periodo->periodo_lectivo_id);
            array_push($periodos_collection, $actual);
        }
        //periodos lectivos con los cuales han evaluado este curso
        $instrumentos_curso = Evaluacion::where('curso_id', $curso->id)->distinct('instrumento_id')->get(['instrumento_id']);
        $instrumentos_collection = [];
        $nombreInstrumentos = [];
        foreach($instrumentos_curso as $instrumento_index=>$instrumento){
            $actual = Instrumento::find($instrumento->instrumento_id);
            $nombreInstrumentos[$instrumento_index] = $actual->nombre;
            array_push($instrumentos_collection, $actual);
        }

        //Opciones del instrumento
        $opciones_instrumento = ['Siempre', 'A veces', 'Nunca']; 

        //Charts por indicadores de categora, en instrumento en un periodo lectivo
        $cantidadEvaluacionesCurso = [];
        $ponderacionCurso = [];

        foreach($periodos_collection as $periodo_index=>$periodo){

            if(!empty($periodo)){
            foreach($instrumentos_collection as $instrumento_index=>$instrumento){

                //Recorremos el instrumento para realizar los charts por indicador
                if(!empty($instrumento)){  
                
                    $lista_categorias = [];
                    foreach($instrumento->categorias as $categoria_index=>$categoria){

                        $lista_indicadores = [];
                        foreach($categoria->indicadores as $indicador_index=>$indicador){
                            $k=0;
                            $opciones_indicador = [];
                            foreach($opciones_instrumento as $key=>$opcion){
                                
                                $valor = DB::table('evaluaciones')
                                ->join('respuestas', 'evaluaciones.id', '=', 'respuestas.evaluacion_id')
                                ->select('evaluaciones.*', 'respuestas.*')
                                ->where('evaluaciones.curso_id',$curso->id)
                                ->where('evaluaciones.instrumento_id',$instrumento->id)
                                ->where('evaluaciones.periodo_lectivo_id',$periodo->id)
                                ->where('respuestas.indicador_id',$indicador->id)
                                ->where('respuestas.value_string',$opcion)
                                ->count();
                                $opciones_indicador[$k] = $valor;
                                $k++;
                            }
                            
                            $IndicadoresCharts[$periodo_index][$instrumento_index][$categoria_index][$indicador_index] = new indicadoresChart;
                            $IndicadoresCharts[$periodo_index][$instrumento_index][$categoria_index][$indicador_index]->labels($opciones_instrumento);
                            $IndicadoresCharts[$periodo_index][$instrumento_index][$categoria_index][$indicador_index]->
                            dataset($indicador->nombre.' Instrumento: '.$instrumento->id.' Periodo Lectivo: '.$periodo->id.' '.$periodo_index.$instrumento_index.$categoria_index.$indicador_index, 
                            'pie', 
                            $opciones_indicador)->options([
                                "color"=>["#90ed7d", "#7cb5ec", "#f7a35c", "#8085e9", "#f15c80", "#e4d354", "#2b908f", "#f45b5b", "#91e8e1"],
                            ]);
                            $IndicadoresCharts[$periodo_index][$instrumento_index][$categoria_index][$indicador_index]->options([
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
                        }
                    }

                    //Chart1. Cantidad personas que han evaluado el eva
                    $cantidadEvaluacionesCurso [$periodo_index][$instrumento_index] = Evaluacion::where('periodo_lectivo_id',$periodo->id)
                    ->where('instrumento_id',$instrumento->id)
                    ->where('curso_id',$curso->id)->count();
                    

                    //Chart2. Ponderacion de la evaluacion del eva
                    $ponderacionCurso [$periodo_index][$instrumento_index] = Evaluacion::where('periodo_lectivo_id',$periodo->id)
                    ->where('instrumento_id',$instrumento->id)
                    ->where('curso_id',$curso->id)
                    ->avg('percentil_eva');
                    
                }
            }
            }
        }

        //Chart1. Cantidad personas que han evaluado el eva
        //Chart2. Ponderacion de la evaluacion del eva
        $cantidadEvaluacionesCursoCharts = [];
        $promedioPonderacionCurso = [];
        if(!empty($cantidadEvaluacionesCurso) && !empty($ponderacionCurso)){

            $cantidadEvaluacionesCursoCharts = new indicadoresChart;
            $cantidadEvaluacionesCursoCharts->labels($nombreInstrumentos);

            $promedioPonderacionCurso = new indicadoresChart;
            $promedioPonderacionCurso->labels($nombreInstrumentos);

            foreach($periodos_collection as $periodo_index=>$periodo){
                $cantidadEvaluacionesCursoCharts->dataset($periodo->nombre, 'bar', $cantidadEvaluacionesCurso[$periodo_index]);
                $promedioPonderacionCurso->dataset($periodo->nombre, 'bar', $ponderacionCurso[$periodo_index]);
            }
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
        }

        //categorias con los cuales han evaluado este curso
        $categorias_collection = [];
        $categoriasDisponibles = DB::table('evaluaciones')
                ->join('respuestas', 'evaluaciones.id', '=', 'respuestas.evaluacion_id')
                ->select('respuestas.categoria_id')
                ->where('evaluaciones.curso_id',$curso->id)
                ->distinct('categoria_id')
                ->get();

        foreach($categoriasDisponibles as $categoria){
            $actual = Categoria::find($categoria->categoria_id);
            array_push($categorias_collection, $actual);
        }

        //indicadores con los cuales han evaluado este curso
        $indicadores_collection = [];
        $indicadoresDisponibles = DB::table('evaluaciones')
                ->join('respuestas', 'evaluaciones.id', '=', 'respuestas.evaluacion_id')
                ->select('respuestas.indicador_id')
                ->where('evaluaciones.curso_id',$curso->id)
                ->distinct('indicador_id')
                ->get();
        foreach($indicadoresDisponibles as $indicador){
            $actual = Indicador::find($indicador->indicador_id);
            array_push($indicadores_collection, $actual);
        }

        //Buscamos los participantes
        $participantes = $this->cvucv_get_participantes_curso($curso->id);

        return view('user.curso',
        compact(
            'curso',
            'participantes',
            'periodos_collection',
            'instrumentos_collection',
            'categorias_collection',
            'indicadores_collection',
            'IndicadoresCharts',
            'cantidadEvaluacionesCursoCharts',
            'promedioPonderacionCurso'
        ));
    }*/


    /*public function visualizar_resultados_curso($curso_id){//Crea la vista del dashboard/graficos del curso
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
            return redirect('/home')->with(['message' => "Error, acceso no autorizado", 'alert-type' => 'error']);
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

    }*/

    /*public function sync_user_courses(&$cursosEstudiante, &$cursosDocente ){
        $user = Auth::user();

        //Consultamos los cursos del usuario
        $cursos_cvucv = $this->cvucv_get_users_courses($user->cvucv_id);
        
        if(!empty($cursos_cvucv)){

            $cursosEstudiante   = collect();
            $cursosDocente      = collect();
            foreach($cursos_cvucv as $data){

                $curso = Curso::find($data['id']);

                //1. Verificamos que existan los cursos
                //Si no existe, hay que crearlo
                if(empty($curso)){

                    $curso = new Curso;

                    $curso->id                  = $data['id'];
                    $curso->cvucv_shortname     = $data['shortname'];
                    $curso->cvucv_category_id   = $data['category'];
                    $curso->cvucv_fullname      = $data['fullname'];
                    $curso->cvucv_displayname   = $data['displayname'];
                    $curso->cvucv_summary       = $data['summary'];
                    $curso->cvucv_visible       = $data['visible'];
                    $curso->cvucv_link          = env("CVUCV_GET_SITE_URL","https://campusvirtual.ucv.ve")."/course/view.php?id=".$data['id'];

                    $curso->save();
                }

                //2. Verificamos que este matriculado en ese curso -> Solicitamos los participantes del curso
                $participantes_curso = $this->cvucv_get_participantes_curso($data['id']);

                foreach($participantes_curso as $participante){
                
                    //Buscamos el usuario actual
                    if($user->cvucv_id == $participante['id']){
                        $matriculacion = CursoParticipante::where('cvucv_user_id', $participante['id'])
                            ->where('cvucv_curso_id', $data['id'])
                            ->first();
                        //Si no esta, hay que matricularlo
                        if(empty($matriculacion)){
                            $matriculacion                 = new CursoParticipante;

                            $matriculacion->user_id        = $user->id;
                            $matriculacion->cvucv_user_id  = $participante['id'];
                            $matriculacion->cvucv_curso_id = $data['id'];
                            $matriculacion->user_sync      = true;
                        }
                        if(isset($participante['roles']) && !empty($participante['roles'])){
                            $matriculacion->cvucv_rol_id = $participante['roles'][0]['roleid'];

                            //Es estudiante en el curso?
                            if($participante['roles'][0]['roleid'] == 5){
                                $cursosEstudiante->push($curso);
                            }else{
                                $cursosDocente->push($curso);
                            }   
                        }      
                        //$matriculacion->curso_sync   = true;
                        $matriculacion->save();

                        break;
                    }
                }                     
            }
            
        }
    }*/

}
