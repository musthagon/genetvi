@extends('voyager::master')

@section('page_title', 'Viendo '.$informacion_pagina['categorias'] )

@section('page_header')
    <div class="container-fluid">
        <h1 class="page-title">
            <i class="icon voyager-settings"></i> 

            {{$informacion_pagina['categorias']}}
        </h1>
    </div>
@stop

@section('content')
    <div class="page-content browse container-fluid">
        
        <div class="analytics-container">
            <p style="border-radius:4px; padding:20px; background:#fff; margin:0; color:#999; text-align:center;">
                <code>Información sobre los Cursos del CV-UCV</code> 
                <ul>
                    <li>¿Qué es "Habilitar Evaluación"? Es permitir la evaluación de ese curso para el periodo lectivo previamente seleccionado. Entonces se enviaran automáticamente  invitaciones a evaluar a los participantes determinados de ese curso. Y podrás asignar evaluadores manualmente si hay instrumentos que lo permitan.</li>
                    <li>¿Qué es "Cerrar Evaluación"? Es no permitir que se realicen más evaluaciones para el periodo lectivo seleccionado y si aún había evaluadores sin evaluar, ya no podrán hacerlo.</li>
                    <li>¿Qué es "Ver Estatus de Evaluación"? Es ver en detalle el estatus de cada uno de los evaluadores (si han leído la evaluación, si la completaron, si la aceptaron o rechazaron,…) También, en este panel podrás invitar a evaluadores de forma manual.</li>
                    <li>¿Qué es "Ver Resultados de Evaluación"? Es ver los resultados de la evaluación para el determinado curso en todo sus periodos lectivos y momentos de evaluación.</li>
                </ul>
            </p>
        </div>

        <div class="row">
            <div class="col-md-12">

                @if(isset($cursos_por_categoria) && !empty($cursos_por_categoria))
                    <div class="panel panel-bordered">
                        <div class="panel-body">

                            <div class="page-title-content">
                                <h1 class="page-title page-title-custom">
                                    <i class="icon voyager-settings"></i> Cursos Activos
                                </h1>
                            </div>

                            <div class="table-responsive">
                                <table id="dataTable" class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Nombre</th>
                                            <th>Descripción</th>
                                            <th class="actions text-right">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($cursos_por_categoria as $cursos)
                                            @foreach($cursos as $curso)
                                            <tr>
                                                <td>{{$curso->id}}</td>
                                                <td><a href="{{env('CVUCV_GET_SITE_URL',setting('site.CVUCV_GET_SITE_URL')).'/course/view.php?id='.$curso->id}}" target="_blank"> {{$curso->cvucv_fullname}} </a></td>
                                                @php
                                                    $text = $curso->cvucv_summary;
                                                    preg_match_all('#\bhttps?://[^,\s()<>]+(?:\([\w\d]+\)|([^,[:punct:]\s]|/))#', $text, $match);
                                                    $links = $match[0];
                                                    if(!empty($links)){
                                                        foreach($links as $link){
                                                            if(strpos($link, env('CVUCV_GET_WEBSERVICE_ENDPOINT2', setting('site.CVUCV_GET_WEBSERVICE_ENDPOINT2')) ) !== false){
                                                                $text = str_replace($link, $link."?token=".$wstoken, $text);
                                                            }
                                                        }
                                                    }
                                                @endphp
                                                
                                                <td class="course_summary">{!!$text!!}</td>

                                                
                                                <td class="no-sort no-click" id="bread-actions">
                                                    @if($curso->getEvaluacionActiva() == true)
                                                        <a href="{{ route('curso_estatus_evaluacion_curso', ['categoria_id' => $curso->categoria, 'curso_id' => $curso->id]) }}" title="Ver" class="btn btn-sm btn-success" style="margin-right: 5px;">
                                                            <i class="voyager-eye"></i> Ver Estatus de Evaluación
                                                        </a>
                                                        <a href="{{ route('curso_cerrar_evaluacion_curso', ['id' => $curso->id]) }}" title="Ver" class="btn btn-sm btn-danger" style="margin-right: 5px;">
                                                            <i class="voyager-pause"></i> Cerrar Evaluación
                                                        </a>
                                                    @else
                                                        <a href="{{ route('curso_iniciar_evaluacion_curso', ['id' => $curso->id]) }}" title="Ver" class="btn btn-sm btn-success" style="margin-right: 5px;">
                                                            <i class="voyager-play"></i> Iniciar Evaluación
                                                        </a>
                                                    @endif
                                                    
                                                    <a href="{{ route('curso.visualizar_resultados_curso', ['categoria_id' => $curso->categoria, 'curso_id' => $curso->id]) }}" title="Ver" class="btn btn-sm btn-primary" style="margin-right: 5px;">
                                                        <i class="voyager-list"></i> Ver Resultados de Evaluaciones
                                                    </a>
                                                </td>
                                            </tr>
                                            @endforeach
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                                                                    
                        </div>
                    </div>
                @else
                    <div class="alerts">
                        <div class="alert alert-info">
                            <strong>No hay cursos disponibles</strong>
                        </div>   
                    </div>
                @endif 
                
            </div>
        </div>
    </div>

@stop

@section('css')
    <style>

    .course_summary p{
        text-align:left !important;
    }
    .page-title-content{
        text-align: center;
    }
    .page-title-custom{
        padding-top: 0px;
        height: auto;
        padding-bottom: 10px;
    }
    .page-title-custom i{
        top: 5px;
    }
    </style>
@stop

@section('javascript')
    

    <script>
        $(document).ready(function () {

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

                var table = $('#dataTable2').DataTable({!! json_encode(
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

                /*$('#search-input select').select2({
                    minimumResultsForSearch: Infinity
                });*/


            $('.select_all').on('click', function(e) {
                $('input[name="row_id"]').prop('checked', $(this).prop('checked'));
            });
        });


    </script>
@stop
