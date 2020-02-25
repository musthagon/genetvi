@extends('voyager::master')

@section('page_title', __($curso->cvucv_fullname))

@section('page_header')
    <div class="container-fluid">
        <h1 class="page-title">
            <i class="icon voyager-settings"></i> {{$curso->cvucv_fullname}}
        </h1>  
        
        <div class="container-fluid">
            <form class="form-edit-add" 
                action="{{ route('curso.visualizar_resultados_curso.respuesta_publica', ['categoria_id' => $curso->categoria, 'curso_id' => $curso->id]) }}" 
                method="GET">

                <div class="form-group  col-sm-6 col-md-3 ">
                    <label class="control-label" for="name">Periodo Lectivo</label>
                    <select id="periodos_lectivos" class="form-control select2" name="periodo_lectivo" required>
                        @foreach($periodos_collection as $periodo_index=>$periodo)
                            <option value="{{$periodo->id}}">{{$periodo->nombre}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group  col-sm-6 col-md-3 ">
                    <label class="control-label" for="name">Instrumentos</label>
                    <select id="instrumentos" class="form-control select2" name="instrumento" required>
                        @foreach($instrumentos_collection2 as $instrumento_index=>$instrumento)
                            <option value="{{$instrumento->id}}">{{$instrumento->nombre}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group  col-sm-8 col-md-4 ">
                    <label class="control-label" for="name">Seleccionar usuario</label>
                    <select id="search_users" class="js-data-example-ajax form-control select2" name="user" required>
                    </select>
                </div>
                <div class="form-group  col-sm-4 col-md-2 " style="margin-top: 15px;">
                    <button type="submit" class="btn btn-success btn-add-new">
                        <i class="voyager-plus"></i> <span>Ir</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
@stop

@section('content')
    <div class="page-content browse container-fluid">
        
        <div class="row">
            <div class="col-md-12">

                @if(isset($evaluacion))
                <div class="panel panel-bordered">
                    <div class="panel-body">

                        <div class="page-title-content">
                            <h1 class="page-title page-title-custom">
                                <i class="icon voyager-settings"></i> <div>Evaluación de {{$usuario['fullname']}}</div>
                                <div>
                                    Periodo Lectivo: {{$periodo_lectivo->nombre}}, Instrumento: {{$instrumento->nombre}}
                                </div>
                                <div>
                                    Valoración: {{$evaluacion->percentil_eva}}%
                                </div>
                                
                            </h1>
                            <div class="">
                                    <div class="select2-result-repository__avatar">
                                        <img src="{{$usuario['profileimageurl']}}">
                                    </div>
                                    <div class="select2-result-repository__meta">
                                        <div class="select2-result-repository__title">{{$usuario['fullname']}}</div>
                                        <div class="select2-result-repository__description">{{$usuario['email']}}</div>
                                    </div>
                                </div>
                        </div>
                        @if(!empty($evaluacion))
                            <div class="table-responsive">
                                <table id="dataTable" class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Nº</th>
                                            <th>Categoría</th>
                                            <th>Nombre Indicador</th>
                                            <th>Respuesta</th>
                                            <th>Valor</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($evaluacion->respuestas_evaluacion as $respuestaIndex => $respuesta)
                                        <tr>
                                            <td>{{$respuestaIndex+1}}</td>
                                            <td>{{$respuesta->categoria->nombre}}</td>
                                            <td>{{$respuesta->indicador_nombre}}</td>
                                            <td>{{$respuesta->value_string}}</td>
                                            <td>@if($respuesta->value_percentil != -1) {{$respuesta->value_percentil}}% @else No medible @endif</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alerts">
                                <div class="alert alert-info">
                                    <strong>No hay evaluaciones disponibles</strong>
                                </div>   
                            </div>
                        @endif
                                          

                    </div>
                </div>
                @endif   
                

            </div>
        </div>
    </div>
@stop


@section('css')
    <link rel="stylesheet" href="/css/user_list.css">
    <style>
        .page-title-content{
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
        }
        .page-title-custom{
            line-height: 1.1; 
            padding-top: 0px;
        }
    </style>
@stop

@section('javascript')
                                          
    <script>
        var table = $('#dataTable').DataTable({!! json_encode(
            array_merge([
                "language" => [
                    "sProcessing"=>    "Procesando...",
                    "sLengthMenu"=>     "Mostrar _MENU_ registros",
                    "sZeroRecords"=>    "No se encontraron resultados",
                    "sEmptyTable"=>     "Ningún dato disponible en esta tabla =(",
                    "sInfo"=>          "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                    "sInfoEmpty"=>      "Mostrando registros del 0 al 0 de un total de 0 registros",
                    "sInfoFiltered"=>   "(filtrado de un total de _MAX_ registros)",
                    "sInfoPostFix"=>    "",
                    "sSearch"=>         "Buscar:",
                    "sUrl"=>           "",
                    "sInfoThousands"=>  ",",
                    "sLoadingRecords"=> "Cargando...",
                    "oPaginate"=> [
                        "sFirst"=>    "Primero",
                        "sLast"=>     "Último",
                        "sNext"=>     "Siguiente",
                        "sPrevious"=> "Anterior"
                    ],
                    "oAria"=> [
                        "sSortAscending"=>  ": Activar para ordenar la columna de manera ascendente",
                        "sSortDescending"=> ": Activar para ordenar la columna de manera descendente"
                    ],
                    "buttons"=> [
                        "copy"=> "Copiar",
                        "colvis"=> "Visibilidad"
                    ]
                ],
                "columnDefs" => [['targets' => -1, 'searchable' =>  false, 'orderable' => false]],
            ],
            config('voyager.dashboard.data_tables', []))
        , true) !!});

        // CSRF Token
        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
        
        $(document).ready(function () {
            
            
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $("#periodos_lectivos").select2({
                placeholder: 'Seleccionar periodo lectivo a consultar',
            });

            $("#search_users").select2({
                language: {
                    /*inputTooShort: function () {
                        return "Mínimo 4 caracteres";
                    }*/
                },
                ajax: {
                    
                    url: "{{route('campus_users_by_ids')}}",
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            lastname: params.term, // search term
                            curso_id: {{$curso->id}},
                            periodo_lectivo_id: $("#periodos_lectivos").val(),
                            instrumento_id: $("#instrumentos").val(),
                            page: params.page || 1,
                        };
                    },
                    cache: true
                },
                placeholder: 'Ver revisiones públicas',
                minimumInputLength: 0,
                templateResult: formatRepo,
                templateSelection: formatRepoSelection
            });

            function formatRepo (results) {
                if (results.loading) {
                    return results.text;
                }

                var $container = $(
                    "<div class='select2-result-repository clearfix'>" +
                    "<div class='select2-result-repository__avatar'><img src='" + results.profileimageurl + "' /></div>" +
                    "<div class='select2-result-repository__meta'>" +
                        "<div class='select2-result-repository__title'></div>" +
                        "<div class='select2-result-repository__description'></div>" +
                        "</div>" +
                    "</div>" +
                    "</div>"
                );

                $container.find(".select2-result-repository__title").text(results.fullname);
                $container.find(".select2-result-repository__description").text(results.email);

                return $container;
            }

            function formatRepoSelection (repo) {
                return repo.fullname || repo.text;
            }
            
        
        
        });
    </script>
    
@stop
