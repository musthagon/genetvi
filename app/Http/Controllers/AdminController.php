<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Instrumento;
use App\Curso;
use App\CategoriaDeCurso;
use App\CursoParticipante;
use App\PeriodoLectivo;
use App\Evaluacion;
use App\Charts\indicadoresChart;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;


class AdminController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(){
        //$this->middleware('auth');
        //Voyager admin middleware
        $this->middleware('admin.user');
    }   

    /*Para revisar si el usuario es admin */
    public function isAdminRedirect(){
        $isAdmin = Auth::user()->hasRole('admin');

        if(!$isAdmin){
            return redirect()->route('home');
        }
    }
    /**
     * Para  Visualizar y sincronizar las categorias, cursos, participantes, roles
     *
     * 
     */
    public function gestion($id = 0){ 
        $wstoken  = env("CVUCV_ADMIN_TOKEN");
        $user = Auth::user();

        if($id == 0){
            //Consultamos la api
            $categorias_padre = $this->cvucv_get_courses_categories('parent',0);

            $categorias = collect();
            foreach($categorias_padre as $data){
                if (Gate::allows('checkCategoryPermissionSisgeva', ['ver_',$data['name']]  )) {                  
                    $categorias[] = CategoriaDeCurso::create($data['id'],$data['parent'],$data['name'],$data['description'],$data['coursecount'],$data['visible'],$data['depth'],$data['path']);
                }
            }  
           
            //O si no, la BD
            if(empty($categorias_padre) || $categorias->isEmpty()){
                $categoriasDB = CategoriaDeCurso::where('cvucv_category_parent_id', $id)->get();
                foreach($categoriasDB as $categoria){
                    if (Gate::allows('checkCategoryPermissionSisgeva', ['ver_',$categoria->cvucv_name]  )) {    
                        $categorias[] = $categoria;
                    }
                }
            }

            if(!empty($categorias)){
                $categorias = collect($categorias);
            }

        }else{
            //Tienen acceso?
            $categoria = CategoriaDeCurso::where('id', $id)->first();
            if(!empty($categoria) ){
                if($categoria->cvucv_category_parent_id == 0){
                    $categoriaSuperPadre = $categoria;
                }else{
                    $categoriaSuperPadre = CategoriaDeCurso::where('id', $categoria->cvucv_category_super_parent_id)->first();
                }
                
                if (!empty($categoriaSuperPadre) && !Gate::allows('checkCategoryPermissionSisgeva', ['ver_',$categoriaSuperPadre->cvucv_name]  )) {    
                    return redirect('/admin')->with(['message' => "Error, acceso no autorizado", 'alert-type' => 'error']);
                }
            }

            $categorias = collect();
            $categoriasDB = CategoriaDeCurso::where('cvucv_category_parent_id', $id)->get();
            
            if(!$categoriasDB->isEmpty()){
                foreach($categoriasDB as $categoria){
                        $categorias[] = $categoria;

                }
                if(!empty($categorias)){
                    $categorias = collect($categorias);
                }
            }
            
            $cursos = Curso::where('cvucv_category_id', $id)->get();
            
            if(!$cursos->isEmpty()){
                return view('vendor.voyager.gestion.index',compact('categorias','cursos','wstoken'));
            }else{
                if($categorias->isEmpty()){
                    return redirect()->back()->with(['message' => "Categoría sin datos, intente sincronizarla", 'alert-type' => 'error']);
                }
            }
        }

        return view('vendor.voyager.gestion.index',compact('categorias','wstoken'));
    }
    public function gestion_cursos($id){
        $wstoken  = env("CVUCV_ADMIN_TOKEN");

        $cursos = Curso::where('cvucv_category_id', $id)->get();

        return view('vendor.voyager.gestion.index_courses',compact('cursos','wstoken'));
    }
    public function gestion_sincronizar($id, Request $request){
        //Tienen acceso?
        $categoria = CategoriaDeCurso::where('id', $id)->first();
        if(!empty($categoria) ){
            if($categoria->cvucv_category_parent_id == 0){
                $categoriaSuperPadre = $categoria;
            }else{
                $categoriaSuperPadre = CategoriaDeCurso::where('id', $categoria->cvucv_category_super_parent_id)->first();
            }
            
            if (!empty($categoriaSuperPadre) && !Gate::allows('checkCategoryPermissionSisgeva', ['sincronizar_',$categoriaSuperPadre->cvucv_name]  )) {    
                return redirect('/admin')->with(['message' => "Error, acceso no autorizado", 'alert-type' => 'error']);
            }
        }
        
        if(isset($request->sync_courses)){
            //dd($request->sync_courses);
            return $this->gestion_sincronizar_cursos_categorias($id,$request);
        }
        return $this->gestion_sincronizar_categorias($id,$request);
    }
    public function gestion_sincronizar_categorias($id, Request $request){
        $categorias = $this->cvucv_get_courses_categories('id',$id,1);

        try {
            foreach($categorias as $categoria){

                $nueva_categoria = CategoriaDeCurso::find($categoria['id']);

                //1. Verificamos que existan los cursos
                //Si no existe, hay que crearlo
                if(empty($nueva_categoria)){
                    $nueva_categoria = new CategoriaDeCurso;
                }

                $nueva_categoria->id                         = $categoria['id'];
                $nueva_categoria->cvucv_category_parent_id   = $categoria['parent'];
                if(isset($request->categoria_raiz) && $categoria['id'] != $id ){
                    $nueva_categoria->cvucv_category_super_parent_id   = $id;
                }
                $nueva_categoria->cvucv_name                 = $categoria['name'];
                $nueva_categoria->cvucv_description          = $categoria['description'];
                $nueva_categoria->cvucv_coursecount          = $categoria['coursecount'];
                $nueva_categoria->cvucv_visible              = $categoria['visible'];
                $nueva_categoria->cvucv_path                 = $categoria['path'];
                $nueva_categoria->cvucv_depth                = $categoria['depth'];
                $nueva_categoria->cvucv_visible              = $categoria['visible'];
                $nueva_categoria->cvucv_link                 = env("CVUCV_GET_SITE_URL")."/moodle/course/index.php?categoryid=".$categoria['id'];

                $nueva_categoria->save();

            }
            return redirect()->route('gestion.evaluaciones2', ['id' => $id])->with(['message' => "Datos sincronizados", 'alert-type' => 'success']);
        } catch (Exception $e) {
            return redirect()->route('gestion.evaluaciones2', ['id' => $id])->with($this->alertException($e, 'Error al sincronizar'));
        }
    }   
    public function gestion_sincronizar_cursos_categorias($id,$request){
        
        try {
            //Cursos -> matriculaciones -> roles
            $cursos_de_la_categoria = $this->cvucv_get_category_courses('category',$id);

            foreach($cursos_de_la_categoria as $data){

                $curso = Curso::find($data['id']);

                //1. Verificamos que existan los cursos
                //Si no existe, hay que crearlo
                if(empty($curso)){
                    $curso = new Curso;
                }
                $curso->id                  = $data['id'];
                $curso->cvucv_shortname     = $data['shortname'];
                $curso->cvucv_category_id   = $data['categoryid'];
                $curso->cvucv_fullname      = $data['fullname'];
                $curso->cvucv_displayname   = $data['displayname'];
                $curso->cvucv_summary       = $data['summary'];
                $curso->cvucv_visible       = $data['visible'];
                $curso->cvucv_link          = env("CVUCV_GET_SITE_URL")."/course/view.php?id=".$data['id'];

                $curso->save();
                
                $participantes_curso = $this->cvucv_get_participantes_curso($data['id']);

                //Sincronizamos sus participantes
                foreach($participantes_curso as $participante){
                
                    //2. Verificamos que este matriculado en ese curso
                    $matriculacion = CursoParticipante::where('cvucv_user_id', $participante['id'])
                        ->where('cvucv_curso_id', $data['id'])
                        ->first();
                    //Si no esta, hay que matricularlo
                    if(empty($matriculacion)){
                        $matriculacion                 = new CursoParticipante;
                        $matriculacion->cvucv_user_id  = $participante['id'];
                        $matriculacion->cvucv_curso_id = $data['id'];
                        $matriculacion->user_sync      = false;
                    }
                    $matriculacion->cvucv_rol_id = $participante['roles'][0]['roleid'];
                    $matriculacion->curso_sync   = true;
                    $matriculacion->save();

                }

            }              
        } catch (Exception $e) {
            return redirect()->route('gestion.evaluaciones2', ['id' => $id])->with($this->alertException($e, 'Error al sincronizar cursos'));
        }

        return $this->gestion_sincronizar_categorias($id,$request);
    }
    /**
     * Para procesar la evaluación de los instrumentos
     *
     * 
     */
    public function gestionar_evaluacion_categoria($id){
        /*$categoria = CategoriaDeCurso::find($id);*/

        //Tienen acceso?
        $categoria = CategoriaDeCurso::where('id', $id)->first();
        if(!empty($categoria) ){
            if($categoria->cvucv_category_parent_id == 0){
                $categoriaSuperPadre = $categoria;
            }else{
                $categoriaSuperPadre = CategoriaDeCurso::where('id', $categoria->cvucv_category_super_parent_id)->first();
            }
            
            if (!empty($categoriaSuperPadre) && !Gate::allows('checkCategoryPermissionSisgeva', ['habilitar_evaluacion_',$categoriaSuperPadre->cvucv_name]  )) {    
                return redirect('/admin')->with(['message' => "Error, acceso no autorizado", 'alert-type' => 'error']);
            }
        }


        if(empty($categoria)){
            return redirect()->back()->with(['message' => "La categoría no existe. Intente, sincronizarla", 'alert-type' => 'error']);
        }
        if($categoria->cvucv_category_parent_id != 0){
            return redirect()->back()->with(['message' => "No es una categoría principal", 'alert-type' => 'error']);
        }

        //Va a editarla ?
        $edit = false;
        if(!is_null($categoria->periodo_lectivo) ){
            $edit = true;
        }

        $instrumentos       = Instrumento::all();

        $periodos_lectivos  = PeriodoLectivo::all();

        if(empty($instrumentos)){
            return redirect()->back()->with(['message' => "No hay instrumentos de evaluación", 'alert-type' => 'error']);
        }
        if(empty($periodos_lectivos)){
            return redirect()->back()->with(['message' => "No hay periodos lectivos registrados", 'alert-type' => 'error']);
        }

        return view('vendor.voyager.gestion.gestionar_evaluacion_categoria',compact('edit', 'categoria', 'instrumentos','periodos_lectivos'));
    }
    public function gestionar_evaluacion_categoria_store($id,Request $request){
        if(!isset($request->periodo_lectivo)){
            return redirect()->back()->with(['message' => "Debe seleccionar el periodo lectivo", 'alert-type' => 'error']);
        }
        if(!isset($request->instrumentos)){
            return redirect()->back()->with(['message' => "Debe seleccionar algun instrumento", 'alert-type' => 'error']);
        }

        $categoria = CategoriaDeCurso::find($id);
        
        if(empty($categoria)){
            return redirect()->back()->with(['message' => "La categoría no existe. Intente, sincronizarla", 'alert-type' => 'error']);
        }
        if($categoria->cvucv_category_parent_id != 0){
            return redirect()->back()->with(['message' => "No es una categoría principal", 'alert-type' => 'error']);
        }
        $categoria->periodo_lectivo = $request->periodo_lectivo;
        $categoria->save();

        foreach($request->instrumentos as $instrumento){
            if($instrumento == 'null'){
                $categoria->instrumentos_habilitados()->detach();
                break;
            }else{
                $categoria->instrumentos_habilitados()->attach($instrumento);
            }
        }
        
        return redirect()->route('gestion.evaluaciones')->with(['message' => "Instrumentos habilitados para esta categoría", 'alert-type' => 'success']);

    }
    public function gestionar_evaluacion_categoria_edit($id,Request $request){
        if(!isset($request->periodo_lectivo)){
            return redirect()->back()->with(['message' => "Debe seleccionar el periodo lectivo", 'alert-type' => 'error']);
        }
        if(!isset($request->instrumentos)){
            return redirect()->back()->with(['message' => "Debe seleccionar algun instrumento", 'alert-type' => 'error']);
        }

        $categoria = CategoriaDeCurso::find($id);

        if(empty($categoria)){
            return redirect()->back()->with(['message' => "La categoría no existe. Intente, sincronizarla", 'alert-type' => 'error']);
        }
        if($categoria->cvucv_category_parent_id != 0){
            return redirect()->back()->with(['message' => "No es una categoría principal", 'alert-type' => 'error']);
        }
        if($categoria->periodo_lectivo != $request->periodo_lectivo){
            $categoria->periodo_lectivo = $request->periodo_lectivo;
            $categoria->save();
        }

        // Detach all roles from the categoria...
        $categoria->instrumentos_habilitados()->detach();
        foreach($request->instrumentos as $instrumento){
            if($instrumento == 'null'){
                $categoria->instrumentos_habilitados()->detach();
                break;
            }else{
                $categoria->instrumentos_habilitados()->attach($instrumento);
            }
        }
        
        return redirect()->route('gestion.evaluaciones')->with(['message' => "Instrumentos actualizados para esta categoría", 'alert-type' => 'success']);
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

        //Tiene permitido acceder?**********************
        $categoria = CategoriaDeCurso::where('id', $curso->cvucv_category_id)->first();
        if(!empty($categoria) ){
            if($categoria->cvucv_category_parent_id == 0){
                $categoriaSuperPadre = $categoria;
            }else{
                $categoriaSuperPadre = CategoriaDeCurso::where('id', $categoria->cvucv_category_super_parent_id)->first();
            }
            
            if (!empty($categoriaSuperPadre) && !Gate::allows('checkCategoryPermissionSisgeva', ['ver_',$categoriaSuperPadre->cvucv_name]  )) {    
                return redirect('/admin')->with(['message' => "Error, acceso no autorizado", 'alert-type' => 'error']);
            }
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
                                    'text' => 'Respuestas del indicador: '.$indicador->nombre.'<br> del Instrumento: '.$instrumento->nombre
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

        return view('vendor.voyager.gestion.cursos_dashboards',
        compact(
            'curso',
            'periodos_collection',
            'instrumentos_collection',
            'IndicadoresCharts',
            'cantidadEvaluacionesCursoCharts',
            'promedioPonderacionCurso'
        ));
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
    public function cvucv_get_category_courses($field,$value)    {
        $endpoint = env("CVUCV_GET_WEBSERVICE_ENDPOINT");
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
    public function cvucv_get_users_courses($user_id)    {
        $endpoint = env("CVUCV_GET_WEBSERVICE_ENDPOINT");
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
    public function cvucv_get_participantes_curso($course_id)    {
        $endpoint = env("CVUCV_GET_WEBSERVICE_ENDPOINT");
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
        $endpoint = env("CVUCV_GET_WEBSERVICE_ENDPOINT");
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
