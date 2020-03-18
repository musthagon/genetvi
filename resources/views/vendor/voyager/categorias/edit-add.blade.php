@php
    $edit = !is_null($dataTypeContent->getKey());
    $add  = is_null($dataTypeContent->getKey());
@endphp

@extends('voyager::master')

@section('css')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        .th-flex{
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
        }
        tr .select2{
            width: 100% !important;
        }
        tr .select2-selection--single {
            height: 100% !important;
        }
        tr .select2-selection__rendered{
            word-wrap: break-word !important;
            text-overflow: inherit !important;
            white-space: normal !important;
        }
        .max-height{
            height:100%;
        }
        .has-error .select2-selection{
            /*border: 1px solid #a94442;
            border-radius: 4px;*/
            border-color:rgb(185, 74, 72) !important;
        }
        .error{
            color:rgb(185, 74, 72) ;
        }
    </style>
@stop

@section('page_title', __(($edit ? 'Editar' : 'Agregar')).' '.$dataType->getTranslatedAttribute('display_name_singular'))

@section('page_header')
    <h1 class="page-title">
        <i class="{{ $dataType->icon }}"></i>
        {{ __(($edit ? 'Editar' : 'Agregar')).' '.$dataType->getTranslatedAttribute('display_name_singular') }}
    </h1>
    @include('voyager::multilingual.language-selector')
@stop

@section('content')
    <div class="page-content edit-add container-fluid">
        <div class="row">
            <div class="col-md-12">

                <div class="panel panel-bordered">
                    <!-- form start -->
                    <form role="form"
                            class="form-edit-add"
                            action="{{ $edit ? route('voyager.'.$dataType->slug.'.update', $dataTypeContent->getKey()) : route('voyager.'.$dataType->slug.'.store') }}"
                            method="POST" enctype="multipart/form-data">
                        <!-- PUT Method if we are editing -->
                        @if($edit)
                            {{ method_field("PUT") }}
                        @endif

                        <!-- CSRF TOKEN -->
                        {{ csrf_field() }}

                        <div class="panel-body">

                            @if (count($errors) > 0)
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <!-- Adding / Editing -->
                            @php
                                $dataTypeRows = $dataType->{($edit ? 'editRows' : 'addRows' )};
                                $slideCount = 0;
                            @endphp

                            @foreach($dataTypeRows as $row)
                                <!-- GET THE DISPLAY OPTIONS -->
                                @php
                                    $display_options = $row->details->display ?? NULL;
                                    if ($dataTypeContent->{$row->field.'_'.($edit ? 'edit' : 'add')}) {
                                        $dataTypeContent->{$row->field} = $dataTypeContent->{$row->field.'_'.($edit ? 'edit' : 'add')};
                                    }
                                @endphp
                                @if (isset($row->details->legend) && isset($row->details->legend->text))
                                    <legend class="text-{{ $row->details->legend->align ?? 'center' }}" style="background-color: {{ $row->details->legend->bgcolor ?? '#f0f0f0' }};padding: 5px;">{{ $row->details->legend->text }}</legend>
                                @endif

                                <div class="form-group @if($row->type == 'hidden') hidden @endif col-md-{{ $display_options->width ?? 12 }} {{ $errors->has($row->field) ? 'has-error' : '' }}" @if(isset($display_options->id)){{ "id=$display_options->id" }}@endif>
                                    {{ $row->slugify }}
                                    <label class="control-label" for="name">{{ $row->getTranslatedAttribute('display_name') }}</label>

                                    @if($row->getTranslatedAttribute('display_name') == "Opciones")
                                        
                                        <span class="initialism slide1_open glyphicon glyphicon-question-sign"></span>

                                        @php $slideCount++; @endphp

                                        @include('vendor.voyager.partials.jquery-popub-overlay', ['slideID' => 'slide'.$slideCount, 'slideTitle' => $row->display_name, 'slideNext' => 'slide'.($slideCount+1),
                                        'slideContent' => '
                                            <div>Para establecer una escala de Likert diferente, el formato es el siguiente: <br>
                                                <pre class="prettyprint prettyprinted"><code>{ "likert" : ["Opcion1","Opcion2","Opcion3"] }</code></pre>
                                                Y si la categoría es para el perfil, se debe agregar al formato:<br>
                                                <pre class="prettyprint prettyprinted"><code>{ "perfil" : true, "likert" : ["Opcion1","Opcion2","Opcion3"] }</code></pre>
                                            </div>
                                        '])
                                    @endif

                                    @include('voyager::multilingual.input-hidden-bread-edit-add')
                                    @if (isset($row->details->view))
                                        @include($row->details->view, ['row' => $row, 'dataType' => $dataType, 'dataTypeContent' => $dataTypeContent, 'content' => $dataTypeContent->{$row->field}, 'action' => ($edit ? 'edit' : 'add')])
                                    @elseif ($row->type == 'relationship')
                                        @include('voyager::formfields.relationship', ['options' => $row->details])
                                    @else
                                        {!! app('voyager')->formField($row, $dataType, $dataTypeContent) !!}
                                    @endif

                                    @foreach (app('voyager')->afterFormFields($row, $dataType, $dataTypeContent) as $after)
                                        
                                        @if(isset($row->details->description))
                                            <span class="initialism slide1_open glyphicon glyphicon-question-sign"></span>

                                            @php $slideCount++; @endphp

                                            @include('vendor.voyager.partials.jquery-popub-overlay', ['slideID' => 'slide'.$slideCount, 'slideTitle' => $row->display_name, 'slideNext' => 'slide'.($slideCount+1),
                                            'slideContent' => $row->details->description])
 
                                        @else
                                            {!! $after->handle($row, $dataType, $dataTypeContent) !!}
                                        @endif

                                    @endforeach
                                    @if ($errors->has($row->field))
                                        @foreach ($errors->get($row->field) as $error)
                                            <span class="help-block">{{ $error }}</span>
                                        @endforeach
                                    @endif
                                </div>
                            @endforeach

                            <div class="form-group">  
                                <label class="control-label" for="name">Indicadores de la categoría</label>
                                <div class="table-responsive">  
                                    <table class="table table-bordered max-height" id="dynamic_field">  
                                        <tr> 
                                            <th>
                                                <div class="th-flex">
                                                    <label class="control-label" for="name">Asociar indicadores</label>
                                                    <a id="add" class="btn btn-success btn-add-new">
                                                        <i class="voyager-plus"></i>
                                                        <span>Agregar</span>
                                                    </a>
                                                </div>
                                            </th> 
                                            <th>
                                                <div class="th-flex">
                                                    <label class="control-label" for="name">Distribuir ponderación</label>
                                                    <a id="balancear_valor_porcentual" class="btn btn-info btn-add-new">
                                                        <i class="voyager-resize-small"></i>
                                                        <span>Distribuir</span>
                                                    </a>
                                                </div>
                                            </th>
                                            <th></th>
                                            <th></th>
                                        </tr>
                                        <tr> 
                                            <th>
                                                <div class="th-flex">
                                                    Total
                                                </div>
                                            </th> 
                                            <th>
                                                <div id="total">
                                                    0
                                                </div>
                                            </th>
                                            <th></th>
                                            <th></th>
                                        </tr>
                                    </table>  
                                </div>   
                            </div>  

                        </div><!-- panel-body -->

                        <div class="panel-footer">
                            @section('submit-buttons')
                                <button type="submit" class="btn btn-primary save">{{ __('voyager::generic.save') }}</button>
                            @stop
                            @yield('submit-buttons')
                        </div>
                    </form>

                    <iframe id="form_target" name="form_target" style="display:none"></iframe>
                    <form id="my_form" action="{{ route('voyager.upload') }}" target="form_target" method="post"
                            enctype="multipart/form-data" style="width:0;height:0;overflow:hidden">
                        <input name="image" id="upload_file" type="file"
                                 onchange="$('#my_form').submit();this.value='';">
                        <input type="hidden" name="type_slug" id="type_slug" value="{{ $dataType->slug }}">
                        {{ csrf_field() }}
                    </form>

                </div>
            </div>
        </div>
    </div>

    <div class="modal fade modal-danger" id="confirm_delete_modal">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"
                            aria-hidden="true">&times;</button>
                    <h4 class="modal-title"><i class="voyager-warning"></i> {{ __('voyager::generic.are_you_sure') }}</h4>
                </div>

                <div class="modal-body">
                    <h4>{{ __('voyager::generic.are_you_sure_delete') }} '<span class="confirm_delete_name"></span>'</h4>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">{{ __('voyager::generic.cancel') }}</button>
                    <button type="button" class="btn btn-danger" id="confirm_delete">{{ __('voyager::generic.delete_confirm') }}</button>
                </div>
            </div>
        </div>
    </div>
    <!-- End Delete File Modal -->
@stop

@section('javascript')
    <script>
        var params = {};
        var $file;
        var index=1;
        var max = 100;
        var min = 0;

        //Función para calcular la suma de las categorías
        function consultarTotalValoresPorcentuales(){
            var sum = 0;
            $('.valor_porcentual').each(function(){
                sum += parseFloat(this.value);
            });
            return sum;
        }
        //Función para  actualiar la suma de las categorías
        function actualizarTotal(){
            var sum = consultarTotalValoresPorcentuales();
            $('#total').text(sum);
            if (sum != 100){
                $('#total').addClass('error');
            }else{
                $('#total').removeClass('error');
            }
        }
        //Función para distribuir valor porcentual de las categorias
        function distribuirValorPorcentual(){
            //$('.valor_porcentual').val(100/(i-1)); 
            var sumaDesactivados = 0;
            var cantidadActivos = 0;
            var total = 100;
            $('.valor_porcentual').each(function(){
                if ( $(this).attr('readOnly') ) {
                    sumaDesactivados += parseFloat(this.value);
                } else {
                    cantidadActivos++;
                }
            });

            var distribucion = total - sumaDesactivados;
            if(cantidadActivos == 0){
                distribucion = 0;
            }else{
                distribucion = distribucion/cantidadActivos;
            }

            $('.valor_porcentual').each(function(){
                if ( !$(this).attr('readOnly') ) {
                    $(this).val(distribucion);
                } 
            });
            actualizarTotal();
        }
        //Función para validar que las categorías no esten repetidas
        function validarCategorias(){
            $('.select2_categorias').each(function(){
                    var value = $(this).val();
                    var id = $(this).attr("id");   
                    var exist = false;
                    $('.select2_categorias').each(function(){
                        if($(this).val() == value && $(this).attr("id") != id){
                            exist = true;
                        }
                    });
                    if(exist){
                        $(this).parent().addClass('has-error');
                    }else{
                        $(this).parent().removeClass('has-error');
                    }
                });
        }

        function actualizarCategoriasAsociadas(){
            @if(!isset($indicadoresAsociados) )
                return 1;
            @else
                var index = 1;
                @foreach($indicadoresAsociados as $indicadorAsociada)
                    
                    index++;
                    var element = '';
                    element += '<tr id="row'+index+'">';
                    element +=      '<td class="form-group">';
                    element +=          '<select id="select'+index+'" class="form-control select2 select2_categorias" name="categorias_list[0][]">';
                    @php $optionSelected=false; @endphp
                    @foreach($indicadores as $indicador)
                        element +=              '<option target="'+index+'" class="opcion_indicador @if(!$indicador->esMedible()) block_opcion @endif" value="{{$indicador->id}}" @if($indicador->id == $indicadorAsociada->id){{'selected="selected"' }}@endif >{{$indicador->nombre}}</option>';

                        @php if($indicador->id == $indicadorAsociada->id && !$indicador->esMedible()) {$optionSelected=true;} @endphp


                    @endforeach
                    element +=          '</select>';
                    element +=      '</td>';
                    element +=      '<td>';
                    element +=          '<input id="valor_porcentual'+index+'" class="valor_porcentual form-control name_list max-height" type="number" name="categorias_list[1][]" placeholder="Valor porcentual" min="0" max="100" step="any" value="{{$indicadorAsociada->pivot->valor_porcentual}}" @if($optionSelected) readOnly @endif/>';
                    element +=      '</td>';
                    element +=      '<td>';
                    element +=          '<button type="button" name="block" target="'+index+'" id="btn_block'+index+'" class="btn btn-info btn_block" @if($optionSelected) disabled @endif><i class="voyager-lock"></i>Bloquear</button>';
                    element +=      '</td>';
                    element +=      '<td>';
                    element +=          '<button type="button" name="remove" target="'+index+'" id="btn_remove'+index+'" class="btn btn-danger btn_remove"><i class="voyager-trash"></i>Eliminar</button>';
                    element +=      '</td>';
                    element += '</tr>';

                    //Agregamos en la penultima fila
                    $('#dynamic_field tr:last').before(element);
                    
                    //Instanciamos select
                    $('#select'+index).select2();
                        
                    validarCategorias();
                    actualizarTotal();

                @endforeach
                return index;

            @endif
        }

        $('document').ready(function () {
            index = actualizarCategoriasAsociadas();
            //Agregar categorias al click
            $('#add').click(function(){  
                index++;
                var element = '';
                element += '<tr id="row'+index+'">';
                element +=      '<td class="form-group">';
                element +=          '<select id="select'+index+'" class="form-control select2 select2_categorias" name="categorias_list[0][]">';
                @foreach($indicadores as $indicador)
                    element +=              '<option target="'+index+'" class="opcion_indicador @if(!$indicador->esMedible()) block_opcion @endif" value="{{$indicador->id}}">{{$indicador->nombre}}</option>';
                @endforeach
                element +=          '</select>';
                element +=      '</td>';
                element +=      '<td>';
                element +=          '<input id="valor_porcentual'+index+'" class="valor_porcentual form-control name_list max-height" type="number" name="categorias_list[1][]" placeholder="Valor porcentual" min="0" max="100" step="any"/>';
                element +=      '</td>';
                element +=      '<td>';
                element +=          '<button type="button" name="block" target="'+index+'" id="btn_block'+index+'" class="btn btn-info btn_block"><i class="voyager-lock"></i>Bloquear</button>';
                element +=      '</td>';
                element +=      '<td>';
                element +=          '<button type="button" name="remove" target="'+index+'" id="btn_remove'+index+'" class="btn btn-danger btn_remove"><i class="voyager-trash"></i>Eliminar</button>';
                element +=      '</td>';
                element += '</tr>';
                //Agregamos en la penultima fila
                $('#dynamic_field tr:last').before(element);
                //Instanciamos select
                $('#select'+index).select2();
                //Distribuimos
                distribuirValorPorcentual();
                validarCategorias();
            });  
            //Remover categorias al click
            $(document).on('click', '.btn_remove', function(){  
                var button_id = $(this).attr("target");   
                $('#row'+button_id+'').remove();  
                distribuirValorPorcentual();
                validarCategorias()
            });  
            //Bloquear balanceo de valor porcentual para categoría actual
            $(document).on('click', '.btn_block', function(){  
                var button_id = $(this).attr("target");

                $('#valor_porcentual'+button_id).prop( "readOnly", function( i, val ) {
                    return !val;
                });
            }); 
            //Distribuir valor porcentual en click
            $(document).on('click', '#balancear_valor_porcentual', function(){  
                distribuirValorPorcentual();
            });  
            //Actualizar suma de valores porcentuales
            $(document).on('change', '.valor_porcentual', function() {
                actualizarTotal();
            });
            //Validar categorías repetidas
            $(document).on('change', '.select2_categorias', function() {
                var optionSelected = $("option:selected", this);
                var opctionSelectedClasses = optionSelected.attr('class');
                var isBlock         = opctionSelectedClasses.includes("block_opcion");
                var idTarget        = optionSelected.attr('target')
                var inputTarget     = document.getElementById("valor_porcentual"+idTarget);
                var buttonTarget    = document.getElementById("btn_block"+idTarget); 

                if(isBlock){
                    inputTarget.value = 0; 
                    inputTarget.readOnly = true;
                    buttonTarget.disabled = true;
                    distribuirValorPorcentual();
                }else if(buttonTarget.disabled){
                        buttonTarget.disabled = false;
                }
                    
                validarCategorias();
            });
            //Bloquear ingreso de valores distintos de entre 0-100
            $(document).on('keyup', '.valor_porcentual', function() {
                var inputValue = $(this).val();
                if(inputValue > max){
                    $(this).val(max);
                }else if(inputValue < min){
                    $(this).val(min);
                }else if(inputValue == ''){
                    $(this).val(min);
                }
                
            });


            $('.toggleswitch').bootstrapToggle();

            //Init datepicker for date fields if data-datepicker attribute defined
            //or if browser does not handle date inputs
            $('.form-group input[type=date]').each(function (idx, elt) {
                if (elt.type != 'date' || elt.hasAttribute('data-datepicker')) {
                    elt.type = 'text';
                    $(elt).datetimepicker($(elt).data('datepicker'));
                }
            });

            @if ($isModelTranslatable)
                $('.side-body').multilingual({"editing": true});
            @endif

            $('.side-body input[data-slug-origin]').each(function(i, el) {
                $(el).slugify();
            });

            $('.form-group').on('click', '.remove-multi-image', deleteHandler('img', true));
            $('.form-group').on('click', '.remove-single-image', deleteHandler('img', false));
            $('.form-group').on('click', '.remove-multi-file', deleteHandler('a', true));
            $('.form-group').on('click', '.remove-single-file', deleteHandler('a', false));

            $('#confirm_delete').on('click', function(){
                $.post('{{ route('voyager.media.remove') }}', params, function (response) {
                    if ( response
                        && response.data
                        && response.data.status
                        && response.data.status == 200 ) {

                        toastr.success(response.data.message);
                        $file.parent().fadeOut(300, function() { $(this).remove(); })
                    } else {
                        toastr.error("Error removing file.");
                    }
                });

                $('#confirm_delete_modal').modal('hide');
            });
            $('[data-toggle="tooltip"]').tooltip();


            //JS Para activar las opciones en formato JSON
            // Create an ace editor instance
	        var ace_editor_element = document.getElementsByClassName("ace_editor");

            // For each ace editor element on the page
            for(var i = 0; i < ace_editor_element.length; i++)
            {

                // Create an ace editor instance
                var ace_editor = ace.edit(ace_editor_element[i].id);

                // Get the corresponding text area associated with the ace editor
                var ace_editor_textarea = document.getElementById(ace_editor_element[i].id + '_textarea');

                if(ace_editor_element[i].getAttribute('data-theme')){
                    ace_editor.setTheme("ace/theme/" + ace_editor_element[i].getAttribute('data-theme'));
                }

                //if(ace_editor_element[i].getAttribute('data-language')){
                    ace_editor.getSession().setMode("ace/mode/json");
                //}
                
                ace_editor.on('change', function(event, el) {
                    ace_editor_id = el.container.id;
                    ace_editor_textarea = document.getElementById(ace_editor_id + '_textarea');
                    ace_editor_instance = ace.edit(ace_editor_id);
                    ace_editor_textarea.value = ace_editor_instance.getValue();
                });
            }
        });

        function deleteHandler(tag, isMulti) {
          return function() {
            $file = $(this).siblings(tag);

            params = {
                slug:   '{{ $dataType->slug }}',
                filename:  $file.data('file-name'),
                id:     $file.data('id'),
                field:  $file.parent().data('field-name'),
                multi: isMulti,
                _token: '{{ csrf_token() }}'
            }

            $('.confirm_delete_name').text(params.filename);
            $('#confirm_delete_modal').modal('show');
          };
        }
    </script>

    <script type="text/javascript" src="{{ asset('js/jquery.popupoverlay.js') }}"></script>
    
    <script>
        $(document).ready(function () {
            $('.slide-jquery-pop-up-overlay').popup({
                vertical: 'top',
                outline: true,
                focusdelay: 400,
                closebutton: true
            });
        });
    </script>
@stop
