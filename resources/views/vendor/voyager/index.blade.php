@extends('voyager::master')

@section('css')
    <style>
        .panel.widget p {
            max-height: inherit; 
        }
    </style>
@stop

@section('content')
    <div class="page-content">
        @include('voyager::alerts')

        @include('voyager::dimmers')

        <div class="analytics-container">
            

            <div id="accordion">
                <div class="card">
                    <div class="card-header" id="card-1">
                    <h5 class="mb-0">
                        <button class="btn btn-link" data-toggle="collapse" data-target="#collapse-card-1" aria-expanded="true" aria-controls="collapse-card-1">
                        *Bienvenido al Panel Administrativo Genetvi
                        </button>
                    </h5>
                    </div>

                    <div id="collapse-card-1" class="collapse show" aria-labelledby="card-1" data-parent="#accordion">
                    <div class="card-body">
                        Los Coordinadores EaD de la UCV pueden acceder al panel administrativo con el objetivo de hacer seguimiento al proceso de evaluación de los EVA de cada una de sus facultades o dependencias, siguiendo los siguientes pasos:
                        <ol>
                            <li>Configurar el periodo lectivo de su facultad o dependencia. En la sección de <a href="{{route('voyager.periodos-lectivos.index')}}" target="_blank">/Evaluación/Periodos Lectivos</a></li>
                            <li>Sincronizar las categorías y/o cursos que se van a evaluar en el periodo lectivo previamente seleccionado. Navegando por cada una de las categorías dentro de su facultad o dependencia en la sección de <a href="{{route('gestion.evaluaciones')}}" target="_blank">Cursos del CV-UCV</a> </li>
                            <li>Por último, buscar los respectivos cursos a evaluar y activar la función “ iniciar la evaluación”. Navegando por cada una de las categorías dentro de su facultad o dependencia en la sección de <a href="{{route('gestion.evaluaciones')}}" target="_blank">Cursos del CV-UCV</a></li>
                        </ol>

                        <div class="container-wrapper-genially" style="position: relative; min-height: 400px; max-width: 100%;">
                            <img src="https://genially.blob.core.windows.net/genially/version3.0/loader.gif" class="loader-genially" style="position: absolute; top: 0; right: 0; bottom: 0; left: 0; margin-top: auto; margin-right: auto; margin-bottom: auto; margin-left: auto; z-index: 1;width: 80px; height: 80px;"/>
                            <div id="5e780ea82dc19c0de00776dd" class="genially-embed" style="margin: 0px auto; position: relative; height: auto; width: 100%;"></div>
                        </div> 
                        
                    </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header" id="card-2">
                    <h5 class="mb-0">
                        <button class="btn btn-link" data-toggle="collapse" data-target="#collapse-card-2" aria-expanded="true" aria-controls="collapse-card-2">
                        Cualquier duda adicional
                        </button>
                    </h5>
                    </div>
                    <div id="collapse-card-2" class="collapse show" aria-labelledby="card-2" data-parent="#accordion">
                    <div class="card-body">
                        Contactar a la Gerencia del SEDUCV, correo electrónico: seducv@gmail.com Teléfonos: 0212-605-45-86
                    </div>
                    </div>
                </div>


            </div>



            <div class="Dashboard Dashboard--full" id="analytics-dashboard">
                <header class="Dashboard-header">
                    <ul class="FlexGrid">
                        <li class="FlexGrid-item">
                            <div class="Titles">
                                <h1 class="Titles-main" id="view-name">{{ __('voyager::analytics.select_view') }}</h1>
                                <div class="Titles-sub">{{ __('voyager::analytics.various_visualizations') }}</div>
                            </div>
                        </li>
                        <li class="FlexGrid-item FlexGrid-item--fixed">
                            <div id="active-users-container"></div>
                        </li>
                    </ul>
                    <div id="view-selector-container"></div>
                </header>

                <ul class="FlexGrid FlexGrid--halves">
                    <li class="FlexGrid-item">
                        <div class="Chartjs">
                            <header class="Titles">
                                <h1 class="Titles-main">{{ __('voyager::analytics.this_vs_last_week') }}</h1>
                                <div class="Titles-sub">{{ __('voyager::analytics.by_users') }}</div>
                            </header>
                            <figure class="Chartjs-figure" id="chart-1-container"></figure>
                            <ol class="Chartjs-legend" id="legend-1-container"></ol>
                        </div>
                    </li>
                    <li class="FlexGrid-item">
                        <div class="Chartjs">
                            <header class="Titles">
                                <h1 class="Titles-main">{{ __('voyager::analytics.this_vs_last_year') }}</h1>
                                <div class="Titles-sub">{{ __('voyager::analytics.by_users') }}</div>
                            </header>
                            <figure class="Chartjs-figure" id="chart-2-container"></figure>
                            <ol class="Chartjs-legend" id="legend-2-container"></ol>
                        </div>
                    </li>
                    <li class="FlexGrid-item">
                        <div class="Chartjs">
                            <header class="Titles">
                                <h1 class="Titles-main">{{ __('voyager::analytics.top_browsers') }}</h1>
                                <div class="Titles-sub">{{ __('voyager::analytics.by_pageview') }}</div>
                            </header>
                            <figure class="Chartjs-figure" id="chart-3-container"></figure>
                            <ol class="Chartjs-legend" id="legend-3-container"></ol>
                        </div>
                    </li>
                    <li class="FlexGrid-item">
                        <div class="Chartjs">
                            <header class="Titles">
                                <h1 class="Titles-main">{{ __('voyager::analytics.top_countries') }}</h1>
                                <div class="Titles-sub">{{ __('voyager::analytics.by_sessions') }}</div>
                            </header>
                            <figure class="Chartjs-figure" id="chart-4-container"></figure>
                            <ol class="Chartjs-legend" id="legend-4-container"></ol>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
@stop

@section('javascript')
<script>(function (d) { var js, id = "genially-embed-js", ref = d.getElementsByTagName("script")[0]; if (d.getElementById(id)) { return; } js = d.createElement("script"); js.id = id; js.async = true; js.src = "https://view.genial.ly/static/embed/embed.js"; ref.parentNode.insertBefore(js, ref); }(document));</script> 
    
@stop
