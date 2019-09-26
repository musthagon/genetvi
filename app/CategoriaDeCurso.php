<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CategoriaDeCurso extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'categorias_cursos';

    public function cursos(){
        return $this->hasMany('App\Curso','categoria_id','id');
    }
}
