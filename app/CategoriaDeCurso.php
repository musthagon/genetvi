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

    protected $primaryKey = 'id';

    public $incrementing = false;

    protected $fillable = ['id', 'cvucv_parent_id', 'cvucv_name', 'cvucv_coursecount', 'cvucv_visible', 'cvucv_path', 'cvucv_link'];

    public function cursos(){
        return $this->hasMany('App\Curso','categoria_id','id');
    }
}
