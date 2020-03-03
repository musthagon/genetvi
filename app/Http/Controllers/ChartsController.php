<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use App\Instrumento;
use App\Curso;
use App\CategoriaDeCurso;
use App\CursoParticipante;
use App\Evaluacion;
use App\Respuesta;
use App\PeriodoLectivo;
use App\Categoria;
use App\Indicador;
use App\User;
use App\Charts\indicadoresChart;
use Yajra\Datatables\Datatables;

use App\Traits\CommonFunctionsGenetvi; 
use App\Traits\CommonFunctionsCharts;

class ChartsController extends Controller
{
    use CommonFunctionsGenetvi;
    use CommonFunctionsCharts;

    
    /*
    * Para visualizar los dashboards de la evaluacion de los cursos
    * Y lo relacionado a ese curso
    *
    */

    public function visualizar_resultados_curso($categoria_id, $curso_id){//Crea la vista del dashboard/graficos del curso
        $curso = Curso::find($curso_id);
        
        if(empty($curso)){
            return redirect()->back()->with(['message' => "El curso no existe", 'alert-type' => 'error']);
        }

        //Tiene permitido acceder a la categoria?
        Gate::allows('checkAccess_ver',[$curso]);

        $this->construirGraficosGeneralesCurso(
            $curso,
            $periodos_collection, 
            $instrumentos_collection, 
            $instrumentos_collection2,
            $categorias_collection, 
            $indicadores_collection,
            $indicadores_collection_charts,
            $cantidadEvaluacionesCursoCharts1,
            $cantidadEvaluacionesRechazadasCursoCharts1,
            $promedioPonderacionCurso1,
            $cantidadEvaluacionesCursoCharts2,
            $cantidadEvaluacionesRechazadasCursoCharts2,
            $promedioPonderacionCurso2,
            $dashboards_subtitle);
        
        return view('vendor.voyager.gestion.cursos_dashboards',
        compact(
            'curso',
            'periodos_collection',
            'instrumentos_collection',
            'instrumentos_collection2',
            'categorias_collection',
            'indicadores_collection',
            'indicadores_collection_charts',
            'cantidadEvaluacionesCursoCharts1',
            'cantidadEvaluacionesRechazadasCursoCharts1',
            'promedioPonderacionCurso1',
            'cantidadEvaluacionesCursoCharts2',
            'cantidadEvaluacionesRechazadasCursoCharts2',
            'promedioPonderacionCurso2',
            'dashboards_subtitle'
        ));

    }
    public function visualizar_resultados_curso_respuesta_publica($categoria_id, $curso_id, Request $request){
        

        $curso = Curso::find($curso_id);

        if(!isset($request->periodo_lectivo) || !isset($request->instrumento)  || !isset($request->user)){
            return redirect()->back()->with(['message' => "Faltan campos obligatorios", 'alert-type' => 'error']);
        }
        
        if(empty($curso)){
            return redirect()->back()->with(['message' => "El curso no existe", 'alert-type' => 'error']);
        }
        if(!$curso->es_categoria_del_curso($categoria_id)){
            return redirect()->back()->with(['message' => "La dependencia del curso es incorrecta", 'alert-type' => 'error']);
        }

        
        $periodo_lectivo_id = $request->periodo_lectivo;
        $instrumento_id     = $request->instrumento;
        $usuario_id         = $request->user;

        $periodo_lectivo = PeriodoLectivo::find($periodo_lectivo_id);
        $instrumento = Instrumento::find($instrumento_id);

        if(empty($periodo_lectivo)){
            return redirect()->back()->with(['message' => "El periodo lectivo no existe", 'alert-type' => 'error']);
        }

        if(empty($instrumento)){
            return redirect()->back()->with(['message' => "El instrumento no existe", 'alert-type' => 'error']);
        }

        if($instrumento->getAnonimo()){
            return redirect()->back()->with(['message' => "Las respuestas de este instrumento son anónimas", 'alert-type' => 'error']);
        }

        $evaluacion = Evaluacion::buscar_evaluacion($curso->id, $periodo_lectivo_id, $instrumento_id, $usuario_id);
        if(empty($evaluacion)){
            return redirect()->back()->with(['message' => "Error, este usuario no ha evaluado este curso", 'alert-type' => 'error']);
        }
        //instrumentos con los cuales han evaluado este curso2
        Evaluacion::instrumentos_de_evaluacion_del_curso($curso->id, $instrumentos_collection2, $nombreInstrumentos2, 1);
        
        //periodos lectivos con los cuales han evaluado este curso
        $periodos_collection        = Evaluacion::periodos_lectivos_de_evaluacion_del_curso($curso->id);

        $usuario = $this->cvucv_get_profile($usuario_id );

        return view('vendor.voyager.gestion.cursos_evaluaciones_publicas',
        compact(
            'curso',
            'periodos_collection',
            'instrumentos_collection2',
            'evaluacion',
            'usuario',
            'periodo_lectivo',
            'instrumento'
        ));
    }
    
}