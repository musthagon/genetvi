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
    
    @if(!empty($indicadores_collection_charts[$periodo_index][$instrumento_index][$categoria_index][$indicador_index]) && $indicador->esMedible() && $categoriaMedible)
        {!! $indicadores_collection_charts[$periodo_index][$instrumento_index][$categoria_index][$indicador_index]->script() !!}
    @elseif(!$indicador->esMedible())
        <script>
            $(document).ready(function () {
                var table = $('#Periodo_{{$periodo->getID()}}Instrumento_{{$instrumento->getID()}}Categoria_{{$categoria->getID()}}Indicador_{{$indicador->getID()}}').DataTable({
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
                        curso_id: {{$curso->getID()}},
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