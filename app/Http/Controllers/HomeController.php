<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Instrumento;
use App\Curso;
use App\CategoriaDeCurso;
use App\CursoParticipante;
use Illuminate\Support\Facades\Auth;

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
        //Buscamos los instrumentos a desplegar
        $instrumento = Instrumento::find(1);

        //Verificar sincronización de datos del usuario
        $cursos = $this->sync_user_courses();

        return view('user.panel', compact('instrumento','cursos'));
    }

    /**
     * Para sincronizar las categorias
     *
     * 
     */
    public function gestion($id = 0){ 
        $wstoken  = env("CVUCV_ADMIN_TOKEN");

        if($id == 0){

            $categorias_padre = $this->cvucv_get_courses_categories('parent',0);

            return view('vendor.voyager.gestion.index',compact('categorias_padre','wstoken'));
        }else{

            $categorias = CategoriaDeCurso::where('cvucv_category_parent_id', $id)->get();

            return view('vendor.voyager.gestion.index',compact('categorias','wstoken'));
        }
        
    }

    public function gestion_sincronizar_categorias($id){
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
                $nueva_categoria->cvucv_name                 = $categoria['name'];
                $nueva_categoria->cvucv_coursecount          = $categoria['coursecount'];
                $nueva_categoria->cvucv_visible              = $categoria['visible'];
                $nueva_categoria->cvucv_path                 = $categoria['path'];
                $nueva_categoria->cvucv_visible              = $categoria['visible'];
                $nueva_categoria->cvucv_link                 = env("CVUCV_GET_SITE_URL")."/moodle/course/index.php?categoryid=".$categoria['id'];

                $nueva_categoria->save();

                /*
                //Cursso -> matriculaciones -> roles
                $cursos_de_la_categoria = $this->cvucv_get_category_courses('category',$categoria['id']);

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

                    foreach($participantes_curso as $participante){
                    


                        //2. Verificamos que este matriculado en ese curso
                        $matriculacion = CursoParticipante::where('cvucv_user_id', $participante['id'])
                            ->where('cvucv_curso_id', $data['id'])
                            ->first();
                        //Si no esta, hay que matricularlo
                        if(empty($matriculacion)){

                            $matriculacion = new CursoParticipante;

                            $matriculacion->cvucv_user_id  = $participante['id'];
                            $matriculacion->cvucv_curso_id = $data['id'];
                            $matriculacion->user_sync      = false;
                            $matriculacion->curso_sync     = true;

                            $matriculacion->save();

                        }else{
                            //Ya esta syncronizada su data
                            if(!$matriculacion->curso_sync){
                                $matriculacion->curso_sync   = true;
                                $matriculacion->save();
                            }
                        }


                    }

                }
                */
            }
            return redirect()->route('gestion.evaluaciones')->with(['message' => "Datos sincronizados", 'alert-type' => 'success']);
        } catch (Exception $e) {
            return redirect()->route('gestion.evaluaciones')->with($this->alertException($e, 'Error al sincronizar'));
        }
        
    }

    /**
     * Para procesar la evaluación de los instrumentos
     *
     * 
     */
    public function evaluacion(Request $request){
        dd($request);
    }

    public function sync_user_courses(){
        $user = Auth::user();

        $cursos_usuario = $this->cvucv_get_users_courses($user->cvucv_id);
        //$cursos_usuario = $this->cvucv_get_users_courses(0);

        if(!empty($cursos_usuario)){

            $cursos = [];
            foreach($cursos_usuario as $data){

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
                    $curso->cvucv_link          = env("CVUCV_GET_SITE_URL")."/course/view.php?id=".$data['id'];

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

                array_push($cursos, $curso);
            }
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
    public function cvucv_get_category_courses($field,$value)
    {
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
    public function cvucv_get_users_courses($user_id)
    {
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
    public function cvucv_get_participantes_curso($course_id)
    {
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
    public function cvucv_get_courses_categories($key = 'id', $value, $subcategories = 0)
    {
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
