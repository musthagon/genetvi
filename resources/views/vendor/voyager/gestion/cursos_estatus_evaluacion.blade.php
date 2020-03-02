@extends('voyager::master')

@section('page_title', __($curso->cvucv_fullname))

@section('page_header')
    <div class="container-fluid">
        <h1 class="page-title">
            <i class="icon voyager-settings"></i> {{$curso->cvucv_fullname}}
        </h1>  
    </div>
@stop

@section('content')

    
    <div class="page-content browse container-fluid">
            
        <div class="row">
            <div class="col-md-12">
                
                <div class="panel panel-bordered">

                    <form role="form"
                        class="form-edit-add"
                        action="{{ route('curso_invitar_evaluacion_curso', ['id' => $curso->id]) }}"
                        method="POST">

                        <!-- CSRF TOKEN -->
                        {{ csrf_field() }}
                    
                        <div class="panel-body">
  
                            <div class="page-title-content">
                                <h1 class="page-title page-title-custom">
                                    <i class="icon voyager-settings"></i> Invitar usuarios a evaluar el {{$curso->getNombre()}}. <div>Periodo Lectivo: {{$periodo_lectivo_actual->getNombre()}}</div>
                                </h1>

                            </div>

                            <div class="form-group col-md-12 ">
                                <label class="control-label" for="name">Buscar usuario por nombre y/o apellido</label>
                                <select id="search_users" class="js-data-example-ajax form-control select2" name="users[]" multiple required>
                                </select>
                            </div>

                            <div class="form-group col-md-6 ">
                                <label class="control-label" for="name">Momento de evalución a invitar</label>
                                <select id="momento_evaluacion" class="form-control select2" name="momentos_evaluacion[]" multiple required>
                                    @foreach($momentos_evaluacion as $momento)
                                    <option value="{{$momento->getId()}}">{{$momento->getNombre()}}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group col-md-6 ">
                                <label class="control-label" for="name">Instrumentos a invitar</label>
                                <select id="instrumentos" class="form-control select2" name="instrumentos_manuales[]" multiple required>
                                    @foreach($instrumentos_manuales as $instrumento)
                                    <option value="{{$instrumento->getID()}}">{{$instrumento->getNombre()}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="panel-footer">
                            <button type="submit" class="btn btn-primary save">Enviar invitación para evaluar</button>
                        </div>

                    </form>

                </div>
                
                @if(!empty($invitaciones_curso))
                    <div class="panel panel-bordered">
                        <div class="panel-body">

                            <div class="page-title-content">
                                <h1 class="page-title page-title-custom">
                                    <i class="icon voyager-settings"></i>Evaluadores invitados al curso {{$curso->cvucv_fullname}}.
                                </h1>
                            </div>
                        
                            <div class="table-responsive">
                                <table id="dataTable" class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Nombre del Evaluador</th>
                                            <th>Correo del Evaluador</th>
                                            <th>Instrumento asignado para evaluar</th>
                                            <th>Periodo Lectivo</th>
                                            <th>Momento de Evaluación</th>
                                            <th>Estatus invitacion a evaluar</th>
                                            <th>Tipo de invitacion</th>
                                            <th class="actions text-right">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($invitaciones_curso as $invitacion_index => $invitacion)
                                        <tr>
                                            @if(isset($revisores[$invitacion_index]['id']) && isset($revisores[$invitacion_index]['profileimageurlsmall']) && $revisores[$invitacion_index]['profileimageurl'] && isset($revisores[$invitacion_index]['fullname']) && isset ($revisores[$invitacion_index]['email']))
                                                <td>
                                                    <a href="{{env('CVUCV_GET_SITE_URL','https://campusvirtual.ucv.ve')}}/user/view.php?id={{$revisores[$invitacion_index]['id']}}&course={{$curso->id}}" target="_blank">
                                                        <div class="pull-left image">

                                                            @if( strpos( $revisores[$invitacion_index]['profileimageurlsmall'], env('CVUCV_GET_WEBSERVICE_ENDPOINT1') ) !== false )
                                                                <img src="{{env('CVUCV_GET_WEBSERVICE_ENDPOINT2',setting('site.CVUCV_GET_WEBSERVICE_ENDPOINT2'))}}/{{strtok($revisores[$invitacion_index]['profileimageurlsmall'], env('CVUCV_GET_WEBSERVICE_ENDPOINT1'))}}/user/icon/f1?token={{env('CVUCV_ADMIN_TOKEN')}}" class="img-circle" alt="User Image"> 
                                                            @else
                                                                <img src="{{$revisores[$invitacion_index]['profileimageurl']}}" class="img-circle" alt="User Image">
                                                            @endif

                                                        </div>{{$revisores[$invitacion_index]['fullname']}}
                                                    </a>
                                                </td>
                                                
                                                <td>
                                                    {{$revisores[$invitacion_index]['email']}}
                                                </td>
                                            @else
                                                <td colspan="2">
                                                    {{$invitacion->getCvucv_user_id()}}
                                                </td>
                                            @endif
                                            
                                            <td>
                                                {{$invitacion->instrumento->getNombre()}}
                                            </td>

                                            <td>
                                                {{$invitacion->periodo->getNombre()}}
                                            </td>

                                            <td>
                                                {{$invitacion->momento_evaluacion->getNombre()}}
                                            </td>

                                            <td>
                                                {{$invitacion->estatus_invitacion->getNombre()}}
                                            </td>

                                            <td>
                                                {{$invitacion->tipo_invitacion->getNombre()}}
                                            </td>
                                            
                                            <td class="no-sort no-click" id="bread-actions">                                               
                                                
                                                @if(!$invitacion->invitacion_completada())
                                                    
                                                    
                                                    @if(!$invitacion->invitacion_revocada())
                                                        <a href="{{ route('curso_enviar_recordatorio', ['id' => $curso->id, 'invitacion' => $invitacion->id]) }}" title="Reenviar invitación" class="btn btn-sm btn-primary" style="margin-right: 5px;">
                                                            <i class="voyager-list"></i> Reenviar invitación a evaluar
                                                        </a>

                                                        <a href="{{ route('curso_revocar_invitacion', ['id' => $curso->id, 'invitacion' => $invitacion->id]) }}" title="Revocar invitación" class="btn btn-sm btn-danger" style="margin-right: 5px;">
                                                            <i class="voyager-trash"></i> Revocar invitación a evaluar
                                                        </a>
                                                    @else
                                                        <a href="{{ route('curso_enviar_recordatorio', ['id' => $curso->id, 'invitacion' => $invitacion->id]) }}" title="Reenviar invitación" class="btn btn-sm btn-primary" style="margin-right: 5px;">
                                                            <i class="voyager-list"></i> Asignar invitación a evaluar
                                                        </a>
                                                    @endif
                                                @else
                                                    @if(!$invitacion->instrumento->getAnonimo())
                                                        

                                                        <a href="{{ route('curso.visualizar_resultados_curso.respuesta_publica', 
                                                                ['categoria_id' => $curso->categoria, 
                                                                'curso_id' => $curso->id,
                                                                'periodo_lectivo' => $invitacion->periodo_lectivo_id,
                                                                'instrumento' => $invitacion->instrumento_id,
                                                                'user' => $invitacion->cvucv_user_id]) }}" 
                                                                title="Reenviar invitación" class="btn btn-sm btn-warning" style="margin-right: 5px;">
                                                            <i class="voyager-eye"></i> Ver evaluación
                                                        </a>
                                                    @else
                                                        <div class="completada">Evaluación anónima realizada satisfactoriamente</div>
                                                    @endif
                                                @endif
                                                                                        
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
   
    <link rel="stylesheet" href="{{asset('css/user_list.css')}}">
    <style>


    </style>
@stop

@section('javascript')
    

    <script>
        // CSRF Token
        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
        
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
        
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $("#search_users").select2({
                language: {
                    // You can find all of the options in the language files provided in the
                    // build. They all must be functions that return the string that should be
                    // displayed.
                    inputTooShort: function () {
                        return "Mínimo 4 caracteres";
                    }
                },
                ajax: {
                    
                    url: "{{route('campus_users')}}",
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            //_token: CSRF_TOKEN,
                            lastname: params.term, // search term
                            page: params.page || 1,
                        };
                    },
                    cache: true
                },
                placeholder: 'Buscar usuarios por nombre y/o apellido',
                minimumInputLength: 2,
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
            
            $("#instrumentos").select2({
                placeholder: "Seleccione entre los instrumentos disponibles para invitación manual",
            });

            $("#momento_evaluacion").select2({
                placeholder: "Seleccione el momento de evaluación",
            });
        
        });
    </script>
@stop
