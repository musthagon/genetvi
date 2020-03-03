@extends('layouts.users')

@section('page_description')
  <h1>
    {{$informacion_pagina['titulo']}}
    <small>{{$informacion_pagina['descripcion']}}</small>
  </h1>
@stop

@section('content')
    <div class="container-fluid">
        <h2 class="page-header">
            <i class="fa fa-globe"></i> {{$curso->getNombre()}}
        </h2>
        
        @include('dashboards.revisiones_publicas')
    
    </div>

    @include('dashboards.revisiones_publicas_tabla')

@stop


@section('css')
    <link rel="stylesheet" href="/css/user_list.css">
    <style>
        
        .page-title {
            display: inline-block;
            height: auto;
            font-size: 18px;
            height: 100px;
            line-height: 43px;
            margin-top: 3px;
            padding-top: 28px;
            color: #555;
            position: relative;
            padding-left: 75px;
            margin-bottom: 0;
            font-weight: 700;
            margin-right: 20px;
        }
        .page-title>i {
            font-size: 36px;
            position: absolute;
            top: 30px;
            left: 25px;
            margin-right: 10px;
        }
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
