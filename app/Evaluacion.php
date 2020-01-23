<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Instrumento;
use App\Curso;
use App\CategoriaDeCurso;
use App\PeriodoLectivo;
use App\Categoria;
use App\Indicador;
use App\Invitacion;
use App\Respuesta;

class Evaluacion extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'evaluaciones';

    protected $fillable = ['id','respuestas','instrumento_id','curso_id','usuario_id','periodo_lectivo_id','percentil_eva','created_at','updated_at'];

    public function instrumento()    {
        return $this->belongsTo('App\Instrumento','instrumento_id','id');
    }

    public function curso()    {
        return $this->belongsTo('App\Curso','curso_id','id');
    }

    public function usuario()    {
        return $this->belongsTo('App\User','usuario_id','id');
    }

    public function respuestas_evaluacion(){
        return $this->hasMany('App\Respuesta','evaluacion_id','id');
    }

    public static function cantidad_evaluaciones_realizadas ($instrumento_id, $curso_id, $user_id, $categoria_raiz_periodo_lectivo){
        $intentos = Evaluacion::where('instrumento_id', $instrumento_id)
                            ->where('curso_id', $curso_id)
                            ->where('cvucv_user_id', $user_id)
                            ->where('periodo_lectivo_id', $categoria_raiz_periodo_lectivo)->count();
        return $intentos;
    } 

    public static function cantidad_respuestas_para_indicador($curso, $instrumento, $periodo, $indicador, $key){
        $valor = DB::table('evaluaciones')
            ->join('respuestas', 'evaluaciones.id', '=', 'respuestas.evaluacion_id')
            ->select('evaluaciones.*', 'respuestas.*')
            ->where('evaluaciones.curso_id',$curso->id)
            ->where('evaluaciones.instrumento_id',$instrumento->id)
            ->where('evaluaciones.periodo_lectivo_id',$periodo->id)
            ->where('respuestas.indicador_id',$indicador->id)
            ->where('respuestas.value_string',$key)
            ->count();
        return $valor;
    }
    public static function periodos_lectivos_de_evaluacion_del_curso($id){
        $periodos_curso = Evaluacion::where('curso_id', $id)->distinct('periodo_lectivo_id')->get(['periodo_lectivo_id']);
        $periodos_collection = [];
        foreach($periodos_curso as $periodo_index=>$periodo){
            $actual = PeriodoLectivo::find($periodo->periodo_lectivo_id);
            array_push($periodos_collection, $actual);
        }
        return $periodos_collection;
    }
    public static function usuarios_evaluadores_del_curso($curso_id, $periodo_lectivo_id, $instrumento_id){
        return Evaluacion::where('instrumento_id', $instrumento_id)
                            ->where('curso_id', $curso_id)
                            ->where('periodo_lectivo_id', $periodo_lectivo_id)
                            ->get(['cvucv_user_id']);
    }
    public static function evaluacion_de_usuario($curso_id, $periodo_lectivo_id, $instrumento_id){
        return Evaluacion::where('instrumento_id', $instrumento_id)
                            ->where('curso_id', $curso_id)
                            ->where('periodo_lectivo_id', $periodo_lectivo_id)
                            ->get(['cvucv_user_id']);
    }
    public static function buscar_evaluacion($curso_id, $periodo_lectivo_id, $instrumento_id, $usuario_id){
        return Evaluacion::where('instrumento_id', $instrumento_id)
                            ->where('curso_id', $curso_id)
                            ->where('periodo_lectivo_id', $periodo_lectivo_id)
                            ->where('cvucv_user_id', $usuario_id)
                            ->first();
    }
    public static function instrumentos_de_evaluacion_del_curso($id, &$instrumentos_collection, &$nombreInstrumentos, $anonimo = 0){
        $instrumentos_curso = Evaluacion::where('curso_id', $id)->distinct('instrumento_id')->get(['instrumento_id']);
        $instrumentos_collection = [];
        $nombreInstrumentos = [];
        foreach($instrumentos_curso as $instrumento_index=>$instrumento){
            $actual = Instrumento::find($instrumento->instrumento_id);
            if(empty($actual)){
                continue;
            }

            if ($anonimo == 0){
                $nombreInstrumentos[$instrumento_index] = $actual->getNombre();
                array_push($instrumentos_collection, $actual);
            }elseif(!$actual->getAnonimo()){
                $nombreInstrumentos[$instrumento_index] = $actual->getNombre();
                array_push($instrumentos_collection, $actual);
            }
            
        }
    }

    public static function categorias_de_evaluacion_del_curso($id){
        $categorias_collection = [];
        $categoriasDisponibles = DB::table('evaluaciones')
                ->join('respuestas', 'evaluaciones.id', '=', 'respuestas.evaluacion_id')
                ->select('respuestas.categoria_id')
                ->where('evaluaciones.curso_id',$id)
                ->distinct('categoria_id')
                ->get();

        foreach($categoriasDisponibles as $categoria){
            $actual = Categoria::find($categoria->categoria_id);
            array_push($categorias_collection, $actual);
        }
        return $categorias_collection;
    }
    public static function indicadores_de_evaluacion_del_curso($id){
        $indicadores_collection = [];
        $indicadoresDisponibles = DB::table('evaluaciones')
                ->join('respuestas', 'evaluaciones.id', '=', 'respuestas.evaluacion_id')
                ->select('respuestas.indicador_id')
                ->where('evaluaciones.curso_id',$id)
                ->distinct('indicador_id')
                ->get();
        foreach($indicadoresDisponibles as $indicador){
            $actual = Indicador::find($indicador->indicador_id);
            array_push($indicadores_collection, $actual);
        }
        return $indicadores_collection;
    }

    public static function respuestas_del_indicador($curso_id, $periodo_id, $instrumento_id, $categoria_id, $indicador_id){
        $valor = DB::table('evaluaciones')
            ->join('respuestas', 'evaluaciones.id', '=', 'respuestas.evaluacion_id')
            ->select('evaluaciones.*', 'respuestas.*')
            ->where('evaluaciones.curso_id',$curso_id)
            ->where('evaluaciones.instrumento_id',$instrumento_id)
            ->where('evaluaciones.periodo_lectivo_id',$periodo_id)
            ->where('respuestas.categoria_id',$categoria_id)
            ->where('respuestas.indicador_id',$indicador_id)
            ->get(['value_string']);
        return $valor;
    }
}
