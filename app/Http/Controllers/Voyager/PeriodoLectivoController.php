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
        $val = $this->validateBread($request->all(), $dataType->editRows, $dataType->name, $id)->validate();
        $this->insertUpdateData($request, $slug, $dataType->editRows, $data);

        //La descripción es requerida
        if(!isset($request->descripcion)){
            return redirect()->back()->with([
                'message'    => 'Error, el campo descripción del instrumento es requerido',
                'alert-type' => 'error',
            ]);
        }

        //Agregamos categorias
        $instrumento = $data;
        if(isset($request->categorias_list)){
            $categorias             = $request->categorias_list[0];
            $valores_porcentuales   = $request->categorias_list[1];
            $total = 0;
            //Verificamos que no esten repetidas las categorías
            //Verificamos que la suma total de los valores porcentuales de las categorías sea 100%

            foreach($categorias as $categoriaIndex => $categoria){

                foreach($categorias as $categoriaIndex2 => $categoria2){
                    if(($categoriaIndex != $categoriaIndex2) && $categoria == $categoria2){
                        return redirect()->back()->with([
                            'message'    => 'Error, las categorías no pueden estar repetidas',
                            'alert-type' => 'error',
                        ]);
                    }    
                }
                $total += (int)$valores_porcentuales[$categoriaIndex];
            }
            if($total != 100 && $total != 0){
                return redirect()->back()->with([
                    'message'    => 'Error, la suma de los valores porcentuales de las categorías debe ser 100% o 0%',
                    'alert-type' => 'error',
                ]);
            }
            
            $instrumento->categorias()->detach();
            foreach($categorias as $categoriaIndex => $categoria){
                $instrumento->categorias()->attach($categoria, ['valor_porcentual' => (int)$valores_porcentuales[$categoriaIndex]]);
            }

        }
        
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
        $val = $this->validateBread($request->all(), $dataType->addRows)->validate();
        $data = $this->insertUpdateData($request, $slug, $dataType->addRows, new $dataType->model_name());

        //La descripción es requerida
        /*if(!isset($request->descripcion)){
            return redirect()->back()->with([
                'message'    => 'Error, el campo descripción del instrumento es requerido',
                'alert-type' => 'error',
            ]);
        }*/

        //Agregamos categorias
        $periodo_lectivo = $data;
        if(isset($request->categorias_list)){
            $momentos               = $request->categorias_list[0];
            $fecha_inicio           = $request->categorias_list[1];
            $fecha_fin              = $request->categorias_list[2];
            $opciones               = $request->categorias_list[3];


            //Verificamos que no esten repetidas las categorías
            foreach($momentos as $momentoIndex => $momento){

                /*foreach($categorias as $categoriaIndex2 => $categoria2){
                    if(($categoriaIndex != $categoriaIndex2) && $categoria == $categoria2){
                        return redirect()->back()->with([
                            'message'    => 'Error, las categorías no pueden estar repetidas',
                            'alert-type' => 'error',
                        ]);
                    }    
                }*/
                //$total += (int)$valores_porcentuales[$categoriaIndex];
            }

            $periodo_lectivo->momentos_evaluacion()->detach();
            foreach($momentos as $momentoIndex => $momento){
                $periodo_lectivo->momentos_evaluacion()->attach($momento, [
                    PeriodoLectivoMomentoEvaluacion::get_fecha_inicio_field() => $fecha_inicio[$momentoIndex],
                    PeriodoLectivoMomentoEvaluacion::get_fecha_fin_field() => $fecha_fin[$momentoIndex],
                    PeriodoLectivoMomentoEvaluacion::get_opciones_field() => $opciones[$momentoIndex] ,
                    PeriodoLectivoMomentoEvaluacion::get_created_at_field() => \Carbon\Carbon::now() ,
                    PeriodoLectivoMomentoEvaluacion::get_updated_at_field() => \Carbon\Carbon::now() ]);
            }

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
