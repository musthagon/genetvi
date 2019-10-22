<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Evaluacion extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'evaluaciones';

    protected $fillable = ['id','respuestas','instrumento_id','curso_id','usuario_id','periodo_lectivo_id','percentil_eva'];

    public function instrumento()    {
        return $this->belongsTo('App\Instrumento','instrumento_id','id');
    }

    public function curso()    {
        return $this->belongsTo('App\Curso','curso_id','id');
    }

    public function usuario()    {
        return $this->belongsTo('App\User','usuario_id','id');
    }

    public function respuestas(){
        return $this->hasMany('App\Respuesta','evaluacion_id','id');
    }

    public static function cantidad_evaluaciones_realizadas ($instrumento_id, $curso_id, $user_id, $categoria_raiz_periodo_lectivo){
        $intentos = Evaluacion::where('instrumento_id', $instrumento_id)
                            ->where('curso_id', $curso_id)
                            ->where('usuario_id', $user_id)
                            ->where('periodo_lectivo_id', $categoria_raiz_periodo_lectivo)->count();
        return $intentos;
    } 
}
