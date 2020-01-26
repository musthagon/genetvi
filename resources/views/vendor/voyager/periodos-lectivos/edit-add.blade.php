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

@section('page_title', __('voyager::generic.'.($edit ? 'edit' : 'add')).' '.$dataType->getTranslatedAttribute('display_name_singular'))

@section('page_header')
    <h1 class="page-title">
        <i class="{{ $dataType->icon }}"></i>
        {{ __('voyager::generic.'.($edit ? 'edit' : 'add')).' '.$dataType->getTranslatedAttribute('display_name_singular') }}
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
                                    @include('voyager::multilingual.input-hidden-bread-edit-add')
                                    @if (isset($row->details->view))
                                        @include($row->details->view, ['row' => $row, 'dataType' => $dataType, 'dataTypeContent' => $dataTypeContent, 'content' => $dataTypeContent->{$row->field}, 'action' => ($edit ? 'edit' : 'add')])
                                    @elseif ($row->type == 'relationship')
                                        @include('voyager::formfields.relationship', ['options' => $row->details])
                                    @else
                                        {!! app('voyager')->formField($row, $dataType, $dataTypeContent) !!}
                                    @endif

                                    @foreach (app('voyager')->afterFormFields($row, $dataType, $dataTypeContent) as $after)
                                        {!! $after->handle($row, $dataType, $dataTypeContent) !!}
                                    @endforeach
                                    @if ($errors->has($row->field))
                                        @foreach ($errors->get($row->field) as $error)
                                            <span class="help-block">{{ $error }}</span>
                                        @endforeach
                                    @endif
                                </div>
                            @endforeach
                            
                            <div class="form-group">  
                                <label class="control-label" for="name">Momentos de evaluación</label>
                                <div class="table-responsive">  
                                    <table class="table table-bordered max-height" id="dynamic_field">  
                                        <tr> 
                                            <th>
                                                <div class="th-flex">
                                                    <label class="control-label" for="name">Asociar momentos de evaluación</label>
                                                    <a id="add" class="btn btn-success btn-add-new">
                                                        <i class="voyager-plus"></i>
                                                        <span>Agregar</span>
                                                    </a>
                                                </div>
                                            </th> 
                                            <th></th>
                                            <th></th>
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

        function actualizarMomentosAsociadas(){

            @if(!isset($momentosAsociados) )

                return 1;

            @else

                var index = 1;

                @foreach($momentosAsociados as $momentoAsociado)

                    index++;

                    var element = '';
                    element += '<tr id="row'+index+'">';
                    element +=      '<td class="form-group">';
                    element +=          '<select id="select'+index+'" class="form-control select2 select2_categorias" name="categorias_list[0][]">';
                    @foreach($momentos as $momento)
                        element +=              '<option target="'+index+'" class="opcion_indicador " value="{{$momento->getId()}}" @if($momento->getId() == $momentoAsociado->getId()){{ 'selected="selected"' }}@endif >{{$momento->getNombre()}}</option>';
                    @endforeach
                    element +=          '</select>';
                    element +=      '</td>';
                    element +=      '<td>';
                    element +=          '<input id="fecha_inicio'+index+'" class="form-control name_list" type="date" name="categorias_list[1][]" value="{{ \Carbon\Carbon::parse($momentoAsociado->pivot->get_fecha_inicio())->format('Y-m-d') }}" placeholder="Fecha de inicio"/>';
                    element +=      '</td>';
                    element +=      '<td>';
                    element +=          '<input id="fecha_fin'+index+'" class="form-control name_list" type="date" name="categorias_list[2][]" value="{{ \Carbon\Carbon::parse($momentoAsociado->pivot->get_fecha_fin())->format('Y-m-d') }}" placeholder="Fecha de fin"/>';
                    element +=      '</td>';
                    element +=      '<td>';
                    element +=          '<input id="code'+index+'" class="form-control name_list" type="text" name="categorias_list[3][]" value="{{$momentoAsociado->pivot->get_opciones()}}" placeholder="Opciones de Configuración"/>';
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

                @endforeach

                return index;

            @endif
        }

        $('document').ready(function () {
            index = actualizarMomentosAsociadas();
            //Agregar categorias al click
            $('#add').click(function(){  
                index++;
                var element = '';
                element += '<tr id="row'+index+'">';
                element +=      '<td class="form-group">';
                element +=          '<select id="select'+index+'" class="form-control select2 select2_categorias" name="categorias_list[0][]">';
                @foreach($momentos as $momento)
                    element +=              '<option target="'+index+'" class="opcion_indicador " value="{{$momento->getId()}}">{{$momento->getNombre()}}</option>';
                @endforeach
                element +=          '</select>';
                element +=      '</td>';
                element +=      '<td>';
                element +=          '<input id="fecha_inicio'+index+'" class="form-control name_list" type="date" name="categorias_list[1][]" placeholder="Fecha de inicio"/>';
                element +=      '</td>';
                element +=      '<td>';
                element +=          '<input id="fecha_fin'+index+'" class="form-control name_list" type="date" name="categorias_list[2][]" placeholder="Fecha de fin"/>';
                element +=      '</td>';
                element +=      '<td>';
                element +=          '<input id="code'+index+'" class="form-control name_list" type="text" name="categorias_list[3][]" placeholder="Opciones de Configuración"/>';
                element +=      '</td>';
                element +=      '<td>';
                element +=          '<button type="button" name="remove" target="'+index+'" id="btn_remove'+index+'" class="btn btn-danger btn_remove"><i class="voyager-trash"></i>Eliminar</button>';
                element +=      '</td>';
                element += '</tr>';

                //Agregamos en la penultima fila
                $('#dynamic_field tr:last').before(element);

                //Instanciamos select
                $('#select'+index).select2();

                //Validamos que no hay filas repetidas
                validarCategorias();
            });  
            //Remover categorias al click
            $(document).on('click', '.btn_remove', function(){  

                var button_id = $(this).attr("target");   
                $('#row'+button_id+'').remove(); 

                //Validamos que no hay filas repetidas 
                validarCategorias();
            });  
            
            //Validar categorías repetidas
            $(document).on('change', '.select2_categorias', function() {
                var optionSelected = $("option:selected", this);
                var opctionSelectedClasses = optionSelected.attr('class');
                var isBlock         = opctionSelectedClasses.includes("block_opcion");
                var idTarget        = optionSelected.attr('target')
                var inputTarget     = document.getElementById("valor_porcentual"+idTarget);
                var buttonTarget    = document.getElementById("btn_block"+idTarget); 

                validarCategorias();
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
@stop
