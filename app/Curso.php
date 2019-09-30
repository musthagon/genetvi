<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Curso extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'cursos';

    protected $primaryKey = 'id';

    public $incrementing = false;

    protected $fillable = ['id', 'cvucv_shortname', 'cvucv_category_id', 'cvucv_fullname', 'cvucv_displayname', 'cvucv_summary', 'cvucv_visible', 'cvucv_link', 'cvucv_participantes'];

    public static function existe_curso($cvucv_course_id){
        return Curso::query()
            ->where('id', $cvucv_course_id)
            ->first();
    }

    /*public function store()
    {
        // Validate the request...

        $curso = new Curso;

        $curso->name = $request->name;
        $curso->name = $request->name;
        $curso->name = $request->name;
        $curso->name = $request->name;
        $curso->name = $request->name;
        $curso->name = $request->name;
        $curso->name = $request->name;
        $curso->name = $request->name;
        $curso->name = $request->name;
        $curso->name = $request->name;

        $curso->save();
    }*/

    /*public function evaluaciones(){
        return $this->hasMany('App\Evaluacion','curso_id','id');
    }

    public function categoria(){
        return $this->belongsTo('App\CategoriaDeCurso','categoria_id','id');
    }

    public function periodos_lectivos(){
        return $this->belongsToMany('App\PeriodoLectivo','curso_periodos_lectivos','curso_id','periodos_lectivo_id')->using('App\CursoPeriodoLectivo');
    }*/
}
