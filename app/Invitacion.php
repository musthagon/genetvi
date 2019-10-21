<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Invitacion extends Model
{
    protected $table = 'invitaciones';
    protected $fillable = ['id', 'token', 'estatus_invitacion_id', 'tipo_invitacion', 'instrumento_id', 'curso_id', 'periodo_lectivo_id', 'cvucv_user_id', 'usuario_id','numero_invitacion'];

    public function invitacionCompletada(){
        if($this->estatus_invitacion_id == 7){
            return true;
        }
        return false;
    }
}
