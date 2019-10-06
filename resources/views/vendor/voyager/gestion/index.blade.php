@extends('voyager::master')

@section('page_title', __('Gestión'))

@section('page_header')
    <div class="container-fluid">
        <h1 class="page-title">
            <i class="icon voyager-settings"></i> 
            Gestión
        </h1>
    </div>
@stop

@section('content')
    <div class="page-content browse container-fluid">
        
        <div class="row">
            <div class="col-md-12">

                @if(isset($cursos))
                <div class="panel panel-bordered">
                    <div class="panel-body">

                        <div class="page-title-content">
                            <h1 class="page-title page-title-custom">
                                <i class="icon voyager-settings"></i> Cursos
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
                                            <td><a href="{{env('CVUCV_GET_SITE_URL').'/course/view.php?id='.$curso->id}}" target="_blank"> {{$curso->cvucv_fullname}} </a></td>
                                            @php
                                                // The Regular Expression filter
                                                $reg_exUrl = "/(?i)\b((?:https?:\/\/|www\d{0,3}[.]|[a-z0-9.\-]+[.][a-z]{2,4}\/)(?:[^\s()<>]+|\(([^\s()<>]+|(\([^\s()<>]+\)))*\))+(?:\(([^\s()<>]+|(\([^\s()<>]+\)))*\)|[^\s`!()\[\]{};:'\".,<>?«»“”‘’]))/";
                                                                                    
                                                $text = $curso->cvucv_summary;

                                                // Check if there is a url in the text
                                                if(preg_match($reg_exUrl, $text, $url)) {
                                                    $text = preg_replace($reg_exUrl,  $url[0]."?token=".$wstoken , $text);
                                                }
                                            @endphp
                                            
                                            <td class="course_summary">{!!$text!!}</td>

                                            
                                            <td class="no-sort no-click" id="bread-actions">
                                                                                           
                                                <a href="{{ route('curso.visualizar', ['id' => $curso->id]) }}" title="Ver" class="btn btn-sm btn-primary" style="margin-right: 5px;">
                                                    <i class="voyager-list"></i> Ver
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
                
                @if(isset($categorias))
                @if(!$categorias->isEmpty())
                
                <div class="panel panel-bordered">
                    <div class="panel-body">

                            <div class="page-title-content">
                                <h1 class="page-title page-title-custom">
                                    <i class="icon voyager-settings"></i> Categorías
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
                                            <td><a href="{{env('CVUCV_GET_SITE_URL').'/moodle/course/index.php?categoryid='.$categoria->id}}" target="_blank"> {{$categoria->cvucv_name}} </a></td>
                                            @php
                                                // The Regular Expression filter
                                                $reg_exUrl = "/(?i)\b((?:https?:\/\/|www\d{0,3}[.]|[a-z0-9.\-]+[.][a-z]{2,4}\/)(?:[^\s()<>]+|\(([^\s()<>]+|(\([^\s()<>]+\)))*\))+(?:\(([^\s()<>]+|(\([^\s()<>]+\)))*\)|[^\s`!()\[\]{};:'\".,<>?«»“”‘’]))/";
                                                                                    
                                                $text = $categoria->cvucv_description;

                                                // Check if there is a url in the text
                                                if(preg_match($reg_exUrl, $text, $url)) {

                                                    $text = preg_replace($reg_exUrl,  $url[0]."?token=".$wstoken , $text);
                                                }
                                            @endphp
                                            
                                            <td class="course_summary">{!!$text!!}</td>

                                            
                                            <td class="no-sort no-click" id="bread-actions">
                                                
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

                                                        @if (Gate::allows('checkCategoryPermissionSisgeva', ['habilitar_evaluacion_',$categoria->cvucv_name]  ))
                                                        <a href="{{ route('gestion.evaluacion_categoria', ['id' => $categoria->id]) }}" title="Evaluación" class="btn btn-sm btn-warning" style="margin-right: 5px;">
                                                            <i class="voyager-edit"></i> Evaluación
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
                        "language" => __('voyager::datatable'),
                        "columnDefs" => [['targets' => -1, 'searchable' =>  false, 'orderable' => false]],
                    ],
                    config('voyager.dashboard.data_tables', []))
                , true) !!});

                var table = $('#dataTable2').DataTable({!! json_encode(
                    array_merge([
                        "language" => __('voyager::datatable'),
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
