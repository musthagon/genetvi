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
    <input id="clickMe" type="button" value="clickme" onclick="doFunction();" />
    <main class="cd-main-content">
		<div class="cd-tab-filter-wrapper">
			<div class="cd-tab-filter">
				<ul class="cd-filters">
					<li class="placeholder"> 
						<a data-type="all" href="#0">Todos</a> <!-- selected option on mobile -->
					</li> 
                    <li class="filter"><a class="selected" href="#0" data-type="all">Todos</a></li>
                    
                    
                    <li class="filter" data-filter=".general"><a href="#0" data-type="general">Otros</a></li>
                    <li class="filter" data-filter=".participantes"><a href="#0" data-type="participantes">Participantes</a></li>
					
				</ul> <!-- cd-filters -->
			</div> <!-- cd-tab-filter -->
		</div> <!-- cd-tab-filter-wrapper -->

		<section class="cd-gallery">
            <section class="page-content browse container-fluid ">
                <div class="row">

                    <div class="chartTarget col-md-12 mix general">
                        test
                        {!! $chart->container() !!}
                    </div>

                
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
                                
							</select>
						</div> <!-- cd-select -->
					</div> <!-- cd-filter-content -->
				</div> <!-- cd-filter-block -->

                
				
				<div class="cd-filter-block">
					<h4>Categorías de los Instrumentos</h4>

					<ul class="cd-filter-content cd-filters list">
						

                        

					</ul> <!-- cd-filter-content -->
				</div> <!-- cd-filter-block -->

                <div class="cd-filter-block">
					<h4>Indicadores de los Instrumentos</h4>

					<ul class="cd-filter-content cd-filters list">
						              


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

    
    
    

    <script>
        $(document).ready(function () {
            
            var table = $('#participantes-data-table').DataTable(
                );
            
            

            document.getElementById("clickMe").onclick = function () { 
                var original_api_url = {{ $chart->id }}_api_url;
                {{ $chart->id }}_refresh(original_api_url + "?date_start=2019-01-01&date_end=2019-12-31&number=8");
                /*alert('hello!');*/
             
            };
        });
    </script>

    {!! $chart->script() !!}

    <!-- Actualizamos gráficos -->
    
@stop
