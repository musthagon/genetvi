<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;
use App\Curso;
use App\CursoParticipante;
use App\Evaluacion;
use App\Invitacion;
use App\User;
use App\Charts\indicadoresChart;

use App\Http\ChartsController;


use App\Traits\CommonFunctionsGenetvi; 
use App\Traits\CommonFunctionsCharts;

class HomeController extends Controller
{
    use CommonFunctionsGenetvi;
    use CommonFunctionsCharts;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){   
        
        $user                = Auth::user();
        $user_id             = $user->getCVUCV_USER_ID();
        $evaluacionesPendientes = Invitacion::invitaciones_pendientes_de_un_usuario($user_id);

        $informacion_pagina['titulo']       = "Bienvenido al Sistema";
        $informacion_pagina['descripcion']  = "El propósito de la aplicación es la gestión de la evaluación de los entornos virtuales de aprendizaje del CV-UCV";

        $esCoordinador = $this->buscarRol($this->permissionVer) ;

        return view('home.principal', compact('evaluacionesPendientes','informacion_pagina','esCoordinador'));
    }

    public function cursos(){   
        
        $user                = Auth::user();
        $user_id             = $user->getCVUCV_USER_ID();
        $cursosDocente       = CursoParticipante::cursosDocente($user_id);

        $informacion_pagina['titulo']       = "Cursos";
        $informacion_pagina['descripcion']  = "Aquí se muestran las acciones que puedes realizar en tus cursos";

        return view('home.mis_cursos', compact('cursosDocente','informacion_pagina'));
    }

    public function evaluaciones(){   
        
        $user                = Auth::user();
        $user_id             = $user->getCVUCV_USER_ID();
        $evaluacionesPendientes = Invitacion::invitaciones_pendientes_de_un_usuario($user_id);
        $evaluacionesRestantes  = Invitacion::invitaciones_restantes_de_un_usuario($user_id);
        $informacion_pagina['titulo']       = "Evaluaciones";
        $informacion_pagina['descripcion']  = "Aquí se muestran las evaluaciones de cursos que tienes pendientes";

        return view('home.mis_invitaciones_evaluar', compact('evaluacionesPendientes','evaluacionesRestantes','informacion_pagina'));
    }

    public function sincronizar_mis_cursos(){

        //Usamos el Trait para sincronizar con el campus
        $mis_cursos = $this->sync_user_courses();

        if(empty($mis_cursos)){
            return redirect()->route('mis_cursos')->with(['message' => "No se puede conectar con el Campus Virtual, intente más tarde", 'alert-type' => 'error']);
        }

        return redirect()->route('mis_cursos')->with(['message' => count($mis_cursos). " Cursos sincronizados", 'alert-type' => 'success']);
    }

    //Crea la vista del dashboard/graficos del curso
    public function visualizar_resultados_curso($curso_id){
        $curso = Curso::find($curso_id);
        
        if(empty($curso)){
            return redirect()->back()->with(['message' => "El curso no existe", 'alert-type' => 'error']);
        }

        //Tiene permitido acceder a la categoria?
        Gate::allows('tieneAccesoVisualizarCurso',[$curso]);
        
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
        
        //$momentos_evaluacion_collection = Evaluacion::momentos_de_evaluacion_del_curso($curso->getID());

        //$ruta_revisiones_publicas = 'mis_cursos.visualizar_resultados_curso.respuesta_publica';

        $informacion_pagina['titulo']       = "Estadísticas del Curso ".$curso->getNombre();
        $informacion_pagina['descripcion']  = "Aquí se muestran algunas estadísticas referente a la evaluación de este curso";

        return view('home.cursos_dashboards',
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
            'informacion_pagina'
        ));

    }

    public function visualizar_resultados_curso_respuesta_publica($categoria_id, $curso_id, Request $request){
        
        $curso = Curso::find($curso_id);
        
        if(empty($curso)){
            return redirect()->back()->with(['message' => "El curso no existe", 'alert-type' => 'error']);
        }

        //Tiene permitido acceder a la categoria?
        Gate::allows('tieneAccesoVisualizarCurso',[$curso]);
        
        $this->construir_resultados_curso_respuesta_publica(
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

        $usuario = $this->cvucv_get_profile( $usuario_id );

        $ruta_revisiones_publicas = 'mis_cursos.visualizar_resultados_curso.respuesta_publica';

        $informacion_pagina['titulo']       = "Respuesta Públicas a la Evaluación del Curso ".$curso->getNombre();
        $informacion_pagina['descripcion']  = "Aquí se muestran las respuestas públicas de los evaluadores";

        return view('home.cursos_evaluaciones_publicas',
        compact(
            'curso',
            'periodos_collection',
            'instrumentos_collection2',
            'evaluacion',
            'usuario',
            'periodo_lectivo',
            'instrumento',
            'ruta_revisiones_publicas',
            'momentos_evaluacion_collection',
            'informacion_pagina'
        ));
    }

}
