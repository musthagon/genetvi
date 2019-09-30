@extends('voyager::master')

@section('page_title', __('Gestión'))

@section('page_header')
    <div class="container-fluid">
        <h1 class="page-title">
            <i class="icon voyager-settings"></i> Gestión
        </h1>

        <a href="#" class="btn btn-success btn-add-new">
            <i class="voyager-plus"></i> <span>Actualizar</span>
        </a>

   
    </div>
@stop

@section('content')
    <div class="page-content browse container-fluid">
        
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-bordered">
                    <div class="panel-body">
                       
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
                                    @foreach($categorias_padre as $categoria)
                                    <tr>
                                        <td>{{$categoria['id']}}</td>
                                        <td><a href="{{env('CVUCV_GET_SITE_URL').'/moodle/course/index.php?categoryid='.$categoria['id']}}" target="_blank"> {{$categoria['name']}} </a></td>
                                        @php
                                            // The Regular Expression filter
                                            $reg_exUrl = "/(?i)\b((?:https?:\/\/|www\d{0,3}[.]|[a-z0-9.\-]+[.][a-z]{2,4}\/)(?:[^\s()<>]+|\(([^\s()<>]+|(\([^\s()<>]+\)))*\))+(?:\(([^\s()<>]+|(\([^\s()<>]+\)))*\)|[^\s`!()\[\]{};:'\".,<>?«»“”‘’]))/";
                                        
                                            // The Text you want to filter for urls
                                            //$text = '<p style="text-align:center;"><img style="vertical-align:middle;margin-left:auto;margin-right:auto;" src="https://campusvirtual.ucv.ve/webservice/pluginfile.php/23/coursecat/description/banner-medicina.jpg" alt="" height="135" width="725" /></p> <p style="text-align:center;"> </p> <p style="text-align:center;">Coordinador de EaD de Medicina: Prof. Mariano Fernández</p> <p style="text-align:center;">Administrador de Espacio Virtual de Medicina: Edmund Chia - correo: edmund.chia@ucv.ve</p>';

                                            $text = $categoria['description'];

                                            // Check if there is a url in the text
                                            if(preg_match($reg_exUrl, $text, $url)) {
                                                $text = preg_replace($reg_exUrl,  $url[0]."?token=".$wstoken , $text);
                                            }
                                        @endphp
                                        
                                        <td class="course_summary">{!!$text!!}</td>

                                        
                                        <td class="no-sort no-click" id="bread-actions">

                                            <a href="gestion/{{$categoria['id']}}/sincronizar_categorias" title="Sincronizar" class="btn btn-sm btn-success pull-right" style="margin-right: 5px;">
                                                <i class="voyager-list"></i> Sincronizar
                                            </a>

                                            <a href="gestion/{{$categoria['id']}}/sincronizar_categorias" title="Sincronizar" class="btn btn-sm btn-success pull-right" style="margin-right: 5px;">
                                                <i class="voyager-list"></i> Ver
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
               
                    </div>
                </div>
            </div>
        </div>
    </div>

@stop

@section('css')
    <style>

    .course_summary p{
        text-align:left !important;
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

                /*$('#search-input select').select2({
                    minimumResultsForSearch: Infinity
                });*/


            $('.select_all').on('click', function(e) {
                $('input[name="row_id"]').prop('checked', $(this).prop('checked'));
            });
        });


    </script>
@stop
