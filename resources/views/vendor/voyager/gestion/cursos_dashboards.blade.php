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
                        <li class="filter" data-filter=".{{$periodo->nombre}}"><a href="#0" data-type="{{$periodo->nombre}}">{{$periodo->nombre}}</a></li>
                    @endif
                    @endforeach
                    <li class="filter" data-filter=".general"><a href="#0" data-type="general">Otros</a></li>
					
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
                @foreach($instrumento->categorias as $categoria_index=>$categoria)
                @foreach($categoria->indicadores as $indicador_index=>$indicador)
                    <div class="chartTarget col-md-6 mix {{$periodo->nombre}} {{$instrumento->nombre}} {{$categoria->nombre}} {{$indicador->nombre}}">
                        {!! $IndicadoresCharts[$periodo_index][$instrumento_index][$categoria_index][$indicador_index]->container() !!}
                    </div>
                @endforeach
                @endforeach
                @endif
                @endforeach
                @endif
                @endforeach
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
                                @if(!empty($instrumento))
                                <option value=".{{$instrumento->nombre}}">{{$instrumento->nombre}}</option>
                                @endif
                                @endforeach
							</select>
						</div> <!-- cd-select -->
					</div> <!-- cd-filter-content -->
				</div> <!-- cd-filter-block -->

				<div class="cd-filter-block">
					<h4>Categorías</h4>

					<ul class="cd-filter-content cd-filters list">
                        @foreach($instrumentos_collection as $instrumento_index=>$instrumento)
                        @if(!empty($instrumento))
                        @foreach($instrumento->categorias as $categoria_index=>$categoria)
                        <li>
							<input class="filter" data-filter=".{{$categoria->nombre}}" type="checkbox" id="{{$categoria->nombre}}">
			    			<label class="checkbox-label" for="{{$categoria->nombre}}">{{$categoria->nombre}}</label>
						</li>
                        @endforeach
                        @endif
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
