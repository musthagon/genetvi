<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use TCG\Voyager\Database\Schema\SchemaManager;
use TCG\Voyager\Events\BreadDataAdded;
use TCG\Voyager\Events\BreadDataUpdated;
use TCG\Voyager\Facades\Voyager;
use TCG\Voyager\Http\Controllers\Traits\BreadRelationshipParser;
use TCG\Voyager\Http\Controllers\VoyagerBaseController;
use App\Indicador;
use App\Categoria;
class CategoriaController extends VoyagerBaseController
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
        $indicadoresAsociados = $dataTypeContent->indicadores;

        $indicadores = Indicador::all();

        return Voyager::view($view, compact(
            'dataType', 
            'dataTypeContent', 
            'isModelTranslatable', 
            'indicadores',
            'indicadoresAsociados'));
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
        /*if(!isset($request->descripcion)){
            return redirect()->back()->with([
                'message'    => 'Error, el campo descripción del instrumento es requerido',
                'alert-type' => 'error',
            ]);
        }*/

        //Agregamos indicadores
        $categoria = $data;
        if(isset($request->categorias_list)){
            $indicadores            = $request->categorias_list[0];
            $valores_porcentuales   = $request->categorias_list[1];
            $total = 0;
            //Verificamos que no esten repetidas los indicadores
            //Verificamos que la suma total de los valores porcentuales de las categorías sea 100%

            foreach($indicadores as $indicadorIndex => $indicador){

                foreach($indicadores as $indicadorIndex2 => $indicador2){
                    if(($indicadorIndex != $indicadorIndex2) && $indicador == $indicador2){
                        return redirect()->back()->with([
                            'message'    => 'Error, los indicadores no pueden estar repetidas',
                            'alert-type' => 'error',
                        ]);
                    }    
                }
                $total += (int)$valores_porcentuales[$indicadorIndex];
            }
            if($total != 100 && $total != 0){
                return redirect()->back()->with([
                    'message'    => 'Error, la suma de los valores porcentuales de los indicadores debe ser 100%',
                    'alert-type' => 'error',
                ]);
            }
            
            $categoria->indicadores()->detach();
            foreach($indicadores as $indicadorIndex => $indicador){
                $categoria->indicadores()->attach($indicador, ['valor_porcentual' => (int)$valores_porcentuales[$indicadorIndex]]);
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
        $indicadores = Indicador::all();

        return Voyager::view($view, compact(
            'dataType', 
            'dataTypeContent', 
            'isModelTranslatable',
            'indicadores'));
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

        //Agregamos indicadores
        $categoria = $data;
        if(isset($request->categorias_list)){
            $indicadores            = $request->categorias_list[0];
            $valores_porcentuales   = $request->categorias_list[1];
            $total = 0;
            //Verificamos que no esten repetidas los indicadores
            //Verificamos que la suma total de los valores porcentuales de las categorías sea 100%

            foreach($indicadores as $indicadorIndex => $indicador){

                foreach($indicadores as $indicadorIndex2 => $indicador2){
                    if(($indicadorIndex != $indicadorIndex2) && $indicador == $indicador2){
                        return redirect()->back()->with([
                            'message'    => 'Error, los indicadores no pueden estar repetidas',
                            'alert-type' => 'error',
                        ]);
                    }    
                }
                $total += (int)$valores_porcentuales[$indicadorIndex];
            }
            if($total != 100 && $total != 0){
                return redirect()->back()->with([
                    'message'    => 'Error, la suma de los valores porcentuales de los indicadores debe ser 100% o 0%',
                    'alert-type' => 'error',
                ]);
            }
            
            $categoria->indicadores()->detach();
            foreach($indicadores as $indicadorIndex => $indicador){
                $categoria->indicadores()->attach($indicador, ['valor_porcentual' => (int)$valores_porcentuales[$indicadorIndex]]);
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
