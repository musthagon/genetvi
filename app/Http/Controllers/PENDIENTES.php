var table = $('#Indicador_{{$indicador->id}}').DataTable({
                        "processing": true,
                        "serverSide": true,
                        "ajax": "{{ route('curso.consultar_tabla_indicador', ['curso' => $curso->id, 'periodo' => $periodo->id, 'instrumento' => $instrumento->id, 'categoria' => $categoria->id, 'indicador' => $indicador->id]) }}",
                        "columns": [
                            {data: 'value_string', name: 'value_string'}
                        ]
                    });

                    <div class="chartTarget col-md-6 mix Periodo_{{$periodo->id}} Instrumento_{{$instrumento->id}} Categoria_{{$categoria->id}} Indicador_{{$indicador->id}} general">
                                <div class="indicador_title" style="color: #333333;font-size: 18px;fill: #333333;">{{$indicador->getNombre()}}</div>
                                <table id="Indicador_{{$indicador->id}}" class="table table-hover table-condensed">
                                    <thead>
                                    <tr>
                                        <th>{{$indicador->getNombre()}}</th>
                                    </tr>
                                    </thead>
                                </table>
                            </div>