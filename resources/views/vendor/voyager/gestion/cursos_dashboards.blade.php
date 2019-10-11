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

    <main class="cd-main-content">
		<div class="cd-tab-filter-wrapper">
			<div class="cd-tab-filter">
				<ul class="cd-filters">
					<li class="placeholder"> 
						<a data-type="all" href="#0">Todos</a> <!-- selected option on mobile -->
					</li> 
                    <li class="filter"><a class="selected" href="#0" data-type="all">Todos</a></li>
                    
                    @foreach($periodos_collection as $periodo_index=>$periodo)
                    @if(!empty($periodo))
                        @php
                            $periodo_string     = "Periodo_".$periodo->id;
                        @endphp
                        <li class="filter" data-filter=".{{$periodo_string}}"><a href="#0" data-type="{{$periodo_string}}">{{$periodo->nombre}}</a></li>
                    @endif
                    @endforeach
                    <li class="filter" data-filter=".general"><a href="#0" data-type="general">Otros</a></li>
                    <li class="filter" data-filter=".participantes"><a href="#0" data-type="participantes">Participantes</a></li>
					
				</ul> <!-- cd-filters -->
			</div> <!-- cd-tab-filter -->
		</div> <!-- cd-tab-filter-wrapper -->

		<section class="cd-gallery">
            <section class="page-content browse container-fluid ">
                <div class="row">
                    @if(!empty($cantidadEvaluacionesCursoCharts))
                    <div class="chartTarget col-md-12 mix general">
                        {!! $cantidadEvaluacionesCursoCharts->container() !!}
                    </div>
                    @endif
                    @if(!empty($promedioPonderacionCurso))
                    <div class="chartTarget col-md-12 mix general">
                        {!! $promedioPonderacionCurso->container() !!}
                    </div>
                    @endif

                @foreach($periodos_collection as $periodo_index=>$periodo)
                @if(!empty($periodo))
                @foreach($instrumentos_collection as $instrumento_index=>$instrumento)
                @if(!empty($instrumento))
                
                        @php
                            
                            $periodo_string     = "Periodo_".$periodo->id;
                            $instrumento_string = "Instrumento_".$instrumento->id;

                        @endphp

                        @if(!empty($listadoParticipantesRevisores[$periodo_index][$instrumento_index]))
                            <div class="chartTarget col-xs-12 mix {{$periodo_string}} {{$instrumento_string}} revisores">
                            <div class="box">
                                <div class="box-header">
                                <h3 class="box-title">Revisores del curso <br>Del Instrumento {{$instrumento->nombre}}<br>En el periodo lectivo {{$periodo->nombre}}</h3>
                                </div>
                                <!-- /.box-header -->
                                <div class="box-body">

                                <table id="revisores-data-table" class="table table-bordered table-hover">
                                    <thead>
                                    <tr>
                                        <th>Nombre</th>
                                        <th>Correo</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    

                                    @foreach($listadoParticipantesRevisores[$periodo_index][$instrumento_index] as $revisorIndex=>$revisor)
                                        <tr>
                                        <td>
                                            
                                            <a href="{{env('CVUCV_GET_SITE_URL','https://campusvirtual.ucv.ve')}}/user/view.php?id={{$revisor->cvucv_id}}&course={{$curso->id}}" target="_blank">
                                                <div class="pull-left image">

                                                    @if( strpos( $revisor->avatar, env('CVUCV_GET_WEBSERVICE_ENDPOINT1', setting('site.CVUCV_GET_WEBSERVICE_ENDPOINT1')) ) !== false )
                                                        <img src="{{env('CVUCV_GET_WEBSERVICE_ENDPOINT2',setting('site.CVUCV_GET_WEBSERVICE_ENDPOINT2'))}}/{{strtok($revisor->avatar, env('CVUCV_GET_WEBSERVICE_ENDPOINT1', setting('site.CVUCV_GET_WEBSERVICE_ENDPOINT1')))}}/user/icon/f1?token={{env('CVUCV_ADMIN_TOKEN',setting('site.CVUCV_ADMIN_TOKEN'))}}" class="img-circle" alt="User Image"> 
                                                    @else
                                                        <img src="{{$revisor->avatar}}" class="img-circle" alt="User Image">
                                                    @endif

                                                </div>{{$revisor->name}}
                                            </a>
                                        </td>
                                        <td>{{$revisor->email}}</td>                              
                                        </tr>
                                    @endforeach
                                      
                                </table>

                                </div><!-- /.box-body -->
                            </div><!-- /.box -->
                            </div>
                        @endif

                @foreach($instrumento->categorias as $categoria_index=>$categoria)
                @foreach($categoria->indicadores as $indicador_index=>$indicador)
                    @php
                        
                        $categoria_string   = "Categoria_".$categoria->id;
                        $indicador_string   = "Indicador_".$indicador->id;

                    @endphp

                    <div class="chartTarget col-md-6 mix {{$periodo_string}} {{$instrumento_string}} {{$categoria_string}} {{$indicador_string}}">
                        {!! $IndicadoresCharts[$periodo_index][$instrumento_index][$categoria_index][$indicador_index]->container() !!}
                    </div>
                @endforeach
                @endforeach
                @endif
                @endforeach
                @endif
                @endforeach

                @if(!empty($participantes))
                        <div class="chartTarget col-xs-12 mix participantes">
                        <div class="box">
                            <div class="box-header">
                            <h3 class="box-title">Participantes del curso</h3>
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body">

                            <table id="participantes-data-table" class="table table-bordered table-hover">
                                <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Correo</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($participantes as $participante)
                                    <tr>
                                    <td>
                                        <a href="{{env('CVUCV_GET_SITE_URL','https://campusvirtual.ucv.ve')}}/user/view.php?id={{$participante['id']}}&course={{$curso->id}}" target="_blank">
                                            <div class="pull-left image">

                                                @if( strpos( $participante['profileimageurlsmall'], env('CVUCV_GET_WEBSERVICE_ENDPOINT1', setting('site.CVUCV_GET_WEBSERVICE_ENDPOINT1')) ) !== false )
                                                    <img src="{{env('CVUCV_GET_WEBSERVICE_ENDPOINT2',setting('site.CVUCV_GET_WEBSERVICE_ENDPOINT2'))}}/{{strtok($participante['profileimageurlsmall'], env('CVUCV_GET_WEBSERVICE_ENDPOINT1', setting('site.CVUCV_GET_WEBSERVICE_ENDPOINT1')))}}/user/icon/f1?token={{env('CVUCV_ADMIN_TOKEN',setting('site.CVUCV_ADMIN_TOKEN'))}}" class="img-circle" alt="User Image"> 
                                                @else
                                                    <img src="{{$participante['profileimageurl']}}" class="img-circle" alt="User Image">
                                                @endif

                                            </div>{{$participante['fullname']}}
                                        </a>
                                    </td>
                                    <td>{{$participante['email']}}</td>                              
                                    </tr>
                                @endforeach
                                    
                            </table>

                            </div><!-- /.box-body -->
                        </div><!-- /.box -->
                        </div>
                    @endif
                </div>
            </section>
			
            
			<div class="cd-fail-message">No se encontraron resultados</div>
		</section> <!-- cd-gallery -->

		<div class="cd-filter">
			<form>
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
                                @php
                                    
                                    $instrumento_string = "Instrumento_".$instrumento->id;

                                @endphp
                                @if(!empty($instrumento))
                                <option value=".{{$instrumento_string}} ">{{$instrumento->nombre}}</option>
                                @endif
                                @endforeach
							</select>
						</div> <!-- cd-select -->
					</div> <!-- cd-filter-content -->
				</div> <!-- cd-filter-block -->

                <div class="cd-filter-block">
					<h4>Revisores</h4>

					<ul class="cd-filter-content cd-filters list">
						<li>
							<input class="filter" data-filter="" type="radio" name="radioButton" id="radio1" checked>
							<label class="radio-label" for="radio1">Todos</label>
						</li>

						<li>
							<input class="filter" data-filter=".revisores" type="radio" name="radioButton" id="revisores">
							<label class="radio-label" for="revisores">Revisores</label>
						</li>
					</ul> <!-- cd-filter-content -->
				</div> <!-- cd-filter-block -->
				
				<div class="cd-filter-block">
					<h4>Categorías de los Instrumentos</h4>

					<ul class="cd-filter-content cd-filters list">
						

                        @foreach($categorias_collection as $categoria_index=>$categoria)
                    
                            @php
                                $categoria_string   = "Categoria_".$categoria->id;
                            @endphp

                            <li>
                                <input class="filter" data-filter=".{{$categoria_string}}" type="checkbox" id="{{$categoria_string}}">
                                <label class="checkbox-label" for="{{$categoria_string}}">{{$categoria->nombre}}</label>
                            </li>

                        @endforeach

					</ul> <!-- cd-filter-content -->
				</div> <!-- cd-filter-block -->

                <div class="cd-filter-block">
					<h4>Indicadores de los Instrumentos</h4>

					<ul class="cd-filter-content cd-filters list">
						              

                        @foreach($indicadores_collection as $indicador_index=>$indicador)
                            
                            @php
                                $indicador_string   = "Indicador_".$indicador->id;
                            @endphp
                            
                            <li>
                                <input class="filter" data-filter=".{{$indicador_string}}" type="checkbox" id="{{$indicador_string}}">
                                <label class="checkbox-label" for="{{$indicador_string}}">{{$indicador->nombre}}</label>
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
    <style>

    </style>
@stop

@section('javascript')
    <!-- ChartJS -->
    <script src="/js/chart.js@2.8.0.js"></script>
    <!-- HighCharts -->
    <script src="/Highcharts-7.2.0/highcharts.js"></script>
    <script src="/Highcharts-7.2.0/modules/exporting.js"></script>
    <script src="/Highcharts-7.2.0/modules/export-data.js"></script>
    <!-- Content Filter-->
    <script src="/content-filter/js/modernizr.js"></script> <!-- Modernizr -->
    <script src="/content-filter/js/jquery.mixitup.min.js"></script>
    <script src="/content-filter/js/main.js"></script> <!-- Resource jQuery -->

    @if(!empty($cantidadEvaluacionesCursoCharts))
    {!! $cantidadEvaluacionesCursoCharts->script() !!}
    @endif
    @if(!empty($promedioPonderacionCurso))
    {!! $promedioPonderacionCurso->script() !!}
    @endif

    @foreach($periodos_collection as $periodo_index=>$periodo)
    @if(!empty($periodo))
    @foreach($instrumentos_collection as $instrumento_index=>$instrumento)
    @if(!empty($instrumento))
    @foreach($instrumento->categorias as $categoria_index=>$categoria)
    @foreach($categoria->indicadores as $indicador_index=>$indicador)
        {!! $IndicadoresCharts[$periodo_index][$instrumento_index][$categoria_index][$indicador_index]->script() !!}
    @endforeach
    @endforeach
    @endif
    @endforeach
    @endif
    @endforeach
    
 

    <script>
        $(document).ready(function () {
            
        });
    </script>
@stop
