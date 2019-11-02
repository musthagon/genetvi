<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Instrumento;
use App\Curso;
use App\CategoriaDeCurso;
use App\PeriodoLectivo;
use App\Evaluacion;
use App\Categoria;
use App\Indicador;
use App\Invitacion;
use App\Charts\indicadoresChart;
//use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Yajra\Datatables\Datatables;

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

    public function isAdminRedirect(){//Verifica si el usuario es admin
        $isAdmin = Auth::user()->hasRole('admin');

        if(!$isAdmin){
            return redirect()->route('home');
        }
    }
    public function checkAccess_ver($curso){//Verifica si tiene acceso a visualizar la categoría del curso
        $categoria = CategoriaDeCurso::find($curso->cvucv_category_id);

        if(empty($categoria) ){
            return redirect()->back()->with(['message' => "La categoria de este curso no existe", 'alert-type' => 'error']);
        }
        $categoriaSuperPadre = $categoria->categoria_raiz;
        
        if (!empty($categoriaSuperPadre) && !Gate::allows('checkCategoryPermissionSisgeva', ['ver_',$categoriaSuperPadre->cvucv_name]  )) {    
            return redirect('/admin')->with(['message' => "Error, acceso no autorizado", 'alert-type' => 'error']);
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
                $nueva_categoria->cvucv_link                 = env("CVUCV_GET_SITE_URL","https://campusvirtual.ucv.ve")."/moodle/course/index.php?categoryid=".$categoria['id'];

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
                $curso->cvucv_link          = env("CVUCV_GET_SITE_URL","https://campusvirtual.ucv.ve")."/course/view.php?id=".$data['id'];

                $curso->save();
                
                /*
                * NO SINCRONIZAR PARTICIPANTES AHORA
                *
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

                }*/

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
        $opciones_instrumento = $indicador->indicadorOpciones($categoria->likertOpciones());
        
                
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
    public function consultar_grafico_generales($curso_id, $tipo){//Crea la data de los dashboards consultados dinamicamente fetch() JS
        if(!isset($curso_id) || !isset($tipo)){
            return json_encode (json_decode ("{}"));
        }

        //$tipo = (int)$tipo ;
        if($tipo != "1" && $tipo != "2"){
            /*return json_encode (json_decode ("{}"));*/
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
                    $query [$periodo_index][$instrumento_index] = Evaluacion::where('periodo_lectivo_id',$periodo->id)
                        ->where('instrumento_id',$instrumento->id)
                        ->where('curso_id',$curso->id)->count();

                }else{
                    $query [$periodo_index][$instrumento_index] = Evaluacion::where('periodo_lectivo_id',$periodo->id)
                        ->where('instrumento_id',$instrumento->id)
                        ->where('curso_id',$curso->id)
                        ->avg('percentil_eva');
                }
            }

            $chart->dataset($periodo->nombre, 'bar', $query[$periodo_index]);
            
        }

        return $chart->api();

    }
    public function script_tabla_indicador($curso, $periodo, $instrumento, $categoria, $indicador){
        $script = "$('#Indicador_".$indicador->id."').DataTable({
            'processing': true,
            'serverSide': true,
            'ajax': '". route('curso.consultar_tabla_indicador', 
                ['curso' => $curso->id, 
                'periodo' => $periodo->id, 
                'instrumento' => $instrumento->id, 
                'categoria' => $categoria->id, 
                'indicador' => $indicador->id]) ."',
            'columns': [
                {data: 'value_string', name: 'value_string'}
            ]
        });
        ";
        return $script;
    }
    public function html_tabla_indicador($curso, $periodo, $instrumento, $categoria, $indicador){
        $script = "<div class='chartTarget col-md-6 mix Periodo_".$periodo->id." Instrumento_".$instrumento->id." Categoria_".$categoria->id." Indicador_".$indicador->id." general'>
            <div class='indicador_title' style='color: #333333;font-size: 18px;fill: #333333;'>".$indicador->getNombre()."</div>
            <table id='Indicador_".$indicador->id."' class='table table-hover table-condensed'>
                <thead>
                <tr>
                    <th>".$indicador->getNombre()."</th>
                </tr>
                </thead>
            </table>
        </div>";
        return $script;
    }
    public function visualizar_resultados_curso($curso_id){//Crea la vista del dashboard/graficos del curso
        $curso = Curso::find($curso_id);
        
        if(empty($curso)){
            return redirect()->back()->with(['message' => "El curso no existe", 'alert-type' => 'error']);
        }

        //Tiene permitido acceder a la categoria?
        $this->checkAccess_ver($curso);

        //instrumentos con los cuales han evaluado este curso
        Evaluacion::instrumentos_de_evaluacion_del_curso($curso->id, $instrumentos_collection, $nombreInstrumentos);
        
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
        $script_tabla_indicador = "";
        $html_tabla_indicador = "";
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
            }else{
                $script_tabla_indicador .= $this->script_tabla_indicador($curso, $periodo, $instrumento, $categoria, $indicador);
                $html_tabla_indicador .= $this->html_tabla_indicador($curso, $periodo, $instrumento, $categoria, $indicador);
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

        
        return view('vendor.voyager.gestion.cursos_dashboards_test',
        compact(
            'curso',
            'periodos_collection',
            'instrumentos_collection',
            'categorias_collection',
            'indicadores_collection',
            'indicadores_collection_charts',
            'cantidadEvaluacionesCursoCharts',
            'promedioPonderacionCurso',
            'html_tabla_indicador',
            'script_tabla_indicador'
        ));

    }

    public function visualizar_curso($id){        
        
        $curso = Curso::find($id);
        
        if(empty($curso)){
            return redirect()->back()->with(['message' => "El curso no existe", 'alert-type' => 'error']);
        }

        
        //Tiene permitido acceder a la categoria?
        $this->checkAccess_ver($curso);

        //instrumentos con los cuales han evaluado este curso
        Evaluacion::instrumentos_de_evaluacion_del_curso($curso->id, $instrumentos_collection, $nombreInstrumentos);

        //periodos lectivos con los cuales han evaluado este curso
        $periodos_collection        = Evaluacion::periodos_lectivos_de_evaluacion_del_curso($curso->id);

        //categorias con los cuales han evaluado este curso
        $categorias_collection      = Evaluacion::categorias_de_evaluacion_del_curso($curso->id);
        
        //indicadores con los cuales han evaluado este curso
        $indicadores_collection     = Evaluacion::indicadores_de_evaluacion_del_curso($curso->id);

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

                        foreach($categoria->indicadores as $indicador_index=>$indicador){

                            $opciones_instrumento = $indicador->indicadorOpciones($categoria->likertOpciones());
                            
                            $k=0;
                            $opciones_indicador = [];
                            foreach($opciones_instrumento as $key=>$opcion){
                                
                                $valor = Evaluacion::cantidad_respuestas_para_indicador($curso, $instrumento, $periodo, $indicador, $key);
                                $opciones_indicador[$k] = $valor;
                                $k++;
                            }
                            
                            $IndicadoresCharts[$periodo_index][$instrumento_index][$categoria_index][$indicador_index] = new indicadoresChart;
                            $IndicadoresCharts[$periodo_index][$instrumento_index][$categoria_index][$indicador_index]->labels(array_keys($opciones_instrumento));
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
                    /*$revisores = Evaluacion::where('periodo_lectivo_id',$periodo->id)
                    ->where('instrumento_id',$instrumento->id)
                    ->where('curso_id',$curso->id)
                    ->get();
                    
                    /*foreach($revisores as $revisorIndex => $revisor){
                        $usuario = User::find($revisor->usuario_id);
                        $listadoParticipantesRevisores [$periodo_index][$instrumento_index][$revisorIndex] = $usuario;
                    }*/
                    
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



        //Buscamos los participantes
        $participantes = $this->cvucv_get_participantes_curso($curso->id);

        return view('vendor.voyager.gestion.cursos_dashboards',
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
    }
    
    /*
    * Para gestionar la evaluacion
    *
    */
    public function iniciar_evaluacion_curso($id){        
        $curso = Curso::find($id);
        
        if(empty($curso)){
            return redirect()->back()->with(['message' => "El curso no existe", 'alert-type' => 'error']);
        }

        $categoria_raiz             = $curso->categoria->categoria_raiz;
        $instrumentos_habilitados   = $categoria_raiz->instrumentos_habilitados;

        if($instrumentos_habilitados->isEmpty() || ($categoria_raiz->periodo_lectivo==NULL)){
            return redirect()->back()->with(['message' => "No está habilitada la evaluación para esta facultad/centro o dependencia", 'alert-type' => 'error']);
        }

        //Buscamos los participantes
        $participantes = $this->cvucv_get_participantes_curso($curso->id);

        foreach($instrumentos_habilitados as $instrumento){
            if($instrumento->invitacion_automatica){ //El instrumento es de matriculacion automatica
                foreach($participantes as $indexParticipante => $participante){

                    //Verificamos que no tenga invitación previa
                    $invitacionAnterior = Invitacion::where('curso_id', $curso->id)
                    ->where('periodo_lectivo_id', $categoria_raiz->periodo_lectivo)
                    ->where('cvucv_user_id', $participante['id'])
                    ->where('instrumento_id', $instrumento->id)
                    ->first();

                    //Si no tiene invitación, hay que crearla
                    if(empty($invitacionAnterior)){

                        //Verificamos que el instrumento va a dirigido al usuario
                        if(isset($participante['roles']) && !empty($participante['roles'])){

                            $rolUsuarioCurso = $participante['roles'][0]['roleid'];
                            
                            $instrumento_dirigido_usuario = $instrumento->instrumentoDirigidoaRol($rolUsuarioCurso);

                            if($instrumento_dirigido_usuario){
                                do {
                                    //generate a random string using Laravel's str_random helper
                                    $token = str_random();
                                } //verificamos que el token no exista
                                while (Invitacion::where('token', $token)->first());
        
                                //se crea la invitacion
                                $invite = Invitacion::create([
                                    'token'                 => $token,
                                    'estatus_invitacion_id' => 1, //Invitacion creada
                                    'cantidad_recordatorios' => 0, 
                                    'tipo_invitacion_id'       => 2, //Invitacion automatica
                                    'instrumento_id'        => $instrumento->id,
                                    'curso_id'              => $curso->id,
                                    'periodo_lectivo_id'    => $categoria_raiz->periodo_lectivo,
                                    'cvucv_user_id'         => $participante['id'],
                                    /*'usuario_id'            => $token,*/
                                    
                                ]);
                            }
                        }

                    }else{
                        $invitacionAnterior->estatus_invitacion_id = 3;
                        $invitacionAnterior->cantidad_recordatorios += 1;
                        $invitacionAnterior->save();
                    }
                }
            }
        }
        //Actualizamos el atributo
        $curso->actualizarEvaluacion(true);
        
        return redirect()->back()->with(['message' => "Evaluación iniciada", 'alert-type' => 'success']);
    }
    public function cerrar_evaluacion_curso($id){
        $curso = Curso::find($id);
        
        if(empty($curso)){
            return redirect()->back()->with(['message' => "El curso no existe", 'alert-type' => 'error']);
        }

        //Actualizamos el atributo
        $curso->actualizarEvaluacion(false);

        return redirect()->back()->with(['message' => "Evaluación cerrada", 'alert-type' => 'warning']);
    }
    public function estatus_evaluacion_curso($id){
        $curso = Curso::find($id);
        
        if(empty($curso)){
            return redirect()->back()->with(['message' => "El curso no existe", 'alert-type' => 'error']);
        }
        
        //Invitaciones para el periodo lectivo actual....
        $periodo_lectivo_actual = $curso->periodo_lectivo_actual();
        if(empty($periodo_lectivo_actual)){
            return redirect()->back()->with(['message' => "Error, no se encuentra configurado el periodo lectivo actual", 'alert-type' => 'error']);
        }

        $invitaciones_curso = Invitacion::where('curso_id',$curso->id)->where('periodo_lectivo_id',$periodo_lectivo_actual->id)->get();

        //Buscamos los participantes
        //$participantes = $this->cvucv_get_participantes_curso($curso->id);

        //Buscamos
        $revisores = [];
        foreach($invitaciones_curso as $invitacion_index => $invitacion){
            $revisores[$invitacion_index] = $invitacion->user_profile();
        }

        //Instrumentos de matriculacion manuak
        $categoria_raiz             = $curso->categoria->categoria_raiz;
        $instrumentos_habilitados   = $categoria_raiz->instrumentos_habilitados;
        $instrumentos_manuales = [];
        foreach($instrumentos_habilitados as $instrumento){
            if(!$instrumento->invitacion_automatica){ //Instrumentos de matriculacion manual
                array_push($instrumentos_manuales, $instrumento);
            }
            
        }

        return view('vendor.voyager.gestion.cursos_estatus_evaluacion',
        compact(
            'curso',
            'periodo_lectivo_actual',
            'invitaciones_curso',
            'revisores',
            'instrumentos_manuales'
        ));
    }
    public function enviar_recordatorio($id_curso, $invitacion){        
        $curso = Curso::find($id_curso);
        
        if(empty($curso)){
            return redirect()->back()->with(['message' => "El curso no existe", 'alert-type' => 'error']);
        }

        //Verificamos que tenga invitación previa
        $invitacionAnterior = Invitacion::find($invitacion);

        //Si no tiene invitación
        if(empty($invitacionAnterior)){
            return redirect()->back()->with(['message' => "La invitación no existe", 'alert-type' => 'error']);
        }

        //Enviamos la invitacion
        $message = $this->messageTemplate($invitacionAnterior->user_profile(), $curso, $invitacionAnterior->token);
        $response = $this->cvucv_send_instant_message($invitacionAnterior->cvucv_user_id, $message, 1);

        if(isset($response[0]['msgid'])){
            if($response[0]['msgid'] == -1){
                return redirect()->back()->with(['message' => "Error para enviar recordatorio, intente luego", 'alert-type' => 'error']);
            }
        }

        //Actualizamos
        $invitacionAnterior->estatus_invitacion_id = 3;
        $invitacionAnterior->cantidad_recordatorios += 1;
        $invitacionAnterior->save();
        
        
        return redirect()->back()->with(['message' => "Recordatorio enviado", 'alert-type' => 'success']);
    }
    public function revocar_invitacion($id_curso, $invitacion){        
        $curso = Curso::find($id_curso);
        
        if(empty($curso)){
            return redirect()->back()->with(['message' => "El curso no existe", 'alert-type' => 'error']);
        }

        //Verificamos que tenga invitación previa
        $invitacionAnterior = Invitacion::find($invitacion);

        //Si no tiene invitación
        if(empty($invitacionAnterior)){
            return redirect()->back()->with(['message' => "La invitación no existe", 'alert-type' => 'error']);
        }

        //Actualizamos
        $invitacionAnterior->estatus_invitacion_id = 8;
        $invitacionAnterior->cantidad_recordatorios = 0;
        $invitacionAnterior->save();
        
        return redirect()->back()->with(['message' => "Invitación a evaluar revocada", 'alert-type' => 'success']);
    }
    public function invitar_evaluacion_curso($id, Request $request){
        
        if(!isset($request->users)){
            return redirect()->back()->with(['message' => "No ha seleccionado ningún usuario para invitar a evaluar", 'alert-type' => 'error']);
        }

        if(!isset($request->instrumentos_manuales)){
            return redirect()->back()->with(['message' => "No ha seleccionado ningún instrumento", 'alert-type' => 'error']);
        }
        $curso = Curso::find($id);
        
        if(empty($curso)){
            return redirect()->back()->with(['message' => "El curso no existe", 'alert-type' => 'error']);
        }

        $instrumentos = $request->instrumentos_manuales;
        $usuarios = $request->users;
        $total_invitacion = 0 ;
        foreach($instrumentos as $instrumentoIndex => $instrumento){

            $instrumentoActual = Instrumento::find($instrumento);
            if(empty($instrumentoActual)){
                return redirect()->back()->with(['message' => "Uno de los instrumentos seleccionados no existe", 'alert-type' => 'error']);
            }
            if($instrumentoActual->invitacion_automatica){
                return redirect()->back()->with(['message' => "El instrumento: ".$instrumentoActual->nombre." no es válido", 'alert-type' => 'error']);
            }

            foreach($usuarios as $usuarioIndex => $usuario){


                do {
                    //generate a random string using Laravel's str_random helper
                    $token = str_random();
                } //verificamos que el token no exista
                while (Invitacion::where('token', $token)->first());

                //se crea la invitacion
                Invitacion::create([
                    'token'                     => $token,
                    'estatus_invitacion_id'     => 1, //Invitacion creada
                    'cantidad_recordatorios'    => 0, 
                    'tipo_invitacion_id'        => 1, //Invitacion automatica
                    'instrumento_id'            => $instrumentoActual->id,
                    'curso_id'                  => $curso->id,
                    'periodo_lectivo_id'        => $curso->periodo_lectivo_actual()->id,
                    'cvucv_user_id'             => $usuario                    
                ]);

            }
            $total_invitacion = ($instrumentoIndex + 1)*($usuarioIndex + 1);
        }

        return redirect()->back()->with(['message' => $total_invitacion." Usuarios invitados", 'alert-type' => 'success']);
    }
    public function messageTemplate($user_profile, $curso, $token){//Mensaje de invitación enviado por el Campus/Correo electronico
        $message = "<div> Estimado ".$user_profile['fullname'].", este es un mensaje de prueba de la aplicación GENETVI, ya que te encuentras matriculado en el curso". $curso->cvucv_fullname."</div>
        <div> <a href=".route('evaluacion_link', ['token' => $token])."> Enlace para evaluar curso ".$curso->cvucv_fullname." </a> </div>";

        return $message;
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
    public function cvucv_get_users_courses($user_id)    {
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
    public function cvucv_get_participantes_curso($course_id)    {
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
    /**
     * Envia un mensaje instantaneo al usuario
     *
     */
    public function cvucv_send_instant_message($user_id, $message, $format)    {
        $endpoint = env("CVUCV_GET_WEBSERVICE_ENDPOINT","https://campusvirtual.ucv.ve/moodle/webservice/rest/server.php");
        $wstoken  = env("CVUCV_ADMIN_TOKEN");

        $params = [
            'wsfunction'                => 'core_message_send_instant_messages',
            'wstoken'                   => $wstoken,
            'moodlewsrestformat'        => 'json',
            'messages[0][touserid]'     => $user_id,
            'messages[0][text]'         => $message,
            'messages[0][textformat]'   => $format,
        ];

        $response = $this->send_curl('POST', $endpoint, $params);
        
        return $response;
    }

    /**
     * Consulta los usuarios del Campus Virtual y configura la paginación
     *
     */
    public function campus_users(Request $request){

        $endpoint = env("CVUCV_GET_WEBSERVICE_ENDPOINT","https://campusvirtual.ucv.ve/moodle/webservice/rest/server.php");
        $wstoken  = env("CVUCV_ADMIN_TOKEN2");

        if(!isset($request->lastname)){
            return [];
        }

        $values = preg_split('/[\s,]+/', $request->lastname, 2);

        if(isset($values[0]) && isset($values[1])){
            $nombre = "%".$values[0]."%"; 
            $apellido = "%".$values[1]."%"; 

            $params = [
                'wsfunction'            => 'core_user_get_users',
                'wstoken'               => $wstoken,
                'moodlewsrestformat'    => 'json',
                'criteria[0][key]'      => "firstname",
                'criteria[0][value]'    => $nombre,
                'criteria[1][key]'      => "lastname",
                'criteria[1][value]'    => $apellido
            ];
        }elseif(isset($values[0])){
            $nombre = "%".$values[0]."%";  

            $params = [
                'wsfunction'            => 'core_user_get_users',
                'wstoken'               => $wstoken,
                'moodlewsrestformat'    => 'json',
                'criteria[0][key]'      => "firstname",
                'criteria[0][value]'    => $nombre
            ];
        }elseif(isset($values[1])){
            $apellido = "%".$values[1]."%"; 
            
            $params = [
                'wsfunction'            => 'core_user_get_users',
                'wstoken'               => $wstoken,
                'moodlewsrestformat'    => 'json',
                'criteria[0][key]'      => "lastname",
                'criteria[0][value]'    => $apellido
            ];
        }else{
            return [];
        }

        $response = $this->send_curl('POST', $endpoint, $params);

        //Construimos la paginación
        if(isset($response['users']) && isset($request->page)){
            $page = $request->page;
            $pagination = 20;

            $offset = ($page - 1) * $pagination;

            //$response;
            $count = count($response['users']);

            $users = array_slice($response['users'],$offset,$pagination);

            $endCount = $offset + $pagination;
            $morePages = $count > $endCount;

            $results = array(
                "results" => $users,
                "pagination" => array(
                    "more" => $morePages
                )
            );

            return response()->json($results);
        }

        return $response;
    
    }
}
