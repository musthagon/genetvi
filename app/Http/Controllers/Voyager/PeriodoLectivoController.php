<?php

namespace App\Http\Controllers\Voyager;

use App\CategoriaDeCurso;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use TCG\Voyager\Database\Schema\SchemaManager;
use TCG\Voyager\Events\BreadDataAdded;
use TCG\Voyager\Events\BreadDataUpdated;
use TCG\Voyager\Facades\Voyager;
use TCG\Voyager\Http\Controllers\VoyagerBaseController;
use App\PeriodoLectivo;
use App\PeriodoLectivoMomentoEvaluacion;
use App\MomentoEvaluacion;
use App\MomentosEvaluacion;
use Illuminate\Support\Facades\Validator;
use App\Traits\CommonFunctionsGenetvi; 
use App\Instrumento;

class PeriodoLectivoController extends VoyagerBaseController
{
    use CommonFunctionsGenetvi;

    //***************************************
    //               ____
    //              |  _ \
    //              | |_) |
    //              |  _ <
    //              | |_) |
    //              |____/
    //
    //      Browse our Data Type (B)READ
    //
    //****************************************

    public function index(Request $request)
    {
        // GET THE SLUG, ex. 'posts', 'pages', etc.
        $slug = $this->getSlug($request);

        // GET THE DataType based on the slug
        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        // Check permission
        $this->authorize('browse', app($dataType->model_name));

        $getter = $dataType->server_side ? 'paginate' : 'get';

        $search = (object) ['value' => $request->get('s'), 'key' => $request->get('key'), 'filter' => $request->get('filter')];

        $searchNames = [];
        if ($dataType->server_side) {
            $searchable = array_keys(SchemaManager::describeTable(app($dataType->model_name)->getTable())->toArray());
            $dataRow = Voyager::model('DataRow')->whereDataTypeId($dataType->id)->get();
            foreach ($searchable as $key => $value) {
                $displayName = $dataRow->where('field', $value)->first()->getTranslatedAttribute('display_name');
                $searchNames[$value] = $displayName ?: ucwords(str_replace('_', ' ', $value));
            }
        }

        $orderBy = $request->get('order_by', $dataType->order_column);
        $sortOrder = $request->get('sort_order', null);
        $usesSoftDeletes = false;
        $showSoftDeleted = false;
        $orderColumn = [];
        if ($orderBy) {
            $index = $dataType->browseRows->where('field', $orderBy)->keys()->first() + 1;
            $orderColumn = [[$index, 'desc']];
            if (!$sortOrder && isset($dataType->order_direction)) {
                $sortOrder = $dataType->order_direction;
                $orderColumn = [[$index, $dataType->order_direction]];
            } else {
                $orderColumn = [[$index, 'desc']];
            }
        }

        // Next Get or Paginate the actual content from the MODEL that corresponds to the slug DataType
        if (strlen($dataType->model_name) != 0) {
            $model = app($dataType->model_name);

            if ($dataType->scope && $dataType->scope != '' && method_exists($model, 'scope'.ucfirst($dataType->scope))) {
                $query = $model->{$dataType->scope}();
            } else {
                $query = $model::select('*');
            }

            // Use withTrashed() if model uses SoftDeletes and if toggle is selected
            if ($model && in_array(SoftDeletes::class, class_uses($model)) && app('VoyagerAuth')->user()->can('delete', app($dataType->model_name))) {
                $usesSoftDeletes = true;

                if ($request->get('showSoftDeleted')) {
                    $showSoftDeleted = true;
                    $query = $query->withTrashed();
                }
            }

            // If a column has a relationship associated with it, we do not want to show that field
            $this->removeRelationshipField($dataType, 'browse');

            if ($search->value != '' && $search->key && $search->filter) {
                $search_filter = ($search->filter == 'equals') ? '=' : 'LIKE';
                $search_value = ($search->filter == 'equals') ? $search->value : '%'.$search->value.'%';
                $query->where($search->key, $search_filter, $search_value);
            }

            if ($orderBy && in_array($orderBy, $dataType->fields())) {
                $querySortOrder = (!empty($sortOrder)) ? $sortOrder : 'desc';
                $dataTypeContent = call_user_func([
                    $query->orderBy($orderBy, $querySortOrder),
                    $getter,
                ]);
            } elseif ($model->timestamps) {
                $dataTypeContent = call_user_func([$query->latest($model::CREATED_AT), $getter]);
            } else {
                $dataTypeContent = call_user_func([$query->orderBy($model->getKeyName(), 'DESC'), $getter]);
            }

            // Replace relationships' keys for labels and create READ links if a slug is provided.
            $dataTypeContent = $this->resolveRelations($dataTypeContent, $dataType);
        } else {
            // If Model doesn't exist, get data from table name
            $dataTypeContent = call_user_func([DB::table($dataType->name), $getter]);
            $model = false;
        }

        // Check if BREAD is Translatable
        if (($isModelTranslatable = is_bread_translatable($model))) {
            $dataTypeContent->load('translations');
        }

        // Check if server side pagination is enabled
        $isServerSide = isset($dataType->server_side) && $dataType->server_side;

        // Check if a default search key is set
        $defaultSearchKey = $dataType->default_search_key ?? null;

        // Actions
        $actions = [];
        if (!empty($dataTypeContent->first())) {
            foreach (Voyager::actions() as $action) {
                $action = new $action($dataType, $dataTypeContent->first());

                if ($action->shouldActionDisplayOnDataType()) {
                    $actions[] = $action;
                }
            }
        }

        $view = 'voyager::bread.browse';

        if (view()->exists("voyager::$slug.browse")) {
            $view = "voyager::$slug.browse";
        }

        //Calculamos los periodos lectivos que puede mostrar para el coordinador actual
        
        //Buscamos las Categorias de Cursos sobre las cuales puede crear periodos lectivos
        if( !auth()->user()->hasRole('admin') ){
            $categoriasDeCurso = CategoriaDeCurso::CategoriaPorNombre($this->buscarRoles($this->permissionHabilitarEvaluacion));
            $dataTypeContent = PeriodoLectivo::getPeriodosLectivosDisponibles($dataTypeContent,$categoriasDeCurso);
        }

        return Voyager::view($view, compact(
            'actions',
            'dataType',
            'dataTypeContent',
            'isModelTranslatable',
            'search',
            'orderBy',
            'orderColumn',
            'sortOrder',
            'searchNames',
            'isServerSide',
            'defaultSearchKey',
            'usesSoftDeletes',
            'showSoftDeleted'
        ));
    }
    
    //***************************************
    //                ______
    //               |  ____|
    //               | |__
    //               |  __|
    //               | |____
    //               |______|
    //
    //  Edit an item of our Data Type BR(E)AD
    //
    //****************************************

    public function edit(Request $request, $id)
    {
        $slug = $this->getSlug($request);

        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        if (strlen($dataType->model_name) != 0) {
            $model = app($dataType->model_name);

            // Use withTrashed() if model uses SoftDeletes and if toggle is selected
            if ($model && in_array(SoftDeletes::class, class_uses($model))) {
                $model = $model->withTrashed();
            }
            if ($dataType->scope && $dataType->scope != '' && method_exists($model, 'scope'.ucfirst($dataType->scope))) {
                $model = $model->{$dataType->scope}();
            }
            $dataTypeContent = call_user_func([$model, 'findOrFail'], $id);
        } else {
            // If Model doest exist, get data from table name
            $dataTypeContent = DB::table($dataType->name)->where('id', $id)->first();
        }

        foreach ($dataType->editRows as $key => $row) {
            $dataType->editRows[$key]['col_width'] = isset($row->details->width) ? $row->details->width : 100;
        }

        // If a column has a relationship associated with it, we do not want to show that field
        $this->removeRelationshipField($dataType, 'edit');

        // Check permission
        $this->authorize('edit', $dataTypeContent);

        // Check if BREAD is Translatable
        $isModelTranslatable = is_bread_translatable($dataTypeContent);

        $view = 'voyager::bread.edit-add';

        if (view()->exists("voyager::$slug.edit-add")) {
            $view = "voyager::$slug.edit-add";
        }

        $periodo_lectivo = $dataTypeContent;

        $categoria = $periodo_lectivo->CategoriaDeCurso;

        //Agregamos las categorias al instrumento
        $momentosAsociados = $dataTypeContent->momentos_evaluacion;

        //Buscamos los momentos para mostrarlos en el listado
        $momentos = MomentosEvaluacion::all();

        //Buscamos las Categorias de Cursos sobre las cuales puede crear periodos lectivos
        $categoriasDeCurso = CategoriaDeCurso::CategoriaPorNombre($this->buscarRoles($this->permissionHabilitarEvaluacion));

        //Instrumentos
        $instrumentos       = Instrumento::instrumentosDisponibles();

        return Voyager::view($view, compact(
            'dataType', 
            'dataTypeContent', 
            'isModelTranslatable', 
            'momentos',
            'momentosAsociados',
            'categoriasDeCurso',
            'instrumentos',
            'categoria'));
    }

    // POST BR(E)AD
    public function update(Request $request, $id)
    {
        
        $slug = $this->getSlug($request);

        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        // Compatibility with Model binding.
        $id = $id instanceof \Illuminate\Database\Eloquent\Model ? $id->{$id->getKeyName()} : $id;

        $model = app($dataType->model_name);
        if ($dataType->scope && $dataType->scope != '' && method_exists($model, 'scope'.ucfirst($dataType->scope))) {
            $model = $model->{$dataType->scope}();
        }
        if ($model && in_array(SoftDeletes::class, class_uses($model))) {
            $data = $model->withTrashed()->findOrFail($id);
        } else {
            $data = call_user_func([$dataType->model_name, 'findOrFail'], $id);
        }

        // Check permission
        $this->authorize('edit', $data);
        
        // Validate fields with ajax
        $val = $this->validateBread($request->all(), $dataType->editRows, $dataType->name, $id);

        if ($val->fails()) {
            return redirect()->back()->withErrors($val)->with([
                'message'    => 'Error, algunos campos son requeridos',
                'alert-type' => 'error',
            ]); 
        }

    
        if(!isset($request->momento_evaluacion) 
        || !isset($request->momento_evaluacion[0]) 
        || !isset($request->momento_evaluacion[1]) 
        || !isset($request->momento_evaluacion[2])
        || !isset($request->fecha_inicio) 
        || !isset($request->fecha_fin)){
            return redirect()->back()->with([
                    'message'    => 'Error, debe agregar momentos de evaluación',
                    'alert-type' => 'error',
                ]);
        }

        $rules = [];

        for ($i = 0; $i <= 2; $i++){
            foreach($request['momento_evaluacion'][$i] as $key => $val){ 
                $rules['momento_evaluacion.'.$i.'.'.$key] = 'required';
            }
        }

        $val2 = Validator::make($request->all(), $rules);
        
        if ($val2->fails()) {
            return redirect()->back()->withErrors($val2)->with([
                'message'    => 'Error, algunos campos son requeridos',
                'alert-type' => 'error',
            ]); 
        }
        
        //Actualizamos el formato de las fechas
        $momentos = $request->momento_evaluacion;
        $momentosAux = []; 
        $momentosAux['momento_evaluacion'][0] = $momentos[0];
        foreach($momentos[1] as $index => $actual){
            $momentosAux['momento_evaluacion'][1][$index] = date("Y-m-d H:i:s", strtotime($actual));
        }
        foreach($momentos[2] as $index => $actual){
            $momentosAux['momento_evaluacion'][2][$index] = date("Y-m-d H:i:s", strtotime($actual));
        }  
        $momentosAux['momento_evaluacion'][3] = $momentos[3];  
        
        //Actualizamos el request
        $request->merge($momentosAux);
        $request->merge(["fecha_inicio" => date("Y-m-d H:i:s", strtotime($request->fecha_inicio))]);
        $request->merge(["fecha_fin"=> date("Y-m-d H:i:s", strtotime($request->fecha_fin))]);

        $momentos                     = $request->momento_evaluacion[0];
        $fecha_inicio                 = $request->momento_evaluacion[1];
        $fecha_fin                    = $request->momento_evaluacion[2];
        $opciones                     = $request->momento_evaluacion[3];
        $periodo_lectivo_fecha_inicio = $request->fecha_inicio;
        $periodo_lectivo_fecha_fin    = $request->fecha_fin;

        //Las fechas deben ser distintas
        if($periodo_lectivo_fecha_inicio == $periodo_lectivo_fecha_fin){
            return redirect()->back()->with([
                'message'    => 'Error, la fecha de inicio y la de fin del periodo lectivo deben ser distintas',
                'alert-type' => 'error',
            ]);
        }
        
        //Verificamos que no esten repetidas los momentos de evalución
        foreach($momentos as $momentosIndex => $momento){
            foreach($momentos as $momentosIndex2 => $momento2){
                if(($momentosIndex != $momentosIndex2) && $momento == $momento2){
                    return redirect()->back()->with([
                        'message'    => 'Error, los momentos de evaluación no pueden estar repetidos',
                        'alert-type' => 'error',
                    ]);
                }    
            }
        }

        //Verificamos la precedencia de las fechas
        if ($periodo_lectivo_fecha_inicio >= $periodo_lectivo_fecha_fin){
            return redirect()->back()->with([
                'message'    => 'Error, la fecha de fin debe ser posterior a la fecha de incio',
                'alert-type' => 'error',
            ]);
        }

        //Validación de cada unas de las Fechas de los momentos de evaluación
        $size = count($fecha_inicio);
        $j = 1;
        for($i = 0; $i < $size ; $i++, $j++){            
            if($fecha_inicio[$i] >= $fecha_fin[$i]){
                return redirect()->back()->with([
                    'message'    => 'Error, la fecha de fin debe ser posterior a la fecha de incio',
                    'alert-type' => 'error',
                ]);
            }
            if($j < $size && $fecha_fin[$i] >= $fecha_inicio[$j]){
                return redirect()->back()->with([
                    'message'    => 'Error, el primer rango de fecha del momento de evaluación debe ser anterior al siguiente',
                    'alert-type' => 'error',
                ]);
            }
            //Las fechas deben estar dentro del rango del periodo lectivo
            if($fecha_inicio[$i] < $periodo_lectivo_fecha_inicio ||  $fecha_fin[$i] > $periodo_lectivo_fecha_fin){
                return redirect()->back()->with([
                    'message'    => 'Error, la fechas del momento de evaluación deben de estar dentro del rango del periodo lectivo',
                    'alert-type' => 'error',
                ]);
            }
        }
            
        
        //Asociamos los momentos de evaluación
        $periodo_lectivo = $data;
        $periodo_lectivo->momentos_evaluacion()->detach();
        foreach($momentos as $momentoIndex => $momento){
            $periodo_lectivo->momentos_evaluacion()->attach($momento, [
                PeriodoLectivoMomentoEvaluacion::get_fecha_inicio_field() => $fecha_inicio[$momentoIndex],
                PeriodoLectivoMomentoEvaluacion::get_fecha_fin_field() => $fecha_fin[$momentoIndex],
                PeriodoLectivoMomentoEvaluacion::get_opciones_field() => $opciones[$momentoIndex] ,
                PeriodoLectivoMomentoEvaluacion::get_created_at_field() => \Carbon\Carbon::now() ,
                PeriodoLectivoMomentoEvaluacion::get_updated_at_field() => \Carbon\Carbon::now() ]);
        }
        
        if( isset($request->categoriaDeCurso) ){

            $categoria = CategoriaDeCurso::find($request->categoriaDeCurso);
        
            if(empty($categoria)){
                return redirect()->back()->with(['message' => "La facultad/dependencia no existe. Intente, sincronizarla o comuníquese con el administrador de la plataforma", 'alert-type' => 'error']);
            }
            if($categoria->cvucv_category_parent_id != 0){
                return redirect()->back()->with(['message' => "Error, la facultad/dependencia no corresponde a una categoría principal. Por favor, comuníqueselo con el administrador de la plataforma", 'alert-type' => 'error']);
            }
            
            //Colocar en CRON KERNEL *********************
            //Activamos el periodo lectivo
            if($categoria->getPeriodoLectivo() == null){
                $categoria->setPeriodoLectivo($id);
            }
            
            $categoria->instrumentos_habilitados()->detach();
            if (isset($request->instrumentos)){
                foreach($request->instrumentos as $instrumentoRequest){
                    $instrumento = Instrumento::find($instrumentoRequest);
                    if(empty($instrumento)){
                        return redirect()->back()->with(['message' => "El instrumento ya no existe, intente actualizar la página", 'alert-type' => 'error']);
                    }
                    $categoria->instrumentos_habilitados()->attach($instrumento);
                }
            }

            //Asociamos la categoria al periodo lectivo
            $periodo_lectivo->setCategoriaDeCurso($categoria->getID());

        }
        

        $this->insertUpdateData($request, $slug, $dataType->editRows, $data);
        
        event(new BreadDataUpdated($dataType, $data));

        return redirect()
        ->route("voyager.{$dataType->slug}.index")
        ->with([
            'message'    => __('voyager::generic.successfully_updated')." {$dataType->getTranslatedAttribute('display_name_singular')}",
            'alert-type' => 'success',
        ]);
    }

    //***************************************
    //
    //                   /\
    //                  /  \
    //                 / /\ \
    //                / ____ \
    //               /_/    \_\
    //
    //
    // Add a new item of our Data Type BRE(A)D
    //
    //****************************************

    public function create(Request $request)
    {
        $slug = $this->getSlug($request);

        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        // Check permission
        $this->authorize('add', app($dataType->model_name));

        $dataTypeContent = (strlen($dataType->model_name) != 0)
                            ? new $dataType->model_name()
                            : false;

        foreach ($dataType->addRows as $key => $row) {
            $dataType->addRows[$key]['col_width'] = $row->details->width ?? 100;
        }

        // If a column has a relationship associated with it, we do not want to show that field
        $this->removeRelationshipField($dataType, 'add');

        // Check if BREAD is Translatable
        $isModelTranslatable = is_bread_translatable($dataTypeContent);

        $view = 'voyager::bread.edit-add';

        if (view()->exists("voyager::$slug.edit-add")) {
            $view = "voyager::$slug.edit-add";
        }

        //Buscamos los momentos para mostrarlos en el listado
        $momentos = MomentosEvaluacion::all();

        //Buscamos las Categorias de Cursos sobre las cuales puede crear periodos lectivos
        $categoriasDeCurso = CategoriaDeCurso::CategoriaPorNombre($this->buscarRoles($this->permissionHabilitarEvaluacion));

        //Instrumentos
        $instrumentos = Instrumento::instrumentosDisponibles();

        return Voyager::view($view, compact(
            'dataType', 
            'dataTypeContent', 
            'isModelTranslatable',
            'momentos',
            'categoriasDeCurso',
            'instrumentos'));
    }

    /**
     * POST BRE(A)D - Store data.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $slug = $this->getSlug($request);

        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        // Check permission
        $this->authorize('add', app($dataType->model_name));

        // Validate fields with ajax
        $val = $this->validateBread($request->all(), $dataType->addRows);
        

        if ($val->fails()) {
            return redirect()->back()->withErrors($val)->with([
                'message'    => 'Error, algunos campos son requeridos',
                'alert-type' => 'error',
            ]); 
        }


        if(!isset($request->momento_evaluacion) 
        || !isset($request->momento_evaluacion[0]) 
        || !isset($request->momento_evaluacion[1]) 
        || !isset($request->momento_evaluacion[2])
        || !isset($request->fecha_inicio) 
        || !isset($request->fecha_fin)){
            return redirect()->back()->with([
                    'message'    => 'Error, debe agregar momentos de evaluación',
                    'alert-type' => 'error',
                ]);
        }

        $rules = [];

        for ($i = 0; $i <= 2; $i++){
            foreach($request['momento_evaluacion'][$i] as $key => $val){ 
                $rules['momento_evaluacion.'.$i.'.'.$key] = 'required';
            }
        }

        $val2 = Validator::make($request->all(), $rules);
        
        if ($val2->fails()) {
            return redirect()->back()->withErrors($val2)->with([
                'message'    => 'Error, algunos campos son requeridos',
                'alert-type' => 'error',
            ]); 
        }

        //Actualizamos el formato de las fechas
        $momentos = $request->momento_evaluacion;
        $momentosAux = []; 
        $momentosAux['momento_evaluacion'][0] = $momentos[0];
        foreach($momentos[1] as $index => $actual){
            $momentosAux['momento_evaluacion'][1][$index] = date("Y-m-d H:i:s", strtotime($actual));
        }
        foreach($momentos[2] as $index => $actual){
            $momentosAux['momento_evaluacion'][2][$index] = date("Y-m-d H:i:s", strtotime($actual));
        }  
        $momentosAux['momento_evaluacion'][3] = $momentos[3];  
        
        //Actualizamos el request
        $request->merge($momentosAux);
        $request->merge(["fecha_inicio" => date("Y-m-d H:i:s", strtotime($request->fecha_inicio))]);
        $request->merge(["fecha_fin"=> date("Y-m-d H:i:s", strtotime($request->fecha_fin))]);

        $momentos                     = $request->momento_evaluacion[0];
        $fecha_inicio                 = $request->momento_evaluacion[1];
        $fecha_fin                    = $request->momento_evaluacion[2];
        $opciones                     = $request->momento_evaluacion[3];
        $periodo_lectivo_fecha_inicio = $request->fecha_inicio;
        $periodo_lectivo_fecha_fin    = $request->fecha_fin;

        //Las fechas deben ser distintas
        if($periodo_lectivo_fecha_inicio == $periodo_lectivo_fecha_fin){
            return redirect()->back()->with([
                'message'    => 'Error, la fecha de inicio y la de fin del periodo lectivo deben ser distintas',
                'alert-type' => 'error',
            ]);
        }

        //Verificamos que no esten repetidas los momentos de evalución
        foreach($momentos as $momentosIndex => $momento){

            foreach($momentos as $momentosIndex2 => $momento2){
                if(($momentosIndex != $momentosIndex2) && $momento == $momento2){
                    return redirect()->back()->with([
                        'message'    => 'Error, los momentos de evaluación no pueden estar repetidos',
                        'alert-type' => 'error',
                    ]);
                }    
            }
        }

        //Verificamos la precedencia de las fechas
        if ($periodo_lectivo_fecha_inicio >= $periodo_lectivo_fecha_fin){
            return redirect()->back()->with([
                'message'    => 'Error, la fecha de fin debe ser posterior a la fecha de incio',
                'alert-type' => 'error',
            ]);
        }

        //Validación de cada unas de las Fechas de los momentos de evaluación
        $size = count($fecha_inicio);
        $j = 1;
        for($i = 0; $i < $size ; $i++, $j++){
            if($fecha_inicio[$i] >= $fecha_fin[$i]){
                return redirect()->back()->with([
                    'message'    => 'Error, la fecha de fin debe ser posterior a la fecha de incio',
                    'alert-type' => 'error',
                ]);
            }
            if($j < $size && $fecha_fin[$i] >= $fecha_inicio[$j]){
                return redirect()->back()->with([
                    'message'    => 'Error, el primer rango de fecha del momento de evaluación debe ser anterior al siguiente',
                    'alert-type' => 'error',
                ]);
            }
            //Las fechas deben estar dentro del rango del periodo lectivo
            if($fecha_inicio[$i] < $periodo_lectivo_fecha_inicio ||  $fecha_fin[$i] > $periodo_lectivo_fecha_fin){
                return redirect()->back()->with([
                    'message'    => 'Error, la fechas del momento de evaluación deben de estar dentro del rango del periodo lectivo',
                    'alert-type' => 'error',
                ]);
            }
        }
            
        //Agregamos el periodo lectivo
        $data = $this->insertUpdateData($request, $slug, $dataType->addRows, new $dataType->model_name());

        //Asociamos los momentos de evaluación
        $periodo_lectivo = $data;
        $periodo_lectivo->momentos_evaluacion()->detach();
        foreach($momentos as $momentoIndex => $momento){
            $periodo_lectivo->momentos_evaluacion()->attach($momento, [
                PeriodoLectivoMomentoEvaluacion::get_fecha_inicio_field() => $fecha_inicio[$momentoIndex],
                PeriodoLectivoMomentoEvaluacion::get_fecha_fin_field() => $fecha_fin[$momentoIndex],
                PeriodoLectivoMomentoEvaluacion::get_opciones_field() => $opciones[$momentoIndex] ,
                PeriodoLectivoMomentoEvaluacion::get_created_at_field() => \Carbon\Carbon::now() ,
                PeriodoLectivoMomentoEvaluacion::get_updated_at_field() => \Carbon\Carbon::now() ]);
        }



        if( isset($request->categoriaDeCurso) ){
            $categoria = CategoriaDeCurso::find($request->categoriaDeCurso);
        
            if(empty($categoria)){
                return redirect('admin/'.$slug.'/'.$periodo_lectivo->getID().'/edit')->with(['message' => "La facultad/dependencia no existe. Intente, sincronizarla o comuníquese con el administrador de la plataforma", 'alert-type' => 'error']);
            }
            if($categoria->cvucv_category_parent_id != 0){
                return redirect('admin/'.$slug.'/'.$periodo_lectivo->getID().'/edit')->with(['message' => "Error, la facultad/dependencia no corresponde a una categoría principal. Por favor, comuníqueselo con el administrador de la plataforma", 'alert-type' => 'error']);
            }
            
            //Colocar en CRON KERNEL *********************
            //Activamos el periodo lectivo
            if($categoria->getPeriodoLectivo() == null){
                $categoria->setPeriodoLectivo($periodo_lectivo->getID());
            }
            

            $categoria->instrumentos_habilitados()->detach();
            if (isset($request->instrumentos)){
                foreach($request->instrumentos as $instrumentoRequest){
                    $instrumento = Instrumento::find($instrumentoRequest);
                    if(empty($instrumento)){
                        return redirect('admin/'.$slug.'/'.$periodo_lectivo->getID().'/edit')->with(['message' => "El instrumento ya no existe, intente actualizar la página", 'alert-type' => 'error']);
                    }
                    $categoria->instrumentos_habilitados()->attach($instrumento);
                }
            }

            //Asociamos la categoria al periodo lectivo
            $periodo_lectivo->setCategoriaDeCurso($categoria->getID());

        }




        event(new BreadDataAdded($dataType, $data));

        return redirect()
        ->route("voyager.{$dataType->slug}.index")
        ->with([
                'message'    => __('voyager::generic.successfully_added_new')." {$dataType->getTranslatedAttribute('display_name_singular')}",
                'alert-type' => 'success',
            ]);
    }

    /**
     * Habilitar periodo lectivo
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return boolean
     */
    public function habilitar_periodo_lectivo(Request $request){
        $categoria = CategoriaDeCurso::find($request->categoria_id);

        if(empty($categoria)){
            return redirect()->back()->with(['message' => "La facultad/dependencia no existe. Intente, sincronizarla o comuníquese con el administrador de la plataforma", 'alert-type' => 'error']);
        }

        if($categoria->cvucv_category_parent_id != 0){
            return redirect()->back()->with(['message' => "Error, la facultad/dependencia no corresponde a una categoría principal. Por favor, comuníqueselo con el administrador de la plataforma", 'alert-type' => 'error']);
        }

        $categoriasDisponibles = $this->buscarRoles($this->permissionHabilitarEvaluacion);
        $tieneAcceso = false;
        foreach($categoriasDisponibles as $categoriaDisponible){
            if( $categoriaDisponible == $categoria->getNombre() ){
                $tieneAcceso = true;
                break;
            }
        }
        
        if(!$tieneAcceso){
            return redirect()->back()->with(['message' => "Error, no puede realizar esta acción", 'alert-type' => 'error']);
        }

        $periodo_lectivo = PeriodoLectivo::find($request->periodo_lectivo_id);

        if(empty($periodo_lectivo)){
            return redirect()->back()->with(['message' => "Error, el periodo léctivo no existe", 'alert-type' => 'error']);
        }

        $categoria->setPeriodoLectivo($periodo_lectivo->getID());

        return redirect()->back()->with(['message' => "Periodo lectivo activado", 'alert-type' => 'success']);
    }

    public function deshabilitar_periodo_lectivo(Request $request){
        $categoria = CategoriaDeCurso::find($request->categoria_id);
        
        if(empty($categoria)){
            return redirect()->back()->with(['message' => "La facultad/dependencia no existe. Intente, sincronizarla o comuníquese con el administrador de la plataforma", 'alert-type' => 'error']);
        }
        
        if($categoria->cvucv_category_parent_id != 0){
            return redirect()->back()->with(['message' => "Error, la facultad/dependencia no corresponde a una categoría principal. Por favor, comuníqueselo con el administrador de la plataforma", 'alert-type' => 'error']);
        }

        $categoriasDisponibles = $this->buscarRoles($this->permissionHabilitarEvaluacion);
        $tieneAcceso = false;
        foreach($categoriasDisponibles as $categoriaDisponible){
            if( $categoriaDisponible == $categoria->getNombre() ){
                $tieneAcceso = true;
                break;
            }
        }
        
        if(!$tieneAcceso){
            return redirect()->back()->with(['message' => "Error, no puede realizar esta acción", 'alert-type' => 'error']);
        }

        $periodo_lectivo = PeriodoLectivo::find($request->periodo_lectivo_id);

        if(empty($periodo_lectivo)){
            return redirect()->back()->with(['message' => "Error, el periodo léctivo no existe", 'alert-type' => 'error']);
        }

        $categoria->setPeriodoLectivo(null);

        return redirect()->back()->with(['message' => "Periodo lectivo deshabilitado", 'alert-type' => 'success']);
    }   
}
