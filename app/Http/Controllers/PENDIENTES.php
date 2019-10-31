
    @foreach($periodos_collection as $periodo_index=>$periodo)
                    @if(!empty($periodo))
                    @foreach($instrumentos_collection as $instrumento_index=>$instrumento)
                    @if(!empty($instrumento))
                    @foreach($instrumento->categorias as $categoria_index=>$categoria)
                    @foreach($categoria->indicadores as $indicador_index=>$indicador)
                        <div class="chartTarget col-md-6 mix Periodo_{{$periodo->id}} Instrumento_{{$instrumento->id}} Categoria_{{$categoria->id}} Indicador_{{$indicador->id}}">
                            {!! $IndicadoresCharts[$periodo_index][$instrumento_index][$categoria_index][$indicador_index]->container() !!}
                        </div>
                    @endforeach
                    @endforeach
                    @endif
                    @endforeach
                    @endif
                    @endforeach
    
    
    
    
    
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