<?php 

namespace App\Traits;

use Illuminate\Http\Request;
use Nahid\JsonQ\Jsonq;
use App\Curso;
use App\Evaluacion;

trait CommonFunctionsGenetvi
{
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
     * Perfil de usuario en el Campus
     *
     */
    public function cvucv_get_profile($cvucv_user_id)
    {
        $endpoint = env("CVUCV_GET_WEBSERVICE_ENDPOINT");
        $wstoken  = env("CVUCV_ADMIN_TOKEN");

        $params = [
            'wsfunction'            => 'core_user_get_users_by_field',
            'wstoken'               => $wstoken,
            'moodlewsrestformat'    => 'json',
            'field'                 => 'id',
            'values[0]'             => $cvucv_user_id,
        ];

        $response = $this->send_curl('GET', $endpoint, $params);
        
        return $response[0];
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
    /**
     * Consulta los usuarios del Campus Virtual por IDS y configura la paginación
     *
     */
    public function campus_users_by_ids(Request $request){

        $endpoint = env("CVUCV_GET_WEBSERVICE_ENDPOINT","https://campusvirtual.ucv.ve/moodle/webservice/rest/server.php");
        $wstoken  = env("CVUCV_ADMIN_TOKEN");

        if(!isset($request->curso_id) || !isset($request->periodo_lectivo_id)  || !isset($request->instrumento_id)){
            return [];
        }
        
        $curso           = Curso::find($request->curso_id);
        $periodo_lectivo = $request->periodo_lectivo_id;
        $instrumento     = $request->instrumento_id;

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

        $usuarios = Evaluacion::usuarios_evaluadores_del_curso($curso->id, $periodo_lectivo, $instrumento);
        foreach($usuarios as $usuario){
            $params['values['.$cant_usuarios.']'] = $usuario->cvucv_user_id;
            $cant_usuarios++;
        }

        if ($cant_usuarios <= 0){
            return [];
        }

        $response = $this->send_curl('POST', $endpoint, $params);

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

        return $response;
    
    }
}