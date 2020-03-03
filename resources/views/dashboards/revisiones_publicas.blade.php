<div class="container-fluid white">
    <form class="form-edit-add" 
        action="{{ route($ruta_revisiones_publicas, ['categoria_id' => $curso->categoria, 'curso_id' => $curso->getID()]) }}" 
        method="GET">

        <div class="form-group  col-md-4 ">
            <label class="control-label" for="name">Periodo Lectivo</label>
            <select id="periodos_lectivos" class="form-control select2" name="periodo_lectivo" required>
                @foreach($periodos_collection as $periodo_index=>$periodo)
                    <option value="{{$periodo->getID()}}">{{$periodo->getNombre()}}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group  col-md-4 ">
            <label class="control-label" for="name">Momento de Evaluación</label>
            <select id="momentos_evaluacion" class="form-control select2" name="momento_evaluacion" required>
                @foreach($momentos_evaluacion_collection as $index=>$momento)
                    <option value="{{$momento->getID()}}">{{$momento->getNombre()}}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group  col-md-4 ">
            <label class="control-label" for="name">Instrumentos</label>
            <select id="instrumentos" class="form-control select2" name="instrumento" required>
                @foreach($instrumentos_collection2 as $instrumento_index=>$instrumento)
                    <option value="{{$instrumento->getID()}}">{{$instrumento->getNombre()}}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group  col-md-10 ">
            <label class="control-label" for="name">Seleccionar usuario</label>
            <select id="search_users" class="js-data-example-ajax form-control select2" name="user" required>
            </select>
        </div>
        <div class="form-group  col-md-2 " style="margin-top: 15px;">
            <button type="submit" class="btn btn-success btn-add-new">
                <i class="voyager-plus"></i> <span>Ir</span>
            </button>
        </div>
    </form>
</div>