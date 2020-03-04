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
                        Los coordinadores tienen acceso a este panel administrativo con el objetivo de hacer seguimiento el proceso de evaluación de los EVA de cada una de sus dependencias, siguiendo los siguientes pasos:
                        <ol>
                            <li>Verificar que se configuró el periodo lectivo de su dependencia. En la sección de <a href="{{route('voyager.periodos-lectivos.index')}}" target="_blank">/Evaluación/Periodos Lectivos</a></li>
                            <li>Sincronizar las categorias y cursos que se van a evaluar. En la sección de <a href="{{route('gestion.evaluaciones')}}" target="_blank">Cursos del CVUCV</a> </li>
                            <li>Configurar el periodo lectivo y los instrumentos de evaluación a usar. En la sección de <a href="{{route('gestion.evaluaciones')}}" target="_blank">Cursos del CVUCV</a></li>
                            <li>Por último, iniciar la evaluación de los cursos. En la sección de <a href="{{route('gestion.evaluaciones')}}" target="_blank">Cursos del CVUCV dentro de las categorías específicas que se desean evaluar</a></li>
                        </ol>
                    </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header" id="card-2">
                    <h5 class="mb-0">
                        <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapse-card-2" aria-expanded="false" aria-controls="collapse-card-2">
                        Cualquier duda adicional
                        </button>
                    </h5>
                    </div>
                    <div id="collapse-card-2" class="collapse" aria-labelledby="card-2" data-parent="#accordion">
                    <div class="card-body">
                        Contáctar a la Gerencia del SEDUCV, correo electrónico: seducv@gmail.com Teléfonos: 0212-605-45-86
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

    
@stop
