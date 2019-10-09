@extends('layouts.users')

@section('content')

<!-- Main content -->
<section class="content">
  <div class="row">

    
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
                            $periodo_string = str_replace(' ', '_', $periodo->nombre);
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
                    @foreach($instrumento->categorias as $categoria_index=>$categoria)
                    @foreach($categoria->indicadores as $indicador_index=>$indicador)
                        @php
                            $periodo_string = str_replace(' ', '_', $periodo->nombre);
                            $instrumento_string = str_replace(' ', '_', $instrumento->nombre);
                            $categoria_string = str_replace(' ', '_', $categoria->nombre);
                            $indicador_string = str_replace(' ', '_', $indicador->nombre);
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

                            <table id="cursos-data-table" class="table table-bordered table-hover">
                                <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Descripción</th>
                                    <th>Acciones</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($participantes as $participante)
                                    <tr>
                                    <td>{{$participante['fullname']}}</td>
                                    <td>1</td>
                                    
                                    <td>                                        
                                        2
                                    </td>
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
                                    $instrumento_string = str_replace(' ', '_', $instrumento->nombre);
                                @endphp
                                @if(!empty($instrumento))
                                <option value=".{{$instrumento_string}} ">{{$instrumento->nombre}}</option>
                                @endif
                                @endforeach
							</select>
						</div> <!-- cd-select -->
					</div> <!-- cd-filter-content -->
				</div> <!-- cd-filter-block -->

				
			</form>

			<a href="#0" class="cd-close">Cerrar</a>
		</div> <!-- cd-filter -->

		<a href="#0" class="cd-filter-trigger">Filtros</a>
  </main> <!-- cd-main-content -->



  </div>
  
</section>

@stop

@section('css')
  <link rel="stylesheet" href="/content-filter/css/reset.css"> <!-- CSS reset -->
  <link rel="stylesheet" href="/content-filter/css/style.css"> <!-- Resource style -->

  <link rel="stylesheet" href="/css/linkert-table.css">
  <link rel="stylesheet" href="/adminlte/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">
@stop

@section('javascript')
  


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
    
 
<!-- DataTables -->
  <script src="/adminlte/bower_components/datatables.net/js/jquery.dataTables.min.js"></script>   
  <script src="/adminlte/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
  <!-- SlimScroll -->
  <script src="/adminlte/bower_components/jquery-slimscroll/jquery.slimscroll.min.js"></script>
  <!-- FastClick -->
  <script src="/adminlte/bower_components/fastclick/lib/fastclick.js"></script>
  <script>
    $(function (){
      
      $('#participantes-data-table, #cursos-data-table2').DataTable({
        'paging'      : false,
        'lengthChange': false,
        'searching'   : false,
        'ordering'    : true,
        'info'        : false,
        'autoWidth'   : false
      })

    });
  </script>


@stop
