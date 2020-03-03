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
                        <li class="filter" data-filter=".Periodo_{{$periodo->getID()}}">
                            <a href="#0" class="" data-type="Periodo_{{$periodo->getID()}}">
                                {{$periodo->getNombre()}}
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
                    

                    @if( !empty($indicadores_collection_charts[$periodo_index][$instrumento_index][$categoria_index][$indicador_index]) && $indicador->esMedible() && $categoriaMedible)
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
    