<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CursoParticipante extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'cursos_participantes';

    protected $fillable = ['user_id', 'cvucv_user_id', 'cvucv_curso_id', 'cvucv_rol_id', 'user_sync', 'curso_sync'];

    public function cursos(){
        return $this->hasMany('App\Curso','categoria_id','id');
    }
}
