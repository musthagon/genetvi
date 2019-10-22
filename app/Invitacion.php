<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Invitacion extends Model
{
    protected $table = 'invitaciones';
    protected $fillable = ['id', 'token', 'estatus_invitacion_id', 'tipo_invitacion_id', 'instrumento_id', 'curso_id', 'periodo_lectivo_id', 'cvucv_user_id', 'usuario_id','numero_invitacion'];

    public function invitacionCompletada(){
        if($this->estatus_invitacion_id == 7){
            return true;
        }
        return false;
    }

    public function instrumento()    {
        return $this->belongsTo('App\Instrumento','instrumento_id','id');
    }

    public function curso()    {
        return $this->belongsTo('App\Curso','curso_id','id');
    }

    public function periodo()    {
        return $this->belongsTo('App\PeriodoLectivo','periodo_lectivo_id','id');
    }
    public function estatus_invitacion()    {
        return $this->belongsTo('App\EstatusInvitacion','estatus_invitacion_id','id');
    }
    public function tipo_invitacion()    {
        return $this->belongsTo('App\TipoInvitacion','tipo_invitacion_id','id');
    }

    public function user_profile(){
        return $this->cvucv_get_profile($this->cvucv_user_id);
    }

    public function invitacion_completada(){
        if ($this->estatus_invitacion_id == 7){
            return true;
        }
        return false;
    }

    public function invitacion_revocada(){
        if ($this->estatus_invitacion_id == 8){
            return true;
        }
        return false;
    }

    /**
     * CURL generíco usando GuzzleHTTP
     *
     */
    public function send_curl($request_type, $endpoint, $params){

        $client   = new \GuzzleHttp\Client();

        $response = $client->request($request_type, $endpoint, ['query' => $params ]);

        //$statusCode = $response->getStatusCode();

        $content    = json_decode($response->getBody(), true);

        return $content;
    }

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
}
