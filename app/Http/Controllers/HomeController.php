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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class HomeController extends Controller
{
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

        //Verificar sincronización de datos del usuario
        $cursos = $this->sync_user_courses();

        $matriculacionesEstudiante= CursoParticipante::where('cvucv_user_id', $user->cvucv_id)
                    ->where('cvucv_rol_id',5)
                    ->get();

        $cursosEstudiante = collect();
        foreach($matriculacionesEstudiante as $matriculacion){
            $curso = Curso::find($matriculacion->cvucv_curso_id);
            if(!empty($curso)){
                $cursosEstudiante [] = $curso;
            }
        }

        //Cursos que puede ver como docente
        $matriculacionesDocente = CursoParticipante::where('cvucv_user_id', $user->cvucv_id)
                    ->where('cvucv_rol_id','!=',5)
                    ->get();
        $cursosDocente = collect();
        foreach($matriculacionesDocente as $matriculacion){
            $curso = Curso::find($matriculacion->cvucv_curso_id);
            if(!empty($curso)){
                $cursosDocente [] = $curso;
            }
        }
          
        

        return view('user.panel', compact('cursosEstudiante','cursosDocente'));
    }

    /**
     * Para procesar la evaluación de los instrumentos
     *
     * 
     */
    public function evaluacion($id_curso, $id_instrumento){
        $curso          = Curso::find($id_curso);
        $instrumento    = Instrumento::find($id_instrumento);
        $user           = Auth::user();

        //1. Verificamos que el curso y el instrumento existan
        if (!empty($curso) && !empty($instrumento)){ 

            //2. Verificamos que el usuario este matriculado en este curso
            if(empty(CursoParticipante::estaMatriculado($user->cvucv_id,$curso->id))){
                return redirect()->route('home')->with(['message' => "Error, no estas matriculado a este curso", 'alert-type' => 'error']);
            }
            
            //3. Verificamos que el instrumento sea valido
            if(!$instrumento->esValido()){
                return redirect()->route('home')->with(['message' => "Error, no puede evaluar en este momento", 'alert-type' => 'error']);
            }

            $categoria_raiz             = $curso->categoria->categoria_raiz;
            $instrumentos_habilitados   = $categoria_raiz->instrumentos_habilitados;
            //4. Verificamos que la categoria del curso tenga instrumentos habilitados para evaluar
            if(!empty($instrumentos_habilitados)){
                foreach($instrumentos_habilitados as $actual){
                    
                    //5. Verificamos que el curso puede ser evaluado por el instrumento (pasado por parametro)
                    if($actual->id == $instrumento->id){
                        
                        //6. Verificamos que el instrumento va dirigido al presente rol
                        $rolUsuarioCurso = CursoParticipante::where('cvucv_user_id', $user->cvucv_id)
                                ->where('cvucv_curso_id',$id_curso)
                                ->first()->cvucv_rol_id;

                        $instrumento_dirigido_usuario = $actual->instrumentoDirigidoaRol($rolUsuarioCurso);

                        if(!$instrumento_dirigido_usuario){
                            return redirect()->route('home')->with(['message' => "Error, no puede evaluar este curso", 'alert-type' => 'error']);
                        }

                        //7. Verificamos la cantidad de intentos de evaluacion del instrumento
                        if (Evaluacion::cantidad_evaluaciones_realizadas($instrumento->id, $curso->id, $user->id, $categoria_raiz->periodo_lectivo) >= 1){
                            return redirect()->route('home')->with(['message' => "Error, ha excedido la cantidad máxima de intentos para evaluar este curso", 'alert-type' => 'error']);
                        }

                        return view('user.evaluacion_cursos', compact('instrumento','curso'));
                    }
                }
                
            }
        }
        
        return redirect()->route('home')->with(['message' => "Error, no se puede evaluar este curso", 'alert-type' => 'error']);
        
    }
    public function evaluacion_procesar($id_curso, $id_instrumento,Request $request){
        $curso          = Curso::find($id_curso);
        $instrumento    = Instrumento::find($id_instrumento);
        $user           = Auth::user();
        
        //1. Verificamos que el curso y el instrumento existan
        if (!empty($curso) && !empty($instrumento)){ 

            //Verificamos que los campos del request no esten vacíos
            foreach($instrumento->categorias as $categorias){
                foreach($categorias->indicadores as $indicador){
                    if(!isset($request->{($indicador->id)} )){  
                        return redirect()->back()->withInput()->with(['message' => "Debe completar el campo: ".$indicador->nombre, 'alert-type' => 'error']);
                    }

                }
            }

            //2. Verificamos que el usuario este matriculado en este curso
            if(empty(CursoParticipante::estaMatriculado($user->cvucv_id,$curso->id))){
                return redirect()->route('home')->with(['message' => "Error, no estas matriculado a este curso", 'alert-type' => 'error']);
            }

            //3. Verificamos que el instrumento sea valido
            if(!$instrumento->esValido()){
                return redirect()->route('home')->with(['message' => "Error, no puede evaluar en este momento", 'alert-type' => 'error']);
            }

            $categoria_raiz             = $curso->categoria->categoria_raiz;
            $instrumentos_habilitados   = $categoria_raiz->instrumentos_habilitados;

            //4. Verificamos que la categoria del curso tenga instrumentos habilitados para evaluar
            if(!empty($instrumentos_habilitados)){
                foreach($instrumentos_habilitados as $actual){

                    //5. Verificamos que el curso puede ser evaluado por el instrumento (pasado por parametro)
                    if($actual->id == $instrumento->id){
                        
                        //6. Verificamos que el instrumento va dirigido al presente rol
                        $rolUsuarioCurso = CursoParticipante::where('cvucv_user_id', $user->cvucv_id)
                                ->where('cvucv_curso_id',$id_curso)
                                ->first()->cvucv_rol_id;

                        $instrumento_dirigido_usuario = $actual->instrumentoDirigidoaRol($rolUsuarioCurso);

                        if(!$instrumento_dirigido_usuario){
                            return redirect()->route('home')->with(['message' => "Error, no puede evaluar este curso", 'alert-type' => 'error']);
                        }
                        
                        //7. Verificamos la cantidad de intentos de evaluacion del instrumento
                        if (Evaluacion::cantidad_evaluaciones_realizadas($instrumento->id, $curso->id, $user->id, $categoria_raiz->periodo_lectivo) >= 1){
                            return redirect()->route('home')->with(['message' => "Error, ha excedido la cantidad máxima de intentos para evaluar este curso", 'alert-type' => 'error']);
                        }

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
                        $evaluacion->usuario_id          = $user->id;
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

                        return redirect()->route('home')->with(['message' => "Evaluacion al curso ".$curso->cvucv_fullname." realizada satisfactoriamente", 'alert-type' => 'success']);
                    }
                }
                
            }
        }
        
        return redirect()->route('home')->with(['message' => "Error, no se puede evaluar este curso", 'alert-type' => 'error']);
    }

    /*
    * Para visualizar los dashboards de la evaluacion de los cursos
    * Y lo relacionado a ese curso
    *
    */
    public function visualizar_curso($id){        
        
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
        $listadoParticipantesRevisores = [];
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

                    //Revisores del instrumento en el periodo lectivo
                    $revisores = Evaluacion::where('periodo_lectivo_id',$periodo->id)
                    ->where('instrumento_id',$instrumento->id)
                    ->where('curso_id',$curso->id)
                    ->get();
                    
                    foreach($revisores as $revisorIndex => $revisor){
                        $usuario = User::find($revisor->usuario_id);
                        $listadoParticipantesRevisores [$periodo_index][$instrumento_index][$revisorIndex] = $usuario;
                    }
                    
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
            'promedioPonderacionCurso',
            'listadoParticipantesRevisores'
        ));
    }

    public function sync_user_courses(){
        $user = Auth::user();

        $cursos_cvucv = $this->cvucv_get_users_courses($user->cvucv_id);

        //Construir un array de colecciones
        foreach($cursos_cvucv as $data){
            $cursos[] = Curso::create($data['id'],$data['shortname'],$data['category'],$data['fullname'],$data['displayname'],$data['summary'],$data['visible']);
        }  
        
        if(!empty($cursos_cvucv)){

            $cursos_array = [];
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

                //2. Verificamos que este matriculado en ese curso
                $matriculacion = CursoParticipante::where('cvucv_user_id', $user->cvucv_id)
                    ->where('cvucv_curso_id', $data['id'])
                    ->first();
                //Si no esta, hay que matricularlo
                if(empty($matriculacion)){

                    $matriculacion = new CursoParticipante;

                    $matriculacion->user_id        = $user->id;
                    $matriculacion->cvucv_user_id  = $user->cvucv_id;
                    $matriculacion->cvucv_curso_id = $data['id'];
                    $matriculacion->user_sync      = true;
                    $matriculacion->curso_sync     = false;

                    $matriculacion->save();

                }else{
                    //Ya esta syncronizada su data
                    if(!$matriculacion->user_sync){
                        $matriculacion->user_id     = $user->id;
                        $matriculacion->user_sync   = true;
                        $matriculacion->save();
                    }
                }

                array_push($cursos_array, $curso);
            }
            return $cursos_array;
        }else{
            return null;
        }

        return $cursos;
    }
    /**
     * CURL generíco usando GuzzleHTTP
     *
     */
    public function send_curl($request_type, $endpoint, $params){

        $client   = new \GuzzleHttp\Client();

        $response = $client->request($request_type, $endpoint, ['query' => $params ]);

        $statusCode = $response->getStatusCode();

        $content    = json_decode($response->getBody(), true);

        return $content;
    }
    /**
     * Obtiene los cursos de una categoria o
     * Obtiene los cursos por un campo
     */
    public function cvucv_get_category_courses($field,$value){
        $endpoint = env("CVUCV_GET_WEBSERVICE_ENDPOINT","https://campusvirtual.ucv.ve/moodle/webservice/rest/server.php");
        $wstoken  = env("CVUCV_ADMIN_TOKEN");

        $params = [
            'wsfunction'            => 'core_course_get_courses_by_field',
            'wstoken'               => $wstoken,
            'moodlewsrestformat'    => 'json',
            'field'                 => $field,
            'value'                 => $value
        ];

        $response = $this->send_curl('POST', $endpoint, $params);
        
        return $response['courses'];
    }
    /**
     * Obtiene los cursos en los que está matriculado un usuario
     *
     */
    public function cvucv_get_users_courses($user_id){
        $endpoint = env("CVUCV_GET_WEBSERVICE_ENDPOINT","https://campusvirtual.ucv.ve/moodle/webservice/rest/server.php");
        $wstoken  = env("CVUCV_ADMIN_TOKEN");

        $params = [
            'wsfunction'            => 'core_enrol_get_users_courses',
            'wstoken'               => $wstoken,
            'moodlewsrestformat'    => 'json',
            'userid'                => $user_id
        ];

        $response = $this->send_curl('POST', $endpoint, $params);
        
        return $response;
    }
    /**
     * Obtiene los participantes de un curso
     *
     */
    public function cvucv_get_participantes_curso($course_id){
        $endpoint = env("CVUCV_GET_WEBSERVICE_ENDPOINT","https://campusvirtual.ucv.ve/moodle/webservice/rest/server.php");
        $wstoken  = env("CVUCV_ADMIN_TOKEN");

        $params = [
            'wsfunction'            => 'core_enrol_get_enrolled_users',
            'wstoken'               => $wstoken,
            'moodlewsrestformat'    => 'json',
            'courseid'              => $course_id
        ];

        $response = $this->send_curl('POST', $endpoint, $params);
        
        return $response;
    }
    /**
     * Obtiene las categorias de los cursos
     *
     */
    public function cvucv_get_courses_categories($key = 'id', $value, $subcategories = 0){
        $endpoint = env("CVUCV_GET_WEBSERVICE_ENDPOINT","https://campusvirtual.ucv.ve/moodle/webservice/rest/server.php");
        $wstoken  = env("CVUCV_ADMIN_TOKEN");

        $params = [
            'wsfunction'            => 'core_course_get_categories',
            'wstoken'               => $wstoken,
            'moodlewsrestformat'    => 'json',
            'criteria[0][key]'      => $key,
            'criteria[0][value]'    => $value,
            'addsubcategories'      => $subcategories
        ];

        $response = $this->send_curl('POST', $endpoint, $params);
        
        return $response;
    }
}
