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
    <main class="cd-main-content">
        
        <!-- cd-tab-filter-wrapper -->
        <div class="cd-tab-filter-wrapper">
			<div class="cd-tab-filter">
				<ul class="cd-filters">
                   
					<li class="placeholder"> 
						<a data-type="all" href="#0">Todos</a><!-- selected option on mobile -->
                    </li> 
                    
                    <li class="filter"><a class="selected" href="#0" data-type="all">Todos</a></li>

                    <!-- periodos lectivos -->
                    @php 
                        $total = count($periodos_collection);
                    @endphp
                    @foreach($periodos_collection as $periodo_index=>$periodo)
                        @if(!empty($periodo))
                            <li class="filter" data-filter=".Periodo_{{$periodo->id}}">
                                <a href="#0" class="" data-type="Periodo_{{$periodo->id}}">
                                    {{$periodo->nombre}}
                                </a>
                            </li>
                        @endif
                    @endforeach
                    
                    <li class="filter" data-filter=".general"><a href="#0" class="" data-type="general">Otros</a></li>

                    
					
				</ul> <!-- cd-filters -->
			</div> <!-- cd-tab-filter -->
		</div> <!-- cd-tab-filter-wrapper -->
        
        <!-- cd-gallery -->
		<section class="cd-gallery">
            @if($curso->getEvaluacionActiva() != 0)
                <div class="analytics-container">
                    <p style="border-radius:4px; padding:20px; background:#fff; margin:0; color:#999; text-align:center;">
                    <code>Información</code> Las estadísticas relacionadas el periodo léctivo {{$curso->periodo_lectivo_actual()->getNombre()}} pueden variar, ya que este curso todavía está en proceso de evaluación
                    </p>
                </div>
            @endif
            <section class="page-content browse container-fluid ">
                <div class="row">
                    
                    @if(!empty($cantidadEvaluacionesCursoCharts1))
                        <div class="chartTarget col-xs-12 col-sm-12 col-md-12 mix general">
                            {!! $cantidadEvaluacionesCursoCharts1->container() !!}
                        </div>
                    @endif

                    @if(!empty($cantidadEvaluacionesRechazadasCursoCharts1))
                        <div class="chartTarget col-xs-12 col-sm-12 col-md-12 mix general">
                            {!! $cantidadEvaluacionesRechazadasCursoCharts1->container() !!}
                        </div>
                    @endif

                    @if(!empty($promedioPonderacionCurso1))
                        <div class="chartTarget col-xs-12 col-sm-12 col-md-12 mix general">
                            {!! $promedioPonderacionCurso1->container() !!}
                        </div>
                    @endif


                    @foreach($periodos_collection as $periodo_index=>$periodo)
                        @if(!empty($cantidadEvaluacionesCursoCharts2[$periodo_index]))
                            <div class="chartTarget col-xs-12 col-sm-12 col-md-12 mix Periodo_{{$periodo->id}} general">
                                {!! $cantidadEvaluacionesCursoCharts2[$periodo_index]->container() !!}
                            </div>
                        @endif
                        @if(!empty($cantidadEvaluacionesRechazadasCursoCharts2[$periodo_index]))
                            <div class="chartTarget col-xs-12 col-sm-12 col-md-12 mix Periodo_{{$periodo->id}} general">
                                {!! $cantidadEvaluacionesRechazadasCursoCharts2[$periodo_index]->container() !!}
                            </div>
                        @endif
                        @if(!empty($promedioPonderacionCurso2[$periodo_index]))
                            <div class="chartTarget col-xs-12 col-sm-12 col-md-12 mix Periodo_{{$periodo->id}} general">
                                {!! $promedioPonderacionCurso2[$periodo_index]->container() !!}
                            </div>
                        @endif

                    @foreach($instrumentos_collection as $instrumento_index=>$instrumento)
                    @foreach($instrumento->categorias as $categoria_index=>$categoria)
                        @php $categoriaMedible = $categoria->esMedible();@endphp
                    @foreach($categoria->indicadores as $indicador_index=>$indicador)
                        @if(!empty($indicadores_collection_charts[$periodo_index][$instrumento_index][$categoria_index][$indicador_index]))
                        @if($indicador->esMedible() && $categoriaMedible)
                            <div class="chartTarget col-xs-12 col-sm-12 col-md-6 mix Periodo_{{$periodo->id}} Instrumento_{{$instrumento->id}} Categoria_{{$categoria->id}} Indicador_{{$indicador->id}} datos_evaluacion">
                                {!! $indicadores_collection_charts[$periodo_index][$instrumento_index][$categoria_index][$indicador_index]->container() !!}
                            </div>
                        @else
                            <div class="chartTarget col-md-12 mix Periodo_{{$periodo->id}} Instrumento_{{$instrumento->id}} Categoria_{{$categoria->id}} Indicador_{{$indicador->id}} datos_perfil">
                                <div class="tabla" style="background:white;">
                                    <div class="indicador_title highcharts-title" >
                                        <tspan>Respuestas del indicador: {{$indicador->nombre}}<br>Del Instrumento: {{$instrumento->nombre}}<br>En el periodo lectivo: {{$periodo->nombre}}</tspan>
                                    </div>
                                    <div class="indicador_subtitle" >{{$dashboards_subtitle}}</div>
                                    <table id="Periodo_{{$periodo->id}}Instrumento_{{$instrumento->id}}Categoria_{{$categoria->id}}Indicador_{{$indicador->id}}" class="table table-hover table-condensed">
                                        <thead>
                                        <tr>
                                            <th style="color: #333333;font-size: 18px;fill: #333333;">{{$indicador->getNombre()}}</th>
                                        </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        @endif
                        @endif
                    @endforeach
                    @endforeach
                    @endforeach
                    @endforeach
                </div>
            </section>
			
            
			<div class="cd-fail-message">No se encontraron resultados</div>
		</section> <!-- cd-gallery -->

        <!-- cd-filter -->
		<div class="cd-filter">
			<form>
                <!-- CSRF TOKEN -->
                {{ csrf_field() }}
				<div class="cd-filter-block">
					<h4>Buscar</h4>
					
					<div class="cd-filter-content">
						<input type="search" placeholder="Buscar...">
					</div> <!-- cd-filter-content -->
				</div> <!-- cd-filter-block -->


                <div class="cd-filter-block">
					<h4>Instrumentos</h4>
					
					<div class="cd-filter-content">
						<div class="cd-select cd-filters">
							<select class="filter" name="selectThis" id="selectThis">
                                <option value="">Todos</option>
                                @foreach($instrumentos_collection as $instrumento_index=>$instrumento)
                                    <option value=".Instrumento_{{$instrumento->id}} ">{{$instrumento->nombre}}</option>
                                @endforeach
							</select>
						</div> <!-- cd-select -->
					</div> <!-- cd-filter-content -->
				</div> <!-- cd-filter-block -->

                
                <div class="cd-filter-block">
					<h4>Datos de Perfil</h4>
					
					<div class="cd-filter-content">
						<div class="cd-select cd-filters">
							<select class="filter" name="selectThis" id="selectThis">
                                <option value="">Todos</option>
                                <option value=".datos_evaluacion">Mostrar sólo indicadores de la evaluación</option>
                                <option value=".datos_perfil">Mostrar sólo datos de perfil</option>
							</select>
						</div> <!-- cd-select -->
					</div> <!-- cd-filter-content -->
				</div> <!-- cd-filter-block -->
                


				<div class="cd-filter-block">
					<h4>Categorías de los Instrumentos</h4>

					<ul class="cd-filter-content cd-filters list">
                        @foreach($categorias_collection as $categoria_index=>$categoria)
                            <li>
                                <input class="filter" data-filter=".Categoria_{{$categoria->id}}" type="checkbox" id="Categoria_{{$categoria->id}}">
                                <label class="checkbox-label" for="Categoria_{{$categoria->id}}">{{$categoria->nombre}}</label>
                            </li>
                        @endforeach
					</ul> <!-- cd-filter-content -->
				</div> <!-- cd-filter-block -->

                <div class="cd-filter-block">
					<h4>Indicadores de los Instrumentos</h4>

					<ul class="cd-filter-content cd-filters list">
                        @foreach($indicadores_collection as $indicador_index=>$indicador)
                            <li>
                                <input class="filter indicadores_filter" name="indicadores[]" value="{{$indicador->id}}" data-filter=".Indicador_{{$indicador->id}}" type="checkbox" id="Indicador_{{$indicador->id}}">
                                <label class="checkbox-label" for="Indicador_{{$indicador->id}}">{{$indicador->nombre}}</label>
                            </li>
                        @endforeach            
					</ul> <!-- cd-filter-content -->
                </div> <!-- cd-filter-block -->

				
			</form>

			<a href="#0" class="cd-close">Cerrar</a>
		</div> <!-- cd-filter -->

		<a href="#0" class="cd-filter-trigger">Filtros</a>
    </main> <!-- cd-main-content -->
    
@stop


@section('css')
    <link rel="stylesheet" href="/content-filter/css/reset.css"> <!-- CSS reset -->
    <link rel="stylesheet" href="/content-filter/css/style.css"> <!-- Resource style -->
    <link rel="stylesheet" href="/css/user_list.css">
    <style>
        
    </style>
@stop

@section('javascript')
    <!-- ChartJS 
    <script src="/js/chart.js@2.8.0.js"></script>-->
    <!-- HighCharts -->
    <script src="/Highcharts-7.2.0/highcharts.js"></script>
    <script src="/Highcharts-7.2.0/modules/exporting.js"></script>
    <script src="/Highcharts-7.2.0/modules/export-data.js"></script>
    <!-- eCharts 
    <script src="/eCharts/echarts.min.js"></script>-->

    <!-- Content Filter-->
    <script src="/content-filter/js/modernizr.js"></script> <!-- Modernizr -->
    <script src="/content-filter/js/jquery.mixitup.min.js"></script>
    <script src="/content-filter/js/main.js"></script> <!-- Resource jQuery -->

    
    @if(!empty($cantidadEvaluacionesCursoCharts1))
        {!! $cantidadEvaluacionesCursoCharts1->script() !!}
    @endif
    @if(!empty($cantidadEvaluacionesCursoCharts1))
        {!! $cantidadEvaluacionesRechazadasCursoCharts1->script() !!}
    @endif
    @if(!empty($promedioPonderacionCurso1))
        {!! $promedioPonderacionCurso1->script() !!}
    @endif

    @foreach($periodos_collection as $periodo_index=>$periodo)

        @if(!empty($cantidadEvaluacionesCursoCharts2[$periodo_index]))
            {!! $cantidadEvaluacionesCursoCharts2[$periodo_index]->script() !!}
        @endif
        @if(!empty($cantidadEvaluacionesRechazadasCursoCharts2[$periodo_index]))
            {!! $cantidadEvaluacionesRechazadasCursoCharts2[$periodo_index]->script() !!}
        @endif
        @if(!empty($promedioPonderacionCurso2[$periodo_index]))
            {!! $promedioPonderacionCurso2[$periodo_index]->script() !!}
        @endif

    @foreach($instrumentos_collection as $instrumento_index=>$instrumento)
    @foreach($instrumento->categorias as $categoria_index=>$categoria)
        @php $categoriaMedible = $categoria->esMedible();@endphp
    @foreach($categoria->indicadores as $indicador_index=>$indicador)
        @if(!empty($indicadores_collection_charts[$periodo_index][$instrumento_index][$categoria_index][$indicador_index]))
        @if($indicador->esMedible() && $categoriaMedible)
            {!! $indicadores_collection_charts[$periodo_index][$instrumento_index][$categoria_index][$indicador_index]->script() !!}
        @else
            <script>
                $(document).ready(function () {
                    var table = $('#Periodo_{{$periodo->id}}Instrumento_{{$instrumento->id}}Categoria_{{$categoria->id}}Indicador_{{$indicador->id}}').DataTable({
                            "processing": true,
                            "serverSide": true,
                            "ajax": "{{ route('curso.consultar_tabla_indicador', ['curso_id' => $curso->getID(), 'periodo_id' => $periodo->getID(), 'instrumento_id' => $instrumento->getID(), 'categoria_id' => $categoria->getID(), 'indicador_id' => $indicador->getID()]) }}",
                            "columns": [
                                {data: 'value_string', name: 'value_string'},
                                
                            ],
                            "language" : 
                                {"sProcessing":     "Procesando...",
                                "sLengthMenu":     "Mostrar _MENU_ registros",
                                "sZeroRecords":    "No se encontraron resultados",
                                "sEmptyTable":     "Ningún dato disponible en esta tabla =(",
                                "sInfo":           "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                                "sInfoEmpty":      "Mostrando registros del 0 al 0 de un total de 0 registros",
                                "sInfoFiltered":   "(filtrado de un total de _MAX_ registros)",
                                "sInfoPostFix":    "",
                                "sSearch":         "Buscar:",
                                "sUrl":            "",
                                "sInfoThousands":  ",",
                                "sLoadingRecords": "Cargando...",
                                "oPaginate": {
                                    "sFirst":    "Primero",
                                    "sLast":     "Último",
                                    "sNext":     "Siguiente",
                                    "sPrevious": "Anterior"
                                },
                                "oAria": {
                                    "sSortAscending":  ": Activar para ordenar la columna de manera ascendente",
                                    "sSortDescending": ": Activar para ordenar la columna de manera descendente"
                                },
                                "buttons": {
                                    "copy": "Copiar",
                                    "colvis": "Visibilidad"
                                }},
                        });
                });
            </script>
        @endif
        @endif
    @endforeach
    @endforeach
    @endforeach
    @endforeach
    
    <script>
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
