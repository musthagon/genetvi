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
            <div class="col-md-6">
                <div class="panel panel-bordered">
                    <canvas id="pieChart"></canvas>
                </div>
            </div>

            <div class="col-md-6">
                <div class="panel panel-bordered">
                    <canvas id="pieChart2"></canvas>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div id="indicador1" ></div>
            </div>
            <div class="col-md-6">
                {!! $chart[0]->container() !!}
            </div>
        </div>

        <div class="row">
            
           
            <div class="col-md-6">
                {!! $IndicadoresCharts[0][0][0][0]->container() !!}
            </div>

        </div>

        @foreach($periodos_curso as $periodo_index=>$periodo)
        @foreach($instrumentos_curso as $instrumento_index=>$instrumento)
        <?php $instrumento = App\Instrumento::find($instrumento->instrumento_id);?>
        @if(!empty($instrumento))
        @foreach($instrumento->categorias as $categoria_index=>$categoria)
        <div class="row">
        @foreach($categoria->indicadores as $indicador_index=>$indicador)
            <div class="col-md-6">
                {!! $IndicadoresCharts[$periodo_index][$instrumento_index][$categoria_index][$indicador_index]->container() !!}
            </div>
        @endforeach
        </div>
        @endforeach
        @endif
        @endforeach
        @endforeach


    </div>

@stop

@section('css')
    <style>

    </style>
@stop

@section('javascript')
    <!-- ChartJS -->
    <script src="/js/chart.js@2.8.0.js"></script>
    <script src="/Highcharts-7.2.0/highcharts.js"></script>
    <script src="/Highcharts-7.2.0/modules/exporting.js"></script>
    <script src="/Highcharts-7.2.0/modules/export-data.js"></script>
    
    @foreach($periodos_curso as $periodo_index=>$periodo)
    @foreach($instrumentos_curso as $instrumento_index=>$instrumento)
    <?php $instrumento = App\Instrumento::find($instrumento->instrumento_id);?>
    @if(!empty($instrumento))
    @foreach($instrumento->categorias as $categoria_index=>$categoria)
    @foreach($categoria->indicadores as $indicador_index=>$indicador)
        {!! $IndicadoresCharts[$periodo_index][$instrumento_index][$categoria_index][$indicador_index]->script() !!}
    @endforeach
    @endforeach
    @endif
    @endforeach
    @endforeach
    
    


    {!! $chart[0]->script() !!}

    <script>
        $(document).ready(function () {
            
            //-------------
            //- PIE CHART -
            //-------------
            var ctx = document.getElementById('pieChart').getContext('2d');
            var chart = new Chart(ctx, {
                // The type of chart we want to create
                type: 'pie',

                // The data for our dataset
                data: {
                    labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July'],

                    datasets: [{
                        label: 'My First dataset',
                        backgroundColor: 'rgb(255, 99, 132)',
                        borderColor: 'rgb(255, 99, 132)',
                        data: [0, 10, 5, 2, 20, 30, 45]
                    }]

                },

                // Configuration options go here
                options: {}
            });

            //-------------
            //- PIE CHART -
            //-------------
            var ctx = document.getElementById('pieChart2').getContext('2d');
            var chart = new Chart(ctx, {
                // The type of chart we want to create
                type: 'pie',

                // The data for our dataset
                data: {
                    labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July'],

                    datasets: [{
                        label: 'My First dataset',
                        backgroundColor: 'rgb(255, 99, 132)',
                        borderColor: 'rgb(255, 99, 132)',
                        data: [0, 10, 5, 2, 20, 30, 45]
                    }]

                },

                // Configuration options go here
                options: {}
            });

            //-------------
            //- Indicador1 -
            //-------------
            Highcharts.chart('indicador1', {
                chart: {
                    plotBackgroundColor: null,
                    plotBorderWidth: null,
                    plotShadow: false,
                    type: 'pie'
                },
                title: {
                    text: 'Browser market shares in January, 2018'
                },
                tooltip: {
                    pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
                },
                plotOptions: {
                    pie: {
                        allowPointSelect: true,
                        cursor: 'pointer',
                        dataLabels: {
                            enabled: true,
                            format: '<b>{point.name}</b>: {point.percentage:.1f} %'
                        }
                    }
                },
                series: [{
                    name: 'Brands',
                    colorByPoint: true,
                    data: [{
                        name: 'Chrome',
                        y: 61.41
                    }, {
                        name: 'Internet Explorer',
                        y: 11.84
                    }, {
                        name: 'Firefox',
                        y: 10.85
                    }, {
                        name: 'Edge',
                        y: 4.67
                    }, {
                        name: 'Safari',
                        y: 4.18
                    }, {
                        name: 'Sogou Explorer',
                        y: 1.64
                    }, {
                        name: 'Opera',
                        y: 1.6
                    }, {
                        name: 'QQ',
                        y: 1.2
                    }, {
                        name: 'Other',
                        y: 2.61
                    }]
                }]
            });
        });
    </script>
@stop
