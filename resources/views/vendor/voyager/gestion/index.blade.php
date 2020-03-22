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
                    <li>Puedes ver los <a href="{{ route('gestion.evaluaciones_cursos_activas') }}">cursos en evaluación</a>. O navegar por cada una de las categorías en esta página</li>
                    <li>¿Qué es sincronizar? Es solicitar la información de cursos y categorías al CV-UCV para mostrarlos en esta página. Si estas buscando un curso/categoría y no aparece aquí, entonces debes sincronizar.</li>
                    <li>¿Qué es "Habilitar Evaluación"? Es permitir la evaluación de ese curso para el periodo lectivo previamente seleccionado. Entonces se enviaran automáticamente  invitaciones a evaluar a los participantes determinados de ese curso. Y podrás asignar evaluadores manualmente si hay instrumentos que lo permitan.</li>
                    <li>¿Qué es "Cerrar Evaluación"? Es no permitir que se realicen más evaluaciones para el periodo lectivo seleccionado y si aún había evaluadores sin evaluar, ya no podrán hacerlo.</li>
                    <li>¿Qué es "Ver Estatus de Evaluación"? Es ver en detalle el estatus de cada uno de los evaluadores (si han leído la evaluación, si la completaron, si la aceptaron o rechazaron,…) También, en este panel podrás invitar a evaluadores de forma manual.</li>
                    <li>¿Qué es "Ver Resultados de Evaluación"? Es ver los resultados de la evaluación para el determinado curso en todo sus periodos lectivos y momentos de evaluación.</li>
                </ul>
            </p>
        </div>

        <div class="row">
            <div class="col-md-12">

                @if(isset($cursos))
                    <div class="panel panel-bordered">
                        <div class="panel-body">

                            <div class="page-title-content">
                                <h1 class="page-title page-title-custom">
                                    <i class="icon voyager-settings"></i> Cursos de {{$informacion_pagina['categorias']}}
                                </h1>
                            </div>
                            @if(!$cursos->isEmpty())
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
                                        </tbody>
                                    </table>
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
                @endif   
                
                @if(isset($categorias) && !$categorias->isEmpty())
                    <div class="panel panel-bordered">
                        <div class="panel-body">

                                <div class="page-title-content">
                                    <h1 class="page-title page-title-custom">
                                        <i class="icon voyager-settings"></i> {{$informacion_pagina['categorias']}}
                                    </h1>
                                </div>
                            
                                <div class="table-responsive">
                                    <table id="dataTable2" class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Nombre</th>
                                                <th>Descripción</th>
                                                <th class="actions text-right">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($categorias as $categoria)
                                                <tr>
                                                    <td>{{$categoria->id}}</td>
                                                    <td><a href="{{env('CVUCV_GET_SITE_URL',setting('site.CVUCV_GET_SITE_URL')).'/moodle/course/index.php?categoryid='.$categoria->id}}" target="_blank"> {{$categoria->cvucv_name}} </a></td>
                                                    @php
                                                        $text = $categoria->cvucv_description;
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

                                                        @if (auth()->user()->hasRole('admin') && filter_var(setting('admin.creacion_de_roles_para_categorias', false), FILTER_VALIDATE_BOOLEAN))
                                                            <a href="{{ route('gestion.generar_permisos', ['id' => $categoria->getID()]) }}" title="Generar Permisos" class="btn btn-sm btn-warning" style="margin-right: 5px;">
                                                                <i class="voyager-people"></i> Generar Permisos 
                                                            </a>
                                                        @endif   

                                                        @if($categoria->cvucv_coursecount<=0)

                                                            @if($categoria->cvucv_category_parent_id>0) 
                                                                @if (Gate::allows('checkCategoryPermissionSisgeva', ['sincronizar_',$categoria->categoria_raiz->cvucv_name]  ))
                                                                <a href="{{ route('gestion.sincronizar', ['id' => $categoria->id]) }}" title="Sincronizar" class="btn btn-sm btn-success" style="margin-right: 5px;">
                                                                    <i class="voyager-list"></i> Sincronizar 
                                                                </a>
                                                                @endif 
                                                            @else

                                                                @if (Gate::allows('checkCategoryPermissionSisgeva', ['sincronizar_',$categoria->cvucv_name]  ))
                                                                <a href="{{ route('gestion.sincronizar', ['id' => $categoria->id, 'categoria_raiz' => true]) }}" title="Sincronizar" class="btn btn-sm btn-success" style="margin-right: 5px;">
                                                                    <i class="voyager-list"></i> Sincronizar 
                                                                </a>
                                                                @endif  

                                                            @endif
                                                        @else
                                                            @if (Gate::allows('checkCategoryPermissionSisgeva', ['sincronizar_',$categoria->categoria_raiz->cvucv_name]  ))
                                                            <a href="{{ route('gestion.sincronizar', ['id' => $categoria->id, 'sync_courses' => true]) }}" title="Sincronizar" class="btn btn-sm btn-success" style="margin-right: 5px;">
                                                                <i class="voyager-list"></i> Sincronizar ({{$categoria->cvucv_coursecount}} cursos)
                                                            </a>
                                                            @endif
                                                        @endif
                                                        
                                                        <a href="{{ route('gestion.evaluaciones2', ['id' => $categoria->id]) }}" title="Ver" class="btn btn-sm btn-primary" style="margin-right: 5px;">
                                                            <i class="voyager-eye"></i> Ver
                                                        </a>


                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            
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
