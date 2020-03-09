<?php 

namespace App\Traits;

use Illuminate\Http\Request;
use Nahid\JsonQ\Jsonq;
use App\Curso;
use App\Evaluacion;
use App\CursoParticipante;
use Illuminate\Support\Facades\Auth;

trait CommonFunctionsGenetvi
{
    private $connection_error = 'connection_error';    
    private $CVUCV_GET_USER_TOKEN = '';
    private $CVUCV_GET_USER_TOKEN_SERVICE = '';
    private $CVUCV_GET_WEBSERVICE_ENDPOINT = '';
    private $CVUCV_ADMIN_TOKEN = '';
    private $CVUCV_ADMIN_TOKEN2 = '';

    protected $nombre_campo_rol_en_cvucv = "Cargo o Rol dentro de la Universidad *";
    protected $roles_que_pueden_accerder = array("Profesor UCV", "Administrativo UCV", "Profesor externo ");

    public  
        $permissionVer         = "ver_",
        $list_dependencias  = array(
                "facultad_de_arquitectura_y_urbanismo"          => "Facultad de Arquitectura y Urbanismo",
                "facultad_de_agronomia"                         => "Facultad de Agronomía",
                "facultad_de_ciencias"                          => "Facultad de Ciencias",
                "facultad_de_ciencias_juridicas_y_politicas"    => "Facultad de Ciencias Jurídicas y Políticas",
                "facultad_de_ciencias_economicas_y_sociales"    => "Facultad de Ciencias Económicas y Sociales",
                "facultad_de_farmacia"                          => "Facultad de Farmacia",
                "facultad_de_humanidades_y_educacion"           => "Facultad de Humanidades y Educación",
                "facultad_de_ingenieria"                        => "Facultad de Ingeniería",
                "facultad_de_medicina"                          => "Facultad de Medicina",
                "facultad_de_odontologia"                       => "Facultad de Odontología",
                "facultad_de_ciencias_veterinarias"             => "Facultad de Ciencias Veterinarias",
                "rectorado"                                     => "Rectorado",
                "vicerrectorado_academico"                      => "Vicerrectorado Académico",
                "vicerrectorado_administrativo"                 => "Vicerrectorado Administrativo",
                "secretaria"                                    => "Secretaría",
                "fundaciones_asociaciones"                      => "Fundaciones - Asociaciones",
                "otras_dependencias"                            => "Otras Dependencias",
            );
    /**
     * CURL generíco usando GuzzleHTTP
     *
     */
    public function send_curl($request_type, $endpoint, $params){
        if(empty($endpoint)){
            $error_message = "Error, variables de entorno sin definir. Por favor, comuníqueselo al administrador de la plataforma";
            $response = array($this->connection_error => $error_message);
            return $response;
        }

        try {
            $client   = new \GuzzleHttp\Client();

            $response = $client->request($request_type, $endpoint, ['query' => $params ]);

            $statusCode = $response->getStatusCode();

            if($statusCode == 200){

                $response = json_decode($response->getBody(), true);

            }else{
                throw new \Exception('Failed');

            }
        
        } catch (\GuzzleHttp\Exception\ConnectException $e) {
            //Catch the guzzle connection errors over here.These errors are something 
            // like the connection failed or some other network error  
            $error_message = $e->getMessage();

            $error_message = "Error, no se puede conectar con el Campus Virtual";

            $response = array($this->connection_error => $error_message);
        }

        return $response;
    }
    /**
     * Verifica si GuzzleHTTP generó algun error
     *
     */
    public function hasError($response){
        return isset($response[$this->connection_error]);
    }
    public function getErrorStatus(){
        return $this->connection_error;
    }

    /**
     * Parámetros para autenticación con el Campus Virtual
     *
     */
    public function cvucv_autenticacion(Request $request){
        $endpoint = env("CVUCV_GET_USER_TOKEN", $this->CVUCV_GET_USER_TOKEN);
        $service  = env("CVUCV_GET_USER_TOKEN_SERVICE", $this->CVUCV_GET_USER_TOKEN_SERVICE);
        
        $params = [
            'service'  => $service,
            'username' => $request->cvucv_username,
            'password' => $request->password
        ];

        $response = $this->send_curl('POST', $endpoint, $params);
        
        return $response;
    }

    /**
     * Obtiene los cursos de una categoria o
     * Obtiene los cursos por un campo
     */
    public function cvucv_get_category_courses($field,$value)    {
        $endpoint = env("CVUCV_GET_WEBSERVICE_ENDPOINT", $this->CVUCV_GET_WEBSERVICE_ENDPOINT);
        $wstoken  = env("CVUCV_ADMIN_TOKEN", $this->CVUCV_ADMIN_TOKEN);

        $params = [
            'wsfunction'            => 'core_course_get_courses_by_field',
            'wstoken'               => $wstoken,
            'moodlewsrestformat'    => 'json',
            'field'                 => $field,
            'value'                 => $value
        ];

        $response = $this->send_curl('POST', $endpoint, $params);
        
        if($this->hasError($response)){
            return [];
        }

        return $response['courses'];
    }
    /**
     * Obtiene los cursos en los que está matriculado un usuario
     *
     */
    public function cvucv_get_users_courses($user_id)    {
        $endpoint = env("CVUCV_GET_WEBSERVICE_ENDPOINT", $this->CVUCV_GET_WEBSERVICE_ENDPOINT);
        $wstoken  = env("CVUCV_ADMIN_TOKEN", $this->CVUCV_ADMIN_TOKEN);

        $params = [
            'wsfunction'            => 'core_enrol_get_users_courses',
            'wstoken'               => $wstoken,
            'moodlewsrestformat'    => 'json',
            'userid'                => $user_id
        ];

        $response = $this->send_curl('POST', $endpoint, $params);
        
        if($this->hasError($response)){
            return [];
        }

        return $response;
    }
    /**
     * Obtiene los participantes de un curso
     *
     */
    public function cvucv_get_participantes_curso($course_id)    {
        $endpoint = env("CVUCV_GET_WEBSERVICE_ENDPOINT", $this->CVUCV_GET_WEBSERVICE_ENDPOINT);
        $wstoken  = env("CVUCV_ADMIN_TOKEN", $this->CVUCV_ADMIN_TOKEN);

        $params = [
            'wsfunction'            => 'core_enrol_get_enrolled_users',
            'wstoken'               => $wstoken,
            'moodlewsrestformat'    => 'json',
            'courseid'              => $course_id
        ];

        $response = $this->send_curl('POST', $endpoint, $params);
        
        if($this->hasError($response)){
            return [];
        }

        return $response;
    }
    /**
     * Obtiene las categorias de los cursos
     *
     */
    public function cvucv_get_courses_categories($key = 'id', $value, $subcategories = 0){
        $endpoint = env("CVUCV_GET_WEBSERVICE_ENDPOINT", $this->CVUCV_GET_WEBSERVICE_ENDPOINT);
        $wstoken  = env("CVUCV_ADMIN_TOKEN", $this->CVUCV_ADMIN_TOKEN);

        $params = [
            'wsfunction'            => 'core_course_get_categories',
            'wstoken'               => $wstoken,
            'moodlewsrestformat'    => 'json',
            'criteria[0][key]'      => $key,
            'criteria[0][value]'    => $value,
            'addsubcategories'      => $subcategories
        ];

        $response = $this->send_curl('POST', $endpoint, $params);
        
        if($this->hasError($response)){
            return [];
        }

        return $response;
    }
    /**
     * Envia un mensaje instantaneo al usuario
     *
     */
    public function cvucv_send_instant_message($user_id, $message, $format)    {
        $endpoint = env("CVUCV_GET_WEBSERVICE_ENDPOINT", $this->CVUCV_GET_WEBSERVICE_ENDPOINT);
        $wstoken  = env("CVUCV_ADMIN_TOKEN", $this->CVUCV_ADMIN_TOKEN);

        $params = [
            'wsfunction'                => 'core_message_send_instant_messages',
            'wstoken'                   => $wstoken,
            'moodlewsrestformat'        => 'json',
            'messages[0][touserid]'     => $user_id,
            'messages[0][text]'         => $message,
            'messages[0][textformat]'   => $format,
        ];

        $response = $this->send_curl('POST', $endpoint, $params);
        
        if($this->hasError($response)){
            return [];
        }

        return $response;
    }
    /**
     * Perfil de usuario en el Campus
     *
     */
    public function cvucv_get_profile($cvucv_user_id, $field ='id')
    {
        $endpoint = env("CVUCV_GET_WEBSERVICE_ENDPOINT", $this->CVUCV_GET_WEBSERVICE_ENDPOINT);
        $wstoken  = env("CVUCV_ADMIN_TOKEN", $this->CVUCV_ADMIN_TOKEN);

        $params = [
            'wsfunction'            => 'core_user_get_users_by_field',
            'wstoken'               => $wstoken,
            'moodlewsrestformat'    => 'json',
            'field'                 => $field,
            'values[0]'             => $cvucv_user_id,
        ];

        $response = $this->send_curl('GET', $endpoint, $params);

        if($this->hasError($response)){
            return [];
        }
        return $response[0];
    }

    /**
     * Consulta los usuarios del Campus Virtual y configura la paginación
     * AJAX
     */
    public function campus_users(Request $request){

        $endpoint = env("CVUCV_GET_WEBSERVICE_ENDPOINT", $this->CVUCV_GET_WEBSERVICE_ENDPOINT);
        $wstoken  = env("CVUCV_ADMIN_TOKEN2", $this->CVUCV_ADMIN_TOKEN2);

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

        if($this->hasError($response)){
            return [];
        }

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
    /**
     * Consulta los usuarios del Campus Virtual por IDS y configura la paginación
     * AJAX
     */
    public function campus_users_by_ids(Request $request){

        $endpoint = env("CVUCV_GET_WEBSERVICE_ENDPOINT", $this->CVUCV_GET_WEBSERVICE_ENDPOINT);
        $wstoken  = env("CVUCV_ADMIN_TOKEN", $this->CVUCV_ADMIN_TOKEN);

        if(!isset($request->curso_id) || !isset($request->periodo_lectivo_id)  || !isset($request->instrumento_id) || !isset($request->momento_evaluacion_id)){
            return [];
        }
        
        $curso                  = Curso::find($request->curso_id);
        $periodo_lectivo        = $request->periodo_lectivo_id;
        $instrumento            = $request->instrumento_id;
        $momento_evaluacion     = $request->momento_evaluacion_id;

        if(empty($curso)){
            return [];
        }
        
        //instrumentos con los cuales han evaluado este curso
        //Evaluacion::instrumentos_de_evaluacion_del_curso($curso->id, $instrumentos_collection, $nombreInstrumentos, 1);

        $params = [
            'wsfunction'            => 'core_user_get_users_by_field',
            'wstoken'               => $wstoken,
            'moodlewsrestformat'    => 'json',
            'field'                 => "id",
        ];
        $cant_usuarios = 0;

        $usuarios = Evaluacion::usuarios_evaluadores_del_curso_en_un_momento($curso->getID(), $periodo_lectivo, $instrumento,$momento_evaluacion);
        foreach($usuarios as $usuario){
            $params['values['.$cant_usuarios.']'] = $usuario->cvucv_user_id;
            $cant_usuarios++;
        }

        if ($cant_usuarios <= 0){
            return [];
        }

        $response = $this->send_curl('POST', $endpoint, $params);

        if($this->hasError($response)){
            return [];
        }
        
        //Busqueda en el response por el valor del select2
        if(isset($request->lastname)){
            $json = new Jsonq();
            $response = $json->json(json_encode($response));
            $response = $json
            //->where('fullname', 'contains', $request->lastname)
            ->orWhere('fullname', 'contains', $request->lastname)
            ->orWhere('email', 'contains', $request->lastname)
            ->orWhere('username', 'contains', $request->lastname)
            ->get();
        }
        
        //Construimos la paginación
        if(isset($response) && isset($request->page)){
            $page = $request->page;
            $pagination = 20;

            $offset = ($page - 1) * $pagination;

            //$response;
            $count = count($response);

            $users = array_slice($response,$offset,$pagination);

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

        return [];
    
    }

    /**
     * Sincroniza todos los cursos del usuario logeado
     *
     */
    public function sync_user_courses(){
        $user = Auth::user();
        //Consultamos los cursos del usuario
        $cursos_cvucv = $this->cvucv_get_users_courses($user->getCVUCV_USER_ID());
        
        if(!empty($cursos_cvucv)){
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
                        }      
                        /*$matriculacion->curso_sync   = true;*/
                        $matriculacion->save();
                        break;
                    }
                }                     
            }
            
        }

        return $cursos_cvucv;
    }

    /**
     * Determine if the widget should be displayed.
     *
     * @return bool
     */
    public function buscarRol($permission){
        foreach($this->list_dependencias as $name=>$display_name){
            if( auth()->user()->hasPermission($permission.$name) ){
                return $display_name;
            }
        }
        return null;
    }
}