@extends('voyager::master')

@section('page_title', __($curso->cvucv_fullname))

@section('page_header')
    <div class="container-fluid">
        <h1 class="page-title">
            <i class="icon voyager-settings"></i> {{$curso->cvucv_fullname}}
        </h1>  
        
        @include('dashboards.revisiones_publicas')

    </div>
@stop

@section('content')
    @include('dashboards.revisiones_publicas_tabla')
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

    </script>

    @include('dashboards.revisiones_publicas_javascript')
    
@stop
