<?php

namespace App\Http\Controllers\Voyager;

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

class PeriodoLectivoController extends VoyagerBaseController
{


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


        //Agregamos las categorias al instrumento
        $momentosAsociados = $dataTypeContent->momentos_evaluacion;

        $momentos = MomentosEvaluacion::all();

        return Voyager::view($view, compact(
            'dataType', 
            'dataTypeContent', 
            'isModelTranslatable', 
            'momentos',
            'momentosAsociados'));
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

        //Agregamos las categorias al instrumento
        $momentos = MomentosEvaluacion::all();

        return Voyager::view($view, compact(
            'dataType', 
            'dataTypeContent', 
            'isModelTranslatable',
            'momentos'));
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


        event(new BreadDataAdded($dataType, $data));

        return redirect()
        ->route("voyager.{$dataType->slug}.index")
        ->with([
                'message'    => __('voyager::generic.successfully_added_new')." {$dataType->getTranslatedAttribute('display_name_singular')}",
                'alert-type' => 'success',
            ]);
    }


}
