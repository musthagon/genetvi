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
        }
        .select2{
            width: 100% !important;
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
                                <label class="control-label" for="name">Categorías del instrumento</label>
                                <div class="table-responsive">  
                                    <table class="table table-bordered" id="dynamic_field">  
                                        <tr> 
                                            <th>
                                                <div class="th-flex">
                                                    <label class="control-label" for="name">Asociar categorías</label>
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
                                                    a
                                                </div>
                                            </th> 
                                            <th>
                                                <div id="total">
                                                    b
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
        var i=1;
        var max = 100;
        var min = 0;

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
        function consultarTotalValoresPorcentuales(){
            var sum = 0;
            $('.valor_porcentual').each(function(){
                sum += parseFloat(this.value);
            });
            return sum;
        }
        function actualizarTotal(){
            var sum = consultarTotalValoresPorcentuales();
            $('#total').text(sum); 
        }
        function distribuirValorPorcentual(){
            //$('.valor_porcentual').val(100/(i-1)); 
            var sumaDesactivados = 0;
            var cantidadActivos = 0;
            var total = 100;
            $('.valor_porcentual').each(function(){
                if ( $(this).attr('disabled') ) {
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
                if ( !$(this).attr('disabled') ) {
                    $(this).val(distribucion);
                } 
            });
            actualizarTotal();
        }

        $('body').on('DOMNodeInserted', 'select', function () {
            $(this).select2();
        });

        $('document').ready(function () {
            
            //Agregar categorias
            
            $('#add').click(function(){  
                i++;
                var element = '';
                element += '<tr id="row'+i+'">';
                element +=      '<td>';
                element +=          '<select id="select'+i+'" class="form-control select2" name="categorias_list[]">';
                @foreach($categorias as $categoria)
                    element +=              '<option value="{{$categoria->id}}">{{$categoria->nombre}}</option>';
                @endforeach
                element +=          '</select>';
                element +=      '</td>';
                element +=      '<td>';
                element +=          '<input id="valor_porcentual'+i+'" class="valor_porcentual form-control name_list" type="number" name="valores_porcentuales[]" placeholder="Valor porcentual" min="0" max="100"/>';
                element +=      '</td>';
                element +=      '<td>';
                element +=          '<button type="button" name="block" id="'+i+'" class="btn btn-info btn_block">B</button>';
                element +=      '</td>';
                element +=      '<td>';
                element +=          '<button type="button" name="remove" id="'+i+'" class="btn btn-danger btn_remove">X</button>';
                element +=      '</td>';
                element += '</tr>';
                //Agregamos en la penultima fila
                $('#dynamic_field tr:last').before(element);
                //Instanciamos select
                $('#select'+i).select2();
                //Distribuimos
                distribuirValorPorcentual();

                //$('.valor_porcentual').attr('disabled', 'disabled');
            });  
            $(document).on('click', '.btn_remove', function(){  
                i--;
                var button_id = $(this).attr("id");   
                $('#row'+button_id+'').remove();  
                distribuirValorPorcentual();
            });  

            $(document).on('click', '.btn_block', function(){  
                var button_id = $(this).attr("id");   
                //$('#valor_porcentual'+button_id).attr('disabled', 'disabled'); //Disable
                //$('#valor_porcentual'+button_id).removeAttr('disabled'); //Enable 

                //$('#valor_porcentual'+button_id).prop( "disabled", true ); //Disable
                //$('#valor_porcentual'+button_id).prop( "disabled", false ); //Enable

                $('#valor_porcentual'+button_id).prop( "disabled", function( i, val ) {
                    return !val;
                });
            }); 
            $(document).on('click', '#balancear_valor_porcentual', function(){  
                distribuirValorPorcentual();
            });  
            $(document).on('change', '.valor_porcentual', function() {
                actualizarTotal();
            });
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
            /*$('input').keyup(function(){
                var inputValue = $(this).val();
                if(inputValue > max){
                    $(this).val(max);
                }else if(inputValue < min){
                    $(this).val(min);
                }
            });*/

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
        });
    </script>
@stop
