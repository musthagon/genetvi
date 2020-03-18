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

            flex-wrap: wrap;
            display: flex;
            justify-content: center;
            align-items: center;
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
        .has-error .select2-selection, .has-error{
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
                            @endphp

                            <!-- Categorias de Cursos -->
                            @php $countCategorias = count($categoriasDeCurso) @endphp 
                            @if($countCategorias == 1 && isset($categoriasDeCurso[0]))
                                <legend class="text-center" style="background-color: #f0f0f0;padding: 5px;">Periodo Lectivo para {{$categoriasDeCurso[0]->getNombre()}}</legend>
                            @endif
                            <div class="form-group @if($countCategorias == 1) hidden @endif col-md-12 ">
                                <label class="control-label" for="name">Facultades o Dependencias a las cuales les va asociar este periodo lectivo</label>
                                <select id="categoriasDeCurso" class="form-control select2" name="categoriaDeCurso" required>
                                    @foreach($categoriasDeCurso as $index=>$categoriaDeCurso)
                                        <?php $selected = ''; ?>
                                        @if($edit && isset($categoria))
                                            @if($categoria->getID() == $categoriaDeCurso->getID()))
                                                <?php $selected = 'selected="selected"'; ?>
                                            @endif
                                        @endif
                                        <option value="{{$categoriaDeCurso->getID()}}" {!! $selected !!}>{{$categoriaDeCurso->getNombre()}}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            @php $slideCount = 0; @endphp

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
                            
                            <div class="form-group col-md-12">  
                                <label class="control-label" for="name">Momentos de evaluación</label>
                                <div class="table-responsive">  
                                    <table class="table table-bordered max-height" id="dynamic_field">  
                                        <tr> 
                                            <th><label class="control-label" for="name">Nombre</label></th> 
                                            <th>
                                                <label class="control-label" for="name">Fecha de Inicio</label>
                                                @php $slideCount2 = $slideCount + 1; @endphp
                                                <span class="initialism slide{{$slideCount2}}_open glyphicon glyphicon-question-sign"></span>
                                                @php $slideCount2++; @endphp
                                            </th>
                                            <th>
                                                <label class="control-label" for="name">Fecha de Fin</label>
                                                <span class="initialism slide{{$slideCount2}}_open glyphicon glyphicon-question-sign"></span>
                                            </th>
                                            <th><label class="control-label" for="name">Opciones</label></th>
                                            <th></th>
                                        </tr> 
                                        <tr> 
                                            <td>
                                                <div class="th-flex">
                                                    <label class="control-label" for="name">Asociar momentos de evaluación</label>
                                                    <a id="add" class="btn btn-success btn-add-new">
                                                        <i class="voyager-plus"></i>
                                                        <span>Agregar</span>
                                                    </a>
                                                </div>
                                            </td> 
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            
                                        </tr>                                      
                                    </table>  
                                </div>   
                            </div>
                            
                            
                            <div class="form-group  col-md-12 ">
                                <legend class="text-center" style="background-color: #f0f0f0;padding: 5px;">Instrumentos a Habilitar para este Periodo Léctivo</legend>
                                <label class="control-label" for="name">Instrumentos a habilitar</label>
                                <select id="instrumentos" class="form-control select2" name="instrumentos[]" multiple required>
                                    <?php $selected = ''; ?>
                                    @if($edit && isset($categoria) && $categoria->instrumentos_habilitados->isEmpty())
                                        <?php $selected = 'selected="selected"'; ?>
                                    @endif

                                    <option value="null" {!! $selected !!}>Ninguno</option>
                                    @foreach($instrumentos as $instrumento)
                                        <?php $selected = ''; ?>

                                        @if($edit && isset($categoria))
                                            @foreach ($categoria->instrumentos_habilitados as $instrumento_almacenado)
                                                @if($instrumento_almacenado->getID() == $instrumento->getID()))
                                                    <?php $selected = 'selected="selected"'; ?>
                                                @endif
                                            @endforeach
                                        @endif

                                        <option value="{{$instrumento->getID()}}" {!! $selected !!}>{{$instrumento->getNombre()}}</option>
                                        
                                    @endforeach
                                </select>
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
    @php $slideCount++; @endphp
                                    
    @include('vendor.voyager.partials.jquery-popub-overlay', ['slideID' => 'slide'.$slideCount, 'slideTitle' => 'Fecha de Inicio del Momento de Evaluación', 'slideNext' => 'slide'.($slideCount+1),
    'slideContent' => '
        <div>
            <p>La fecha de inicio del momento de evaluación debe estar dentro del rango del período lectivo, y debe ser menor a la fecha de fin. Y si agrega otro momento de evaluación, las fechas deben ser posteriores a el anterior momento</p>
        </div>
    '])
    
    @php $slideCount++; @endphp

    @include('vendor.voyager.partials.jquery-popub-overlay', ['slideID' => 'slide'.$slideCount, 'slideTitle' => 'Fecha de Fin del Momento de Evaluación', 'slideNext' => 'slide'.($slideCount+1),
    'slideContent' => '
        <div>
            <p>La fecha de fin del momento de evaluación debe estar dentro del rango del período lectivo, y debe ser mayor a la fecha de inicio. Y si agrega otro momento de evaluación, las fechas deben ser posteriores a el anterior momento</p>
        </div>
    '])

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

                @foreach($momentosAsociados as $momentoIndex => $momentoAsociado)

                    index++;

                    var element = '';
                    element += '<tr id="row'+index+'">';
                    element +=      '<td class="form-group">';
                    element +=          '<select id="select'+index+'" class="form-control select2 select2_categorias" name="momento_evaluacion[0][]">';
                    @foreach($momentos as $momento)
                        element +=              '<option target="'+index+'" class="opcion_indicador " value="{{$momento->getId()}}" @if($momento->getId() == $momentoAsociado->getId()){{ 'selected="selected"' }}@endif >{{$momento->getNombre()}}</option>';
                    @endforeach
                    element +=          '</select>';
                    element +=      '</td>';

                    element +=      '<td>';
                    element +=      '<div class="input-group date" id="div_fecha_inicio'+index+'"> ';
                    element +=      '   <input type="text" class="form-control name_list" name="momento_evaluacion[1][]" id="fecha_inicio'+index+'" ';
                    element +=      '       placeholder="Fecha de inicio" ';
                    element +=      '       value="{{ date("m-d-Y H:i:s",strtotime( $momentoAsociado->pivot->get_fecha_inicio()) ) }}"> ';
                    element +=      '   <span class="input-group-addon"> ';
                    element +=      '       <span class="glyphicon glyphicon-calendar"></span> ';
                    element +=      '    </span> ';
                    element +=      '</div> ';
                    element +=      '</td>';

                    element +=      '<td>';
                    element +=      '<div class="input-group date" id="div_fecha_fin'+index+'"> ';
                    element +=      '   <input type="text" class="form-control name_list" name="momento_evaluacion[2][]" id="fecha_fin'+index+'" ';
                    element +=      '       placeholder="Fecha de fin" ';
                    element +=      '       value="{{ date("m-d-Y H:i:s",strtotime( $momentoAsociado->pivot->get_fecha_fin()) ) }}"> ';
                    element +=      '   <span class="input-group-addon"> ';
                    element +=      '       <span class="glyphicon glyphicon-calendar"></span> ';
                    element +=      '    </span> ';
                    element +=      '</div> ';
                    element +=      '</td>';


                    element +=      '<td>';
                    element +=          '<input id="code'+index+'" class="form-control name_list" type="text" name="momento_evaluacion[3][]" value="{{$momentoAsociado->pivot->get_opciones()}}" placeholder="Opciones de Configuración"/>';
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

        function agregarMomentoEvaluacion(){  
            index++;
            var element = '';
            element += '<tr id="row'+index+'">';
            element +=      '<td class="form-group">';
            element +=          '<select id="select'+index+'" class="form-control select2 select2_categorias" name="momento_evaluacion[0][]">';
            @foreach($momentos as $momento)
                element +=              '<option target="'+index+'" class="opcion_indicador " value="{{$momento->getId()}}">{{$momento->getNombre()}}</option>';
            @endforeach
            element +=          '</select>';
            element +=      '</td>';

            element +=      '<td>';
            element +=      '<div class="input-group date" id="div_fecha_inicio'+index+'"> ';
            element +=      '   <input type="text" class="form-control name_list" name="momento_evaluacion[1][]" id="fecha_inicio'+index+'" ';
            element +=      '       placeholder="Fecha de inicio" >';
            element +=      '   <span class="input-group-addon"> ';
            element +=      '       <span class="glyphicon glyphicon-calendar"></span> ';
            element +=      '    </span> ';
            element +=      '</div> ';
            element +=      '</td>';

            element +=      '<td>';
            element +=      '<div class="input-group date" id="div_fecha_fin'+index+'"> ';
            element +=      '   <input type="text" class="form-control name_list" name="momento_evaluacion[2][]" id="fecha_fin'+index+'" ';
            element +=      '       placeholder="Fecha de fin" >';
            element +=      '   <span class="input-group-addon"> ';
            element +=      '       <span class="glyphicon glyphicon-calendar"></span> ';
            element +=      '    </span> ';
            element +=      '</div> ';
            element +=      '</td>';

            element +=      '<td>';
            element +=          '<input id="code'+index+'" class="form-control name_list" type="text" name="momento_evaluacion[3][]" placeholder="Opciones de Configuración"/>';
            element +=      '</td>';
            element +=      '<td>';
            element +=          '<button type="button" name="remove" target="'+index+'" id="btn_remove'+index+'" class="btn btn-danger btn_remove"><i class="voyager-trash"></i>Eliminar</button>';
            element +=      '</td>';
            element += '</tr>';

            //Agregamos en la penultima fila
            $('#dynamic_field tr:last').before(element);

            //Instanciamos select
            $('#select'+index).select2();

            //Instanciamos el data picker
            $picker = $('.date');
            $picker.datetimepicker({
                format: 'MM/DD/YYYY, h:mm a',
                locale: 'es'
            });

            $picker.datetimepicker().on('dp.show', function() {
                $(this).closest('.table-responsive').removeClass('table-responsive').addClass('temp');
                }).on('dp.hide', function() {
                    $(this).closest('.temp').addClass('table-responsive').removeClass('temp')
            });

            //Validamos que no hay filas repetidas
            validarCategorias();

        }
                
        $('document').ready(function () {
            
            index = actualizarMomentosAsociadas();
            
            //Agregar categorias al click
            $('#add').click( function(){
                agregarMomentoEvaluacion();
            } );
            
            //Instanciamos el data picker
            $picker = $('.date');
            $picker.datetimepicker({
                format: 'MM/DD/YYYY, h:mm a',
                locale: 'es'
            });

            $picker.datetimepicker().on('dp.show', function() {
                $(this).closest('.table-responsive').removeClass('table-responsive').addClass('temp');
                }).on('dp.hide', function() {
                    $(this).closest('.temp').addClass('table-responsive').removeClass('temp')
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
            
            //Validar precedencia de la fecah de inicio y fin del periodo lectivo
            $('input[name ="fecha_inicio"], input[name ="fecha_fin"]').change(function() {
                $fecha_inicio = $('input[name ="fecha_inicio"]');
                $fecha_fin = $('input[name ="fecha_fin"]');
                if($fecha_inicio.val() >= $fecha_fin.val()){
                    $fecha_inicio.addClass('has-error');
                    $fecha_fin.addClass('has-error');
                }else{
                    $fecha_inicio.removeClass('has-error');
                    $fecha_fin.removeClass('has-error');
                }
            });

            $(document).on('change', 'tr input[name*="momento_evaluacion[1]"]', function() {
                $fecha_inicio = $(this);
                $fecha_fin = $(this).parent().next().children();
                if($fecha_inicio.val() >= $fecha_fin.val()){
                    $fecha_inicio.addClass('has-error');
                    $fecha_fin.addClass('has-error');
                }else{
                    $fecha_inicio.removeClass('has-error');
                    $fecha_fin.removeClass('has-error');
                }

            });
            $(document).on('change', 'tr input[name*="momento_evaluacion[2]"]', function() {
                $fecha_inicio = $(this).parent().prev().children();
                $fecha_fin = $(this);
                if($fecha_inicio.val() >= $fecha_fin.val()){
                    $fecha_inicio.addClass('has-error');
                    $fecha_fin.addClass('has-error');
                }else{
                    $fecha_inicio.removeClass('has-error');
                    $fecha_fin.removeClass('has-error');
                }
            });
            
            $('.toggleswitch').bootstrapToggle();

            $("#categoriasDeCurso").select2({
                placeholder: 'Seleccione la Facultad o Dependencia para asociar este periodo lectivo',
            });

            $("#instrumentos").select2({
                placeholder: "Seleccione los instrumentos a habilitar para esta Categoría / Dependencia",
            });

            $("#instrumentos").on('change', function(){
                var selected = $(this).val();

                if(selected != null){
                    if(selected.indexOf('null')>=0){
                        $(this).val('null').select2();
                    }
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
