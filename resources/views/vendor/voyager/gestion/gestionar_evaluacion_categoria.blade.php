@extends('voyager::master')

@section('page_title', __('Gestión de Evaluación'))

@section('page_header')
    <div class="container-fluid">
        <h1 class="page-title">
            <i class="icon voyager-settings"></i> Gestión de Evaluación para {{$categoria->cvucv_name}}
        </h1>  
    </div>
@stop

@section('content')
    <div class="page-content browse container-fluid">
        
        <div class="row">
            <div class="col-md-12">

                <div class="panel panel-bordered">
                    <!-- form start -->
                    <form role="form"
                            class="form-edit-add"
                            action="{{ $edit ? route('gestion.evaluacion_categoria_edit', ['id' => $categoria->id]) : route('gestion.evaluacion_categoria_store', ['id' => $categoria->id]) }}"
                            method="POST">

                        <!-- PUT Method if we are editing -->
                        @if($edit)
                            {{ method_field("PUT") }}
                        @endif

                        <!-- CSRF TOKEN -->
                        {{ csrf_field() }}

                        <div class="panel-body">

                            <div class="form-group  col-md-12 ">
                                <label class="control-label" for="name">Periodo Lectivo</label>
                                <select id="periodos" class="form-control select2" name="periodo_lectivo" required>
                                @foreach($periodos_lectivos as $periodo)
                                    <?php $selected = ''; ?>
                                        @if($edit)
                                            @if($categoria->getPeriodoLectivo() == $periodo->id))
                                                <?php $selected = 'selected="selected"'; ?>
                                            @endif
                                        @endif
                                    <option value="{{$periodo->id}}" {!! $selected !!}>{{$periodo->nombre}}</option>
                                @endforeach
                                </select>
                            </div>

                            <div class="form-group  col-md-12 ">
                                <label class="control-label" for="name">Instrumentos a habilitar</label>
                                <select id="instrumentos" class="form-control select2" placeholder="a" name="instrumentos[]" multiple required>
                                    <?php $selected = ''; ?>
                                    @if($edit && $categoria->instrumentos_habilitados->isEmpty())
                                        <?php $selected = 'selected="selected"'; ?>
                                    @endif

                                    <option value="null" {!! $selected !!}>Ninguno</option>
                                    @foreach($instrumentos as $instrumento)
                                        <?php $selected = ''; ?>

                                        @if($edit)
                                            @foreach ($categoria->instrumentos_habilitados as $instrumento_almacenado)
                                                @if($instrumento_almacenado->id == $instrumento->id))
                                                    <?php $selected = 'selected="selected"'; ?>
                                                @endif
                                            @endforeach
                                        @endif

                                        <option value="{{$instrumento->id}}" {!! $selected !!}>{{$instrumento->nombre}}</option>
                                        
                                    @endforeach
                                </select>
                            </div>
                        </div><!-- panel-body -->

                        <div class="panel-footer">

                            <button type="submit" class="btn btn-primary save">{{ __('Guardar') }}</button>

                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@stop

@section('css')
    <style>

    </style>
@stop

@section('javascript')
    

    <script>
        $(document).ready(function () {
            $("#periodos").select2({
                placeholder: "Selecciona el periodo lectivo a evaluar",
            });
            $("#instrumentos").select2({
                placeholder: "Selecciona los instrumentos a habilitar para esta categoría",
            });       
            $("#instrumentos").on('change', function(){
                var selected = $(this).val();

                if(selected != null){
                    if(selected.indexOf('null')>=0){
                        $(this).val('null').select2();
                    }
                }
            });
        });
    </script>
@stop
