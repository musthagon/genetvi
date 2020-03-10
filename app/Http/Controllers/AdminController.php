<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Instrumento;
use App\Curso;
use App\CategoriaDeCurso;
use App\PeriodoLectivo;
use App\Evaluacion;
use App\Categoria;
use App\Indicador;
use App\Invitacion;
use App\TipoInvitacion;
use App\MomentosEvaluacion;
use App\Charts\indicadoresChart;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Yajra\Datatables\Datatables;

use App\Traits\CommonFunctionsGenetvi; 


class AdminController extends Controller
{
    use CommonFunctionsGenetvi;
    
    protected $permissionHabilitarEvaluacionCategoria   = "habilitar_evaluacion_";
    protected $permissionVerCategoria                   = "ver_";
    protected $permissionSincronizarCategoria           = "sincronizar_";
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(){
        //Voyager admin middleware
        $this->middleware('admin.user');
    }   

    /**
     * Para  Visualizar y sincronizar las categorias, cursos, participantes, roles
     *
     * 
     */
    public function gestion($id = 0){ 
        $wstoken  = env("CVUCV_ADMIN_TOKEN");
        $user = Auth::user();

        $informacion_pagina['descripcion']  = "En esta sección se puede navegar entre las categorías y cursos de la Facultad/Dependencia";
        $informacion_pagina['categorias']   = "Categorías";
        $informacion_pagina['cursos']       = "Cursos";


        if($id == 0){
            //Consultamos la api
            $categorias_padre = $this->cvucv_get_courses_categories('parent',0);

            $categorias = collect();
            foreach($categorias_padre as $data){
                if (Gate::allows('checkCategoryPermissionSisgeva', [$this->permissionVerCategoria,$data['name']]  )) {                  
                    $categorias[] = CategoriaDeCurso::create($data['id'],$data['parent'],$data['name'],$data['description'],$data['coursecount'],$data['visible'],$data['depth'],$data['path']);
                }
            }  
           
            //O si no, la BD
            if(empty($categorias_padre) || $categorias->isEmpty()){
                $categoriasDB = CategoriaDeCurso::where('cvucv_category_parent_id', $id)->get();
                foreach($categoriasDB as $categoria){
                    if (Gate::allows('checkCategoryPermissionSisgeva', [$this->permissionVerCategoria,$categoria->getCVUCV_NAME()]  )) {    
                        $categorias[] = $categoria;
                    }
                }
            }

            if(!empty($categorias)){
                $categorias = collect($categorias);
            }

            $informacion_pagina['categorias']   = "Facultades/Dependencias";

        }else{
            //Tienen acceso?
            $categoria = CategoriaDeCurso::where('id', $id)->first();
            if(!empty($categoria) ){
                if($categoria->cvucv_category_parent_id == 0){
                    $categoriaSuperPadre = $categoria;
                }else{
                    $categoriaSuperPadre = CategoriaDeCurso::where('id', $categoria->cvucv_category_super_parent_id)->first();
                }
                
                if (!empty($categoriaSuperPadre) && !Gate::allows('checkCategoryPermissionSisgeva', [$this->permissionVerCategoria,$categoriaSuperPadre->getCVUCV_NAME()]  )) {    
                    return redirect('/admin')->with(['message' => "Error, acceso no autorizado", 'alert-type' => 'error']);
                }

                $informacion_pagina['categorias']   = $categoria->getNombre();

            }

            $categorias = collect();
            $categoriasDB = CategoriaDeCurso::where('cvucv_category_parent_id', $id)->get();
            
            if(!$categoriasDB->isEmpty()){
                foreach($categoriasDB as $categoria){
                        $categorias[] = $categoria;

                }
                if(!empty($categorias)){
                    $categorias = collect($categorias);
                }
            }
            
            $cursos = Curso::where('cvucv_category_id', $id)->get();

            if(!$cursos->isEmpty()){
                return view('vendor.voyager.gestion.index',compact('categorias','cursos','wstoken','informacion_pagina'));
            }else{
                if($categorias->isEmpty()){
                    return redirect()->back()->with(['message' => "Categoría sin datos, intente sincronizarla", 'alert-type' => 'error']);
                }
            }
        }

        return view('vendor.voyager.gestion.index',compact('categorias','wstoken','informacion_pagina'));
    }
    public function gestion_cursos($id){
        $wstoken  = env("CVUCV_ADMIN_TOKEN");

        $cursos = Curso::where('cvucv_category_id', $id)->get();

        return view('vendor.voyager.gestion.index_courses',compact('cursos','wstoken'));
    }
    public function gestion_sincronizar($id, Request $request){
        //Tienen acceso?
        $categoria = CategoriaDeCurso::where('id', $id)->first();
        if(!empty($categoria) ){
            if($categoria->cvucv_category_parent_id == 0){
                $categoriaSuperPadre = $categoria;
            }else{
                $categoriaSuperPadre = CategoriaDeCurso::where('id', $categoria->cvucv_category_super_parent_id)->first();
            }
            
            if (!empty($categoriaSuperPadre) && !Gate::allows('checkCategoryPermissionSisgeva', [$this->permissionSincronizarCategoria,$categoriaSuperPadre->getCVUCV_NAME()]  )) {    
                return redirect('/admin')->with(['message' => "Error, acceso no autorizado", 'alert-type' => 'error']);
            }
        }
        
        if(isset($request->sync_courses)){
            //dd($request->sync_courses);
            return $this->gestion_sincronizar_cursos_categorias($id,$request);
        }
        return $this->gestion_sincronizar_categorias($id,$request);
    }
    public function gestion_sincronizar_categorias($id, Request $request){
        $categorias = $this->cvucv_get_courses_categories('id',$id,1);

        try {
            foreach($categorias as $categoria){

                $nueva_categoria = CategoriaDeCurso::find($categoria['id']);

                //1. Verificamos que existan los cursos
                //Si no existe, hay que crearlo
                if(empty($nueva_categoria)){
                    $nueva_categoria = new CategoriaDeCurso;
                }

                $nueva_categoria->id                         = $categoria['id'];
                $nueva_categoria->cvucv_category_parent_id   = $categoria['parent'];
                if(isset($request->categoria_raiz)){
                    $nueva_categoria->cvucv_category_super_parent_id   = $id;
                }
                $nueva_categoria->cvucv_name                 = $categoria['name'];
                $nueva_categoria->cvucv_description          = $categoria['description'];
                $nueva_categoria->cvucv_coursecount          = $categoria['coursecount'];
                $nueva_categoria->cvucv_visible              = $categoria['visible'];
                $nueva_categoria->cvucv_path                 = $categoria['path'];
                $nueva_categoria->cvucv_depth                = $categoria['depth'];
                $nueva_categoria->cvucv_visible              = $categoria['visible'];
                $nueva_categoria->cvucv_link                 = env("CVUCV_GET_SITE_URL","https://campusvirtual.ucv.ve")."/moodle/course/index.php?categoryid=".$categoria['id'];

                $nueva_categoria->save();

            }
            return redirect()->route('gestion.evaluaciones2', ['id' => $id])->with(['message' => "Datos sincronizados", 'alert-type' => 'success']);
        } catch (Exception $e) {
            return redirect()->route('gestion.evaluaciones2', ['id' => $id])->with($this->alertException($e, 'Error al sincronizar'));
        }
    }   
    public function gestion_sincronizar_cursos_categorias($id,$request){
        
        try {
            //Cursos -> matriculaciones -> roles
            $cursos_de_la_categoria = $this->cvucv_get_category_courses('category',$id);

            foreach($cursos_de_la_categoria as $data){

                $curso = Curso::find($data['id']);

                //1. Verificamos que existan los cursos
                //Si no existe, hay que crearlo
                if(empty($curso)){
                    $curso = new Curso;
                }
                $curso->id                  = $data['id'];
                $curso->cvucv_shortname     = $data['shortname'];
                $curso->cvucv_category_id   = $data['categoryid'];
                $curso->cvucv_fullname      = $data['fullname'];
                $curso->cvucv_displayname   = $data['displayname'];
                $curso->cvucv_summary       = $data['summary'];
                $curso->cvucv_visible       = $data['visible'];
                $curso->cvucv_link          = env("CVUCV_GET_SITE_URL","https://campusvirtual.ucv.ve")."/course/view.php?id=".$data['id'];

                $curso->save();
                
                /*
                * NO SINCRONIZAR PARTICIPANTES AHORA
                *
                $participantes_curso = $this->cvucv_get_participantes_curso($data['id']);

                //Sincronizamos sus participantes
                foreach($participantes_curso as $participante){
                
                    //2. Verificamos que este matriculado en ese curso
                    $matriculacion = CursoParticipante::where('cvucv_user_id', $participante['id'])
                        ->where('cvucv_curso_id', $data['id'])
                        ->first();
                    //Si no esta, hay que matricularlo
                    if(empty($matriculacion)){
                        $matriculacion                 = new CursoParticipante;
                        $matriculacion->cvucv_user_id  = $participante['id'];
                        $matriculacion->cvucv_curso_id = $data['id'];
                        $matriculacion->user_sync      = false;
                    }
                    $matriculacion->cvucv_rol_id = $participante['roles'][0]['roleid'];
                    $matriculacion->curso_sync   = true;
                    $matriculacion->save();

                }*/

            }              
        } catch (Exception $e) {
            return redirect()->route('gestion.evaluaciones2', ['id' => $id])->with($this->alertException($e, 'Error al sincronizar cursos'));
        }

        return $this->gestion_sincronizar_categorias($id,$request);
    }

    /*
    * Para gestionar la evaluacion
    *
    */
    public function verificarCurso($id){
        $curso = Curso::find($id);
        
        if(empty($curso)){
            return redirect()->back()->with(['message' => "El curso no existe", 'alert-type' => 'error']);
        }

        return $curso;
    }
    public function iniciar_evaluacion_curso($id){        
        
        $curso = $this->verificarCurso($id);

        Gate::allows('checkAccess_HabilitarEvaluacion',[$curso]);

        $categoria_raiz             = $curso->categoria->categoria_raiz;
        $instrumentos_habilitados   = $categoria_raiz->instrumentos_habilitados;
        $periodo_lectivo            = $categoria_raiz->periodo_lectivo_actual;

        if($instrumentos_habilitados->isEmpty() || ($periodo_lectivo===NULL)){
            return redirect()->back()->with(['message' => "No está habilitada la evaluación para esta facultad/centro o dependencia", 'alert-type' => 'error']);
        }
        
        $momento_evaluacion_activo = $periodo_lectivo->momento_evaluacion_actual;

        //1. Verificamos a quienes no se les ha enviado invitacion a este momento

        if($momento_evaluacion_activo!=NULL){
            $curso->verificarInvitacionesAlMomentoActual($instrumentos_habilitados, $periodo_lectivo, $momento_evaluacion_activo);
        }

        //Actualizamos el atributo
        $curso->actualizarEvaluacion(true);
        
        return redirect()->back()->with(['message' => "Evaluación activada", 'alert-type' => 'success']);
    }
    public function cerrar_evaluacion_curso($id){
        $curso = Curso::find($id);
        
        if(empty($curso)){
            return redirect()->back()->with(['message' => "El curso no existe", 'alert-type' => 'error']);
        }

        //Actualizamos el atributo
        $curso->actualizarEvaluacion(false);

        return redirect()->back()->with(['message' => "Evaluación cerrada", 'alert-type' => 'warning']);
    }
    public function estatus_evaluacion_curso($categoria_id, $curso_id){
        $curso = Curso::find($curso_id);
        
        if(empty($curso)){
            return redirect()->back()->with(['message' => "El curso no existe", 'alert-type' => 'error']);
        }
        
        //Invitaciones para el periodo lectivo actual....
        $periodo_lectivo_actual = $curso->periodo_lectivo_actual();
        if(empty($periodo_lectivo_actual)){
            return redirect()->back()->with(['message' => "Error, no se encuentra configurado el periodo lectivo actual", 'alert-type' => 'error']);
        }

        //Invitaciones para el periodo lectivo actual....
        $momentos_evaluacion = $periodo_lectivo_actual->momentos_evaluacion;
        /*if(empty($momentos_evaluacion)){
            return redirect()->back()->with(['message' => "Error, no se encuentra configurado ningún momento de evaluación para el periodo lectivo actual", 'alert-type' => 'error']);
        }*/

        $invitaciones_curso = Invitacion::where('curso_id',$curso->id)->where('periodo_lectivo_id',$periodo_lectivo_actual->id)->get();

        //Buscamos los participantes
        //$participantes = $this->cvucv_get_participantes_curso($curso->id);

        //Buscamos
        $revisores = [];
        foreach($invitaciones_curso as $invitacion_index => $invitacion){
            $revisores[$invitacion_index] = $invitacion->user_profile();
        }

        //Instrumentos de matriculacion manuak
        $categoria_raiz             = $curso->categoria->categoria_raiz;
        $instrumentos_habilitados   = $categoria_raiz->instrumentos_habilitados;

        $instrumentos_manuales = [];
        foreach($instrumentos_habilitados as $instrumento){
            if(!$instrumento->getInvitacionAutomatica()){ //Instrumentos de matriculacion manual
                array_push($instrumentos_manuales, $instrumento);
            }
            
        }

        return view('vendor.voyager.gestion.cursos_estatus_evaluacion',
        compact(
            'curso',
            'periodo_lectivo_actual',
            'momentos_evaluacion',
            'invitaciones_curso',
            'revisores',
            'instrumentos_manuales'
        ));
    }
    public function enviar_recordatorio($id_curso, $invitacion){        
        $curso = Curso::find($id_curso);
        
        if(empty($curso)){
            return redirect()->back()->with(['message' => "El curso no existe", 'alert-type' => 'error']);
        }

        //Verificamos que tenga invitación previa
        $invitacionAnterior = Invitacion::find($invitacion);

        //Si no tiene invitación
        if(empty($invitacionAnterior)){
            return redirect()->back()->with(['message' => "La invitación no existe", 'alert-type' => 'error']);
        }

        //Enviamos la invitacion
        $message =  Invitacion::messageTemplate($invitacionAnterior->user_profile(), $curso, $invitacionAnterior->token);
        $response = $this->cvucv_send_instant_message($invitacionAnterior->cvucv_user_id, $message, 1);

        if(!Invitacion::confirmarMensaje($response)){
            return redirect()->back()->with(['message' => "Error para enviar recordatorio", 'alert-type' => 'error']);
        }

        //Actualizamos
        $invitacionAnterior->actualizar_estatus_recordatorio_enviado();
        
        return redirect()->back()->with(['message' => "Recordatorio enviado", 'alert-type' => 'success']);
    }
    public function revocar_invitacion($id_curso, $invitacion){        
        $curso = Curso::find($id_curso);
        
        if(empty($curso)){
            return redirect()->back()->with(['message' => "El curso no existe", 'alert-type' => 'error']);
        }

        //Verificamos que tenga invitación previa
        $invitacionAnterior = Invitacion::find($invitacion);

        //Si no tiene invitación
        if(empty($invitacionAnterior)){
            return redirect()->back()->with(['message' => "La invitación no existe", 'alert-type' => 'error']);
        }

        //Actualizamos
        $invitacionAnterior->estatus_invitacion_id = 8;
        $invitacionAnterior->cantidad_recordatorios = 0;
        $invitacionAnterior->save();
        
        return redirect()->back()->with(['message' => "Invitación a evaluar revocada", 'alert-type' => 'success']);
    }
    public function invitar_evaluacion_curso($id, Request $request){
        $momentos_evaluacion         = $request->momentos_evaluacion;
        $instrumentos                = $request->instrumentos_manuales;
        $usuarios                    = $request->users;
        $total_invitacion_previas    = 0 ;
        $total_invitacion            = 0 ;

        if(!isset($usuarios)){
            return redirect()->back()->with(['message' => "No ha seleccionado ningún usuario", 'alert-type' => 'error']);
        }

        if(!isset($momentos_evaluacion)){
            return redirect()->back()->with(['message' => "Debe ingresar al menos un momento de evaluación", 'alert-type' => 'error']);
        }

        if(!isset($instrumentos)){
            return redirect()->back()->with(['message' => "No ha seleccionado ningún instrumento", 'alert-type' => 'error']);
        }

        $curso = Curso::find($id);
        
        if(empty($curso)){
            return redirect()->back()->with(['message' => "El curso no existe", 'alert-type' => 'error']);
        }
        
        foreach($instrumentos as $instrumentoIndex => $instrumento){

            $instrumentoActual = Instrumento::find($instrumento);
            if(empty($instrumentoActual)){
                return redirect()->back()->with(['message' => "Uno de los instrumentos seleccionados no existe", 'alert-type' => 'error']);
            }
            if($instrumentoActual->getInvitacionAutomatica()){
                return redirect()->back()->with(['message' => "El instrumento: ".$instrumentoActual->nombre." no permite la invitación manual", 'alert-type' => 'error']);
            }

            foreach($momentos_evaluacion as $momentoIndex => $momento){
                
                $momentoActual = MomentosEvaluacion::find($momento);
                
                if(empty($momentoActual)){
                    return redirect()->back()->with(['message' => "Uno de los momentos de evaluación seleccionados no existe", 'alert-type' => 'error']);
                }
                foreach($usuarios as $usuarioIndex => $usuario){

                    $periodo_lectivo = $curso->periodo_lectivo_actual();
                    $momento_evaluacion_activo = $periodo_lectivo->momento_evaluacion_actual;
    
                    if(!Invitacion::invitacionPrevia($curso->getID(), $instrumentoActual->getID(), $periodo_lectivo->getID(), $momentoActual->getID(), $usuario) ){
    
                        //se crea la invitacion
                        Invitacion::invitarEvaluador($curso->getID(), $instrumentoActual->getID(), $periodo_lectivo->getID(), $momentoActual->getID(), $usuario, TipoInvitacion::getEstatusManual());
                        
                        $total_invitacion++;
                    }else{
                        $total_invitacion_previas++;
                    }
    
                }
            }
            
        }

        if($total_invitacion_previas > 0){
            return redirect()->back()->with(['message' => $total_invitacion_previas." Usuarios con invitaciones previas y ".$total_invitacion." Usuarios invitados", 'alert-type' => 'warning']);
        }else{
            return redirect()->back()->with(['message' => $total_invitacion." Usuarios invitados", 'alert-type' => 'success']);
        }
    }
    
}
