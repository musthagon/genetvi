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
        
        $momentos_evaluacion_collection = Evaluacion::momentos_de_evaluacion_del_curso($curso->getID());
        $ruta_revisiones_publicas = 'curso.visualizar_resultados_curso.respuesta_publica';

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
            'dashboards_subtitle',
            'ruta_revisiones_publicas',
            'momentos_evaluacion_collection'
        ));

    }
    public function visualizar_resultados_curso_respuesta_publica($categoria_id, $curso_id, Request $request){
        $curso = Curso::find($curso_id);
        
        if(empty($curso)){
            return redirect()->back()->with(['message' => "El curso no existe", 'alert-type' => 'error']);
        }

        //Tiene permitido acceder a la categoria?
        Gate::allows('checkAccess_ver',[$curso]);
        
        $status = $this->construir_resultados_curso_respuesta_publica(
            $categoria_id, 
            $curso_id, 
            $request,
            $curso,
            $periodos_collection,
            $instrumentos_collection2,
            $evaluacion,
            $usuario_id,
            $periodo_lectivo,
            $instrumento,
            $momentos_evaluacion_collection
        );
        
        if($status['status'] == "error"){
            return redirect()->back()->with(['message' => $status['message'], 'alert-type' => $status['alert-type']]);
        }

        $usuario = $this->cvucv_get_profile( $usuario_id );

        $ruta_revisiones_publicas = 'curso.visualizar_resultados_curso.respuesta_publica';

        return view('vendor.voyager.gestion.cursos_evaluaciones_publicas',
        compact(
            'curso',
            'periodos_collection',
            'instrumentos_collection2',
            'evaluacion',
            'usuario',
            'periodo_lectivo',
            'instrumento',
            'ruta_revisiones_publicas',
            'momentos_evaluacion_collection'
        ));
    }
    
}