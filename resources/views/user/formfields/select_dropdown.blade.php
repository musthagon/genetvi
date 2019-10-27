<div class="box-body">
    <div class="row">
        <div class="col-md-12">
            <div class="form-group2">
                <select class="form-control select2" 
                    style="width: 100%;" 
                    name="{{$indicador->getID()}}"
                    data-placeholder="{{$indicador->getNombre()}}">
                    <option></option>
                    @php
                        $opciones_indicador = $indicador->getOpciones();
                        if(isset($opciones_indicador[$indicador->getOpcionesEstructura(1)])){
                            $opciones = $opciones_indicador[$indicador->getOpcionesEstructura(1)];
                        }else{
                            $opciones = ['Si' => 'Si', 'No' => 'No'];
                        }
                    @endphp
                    @foreach($opciones as $key => $opcion)
                        <option value="{{$key}}" 
                            @if(isset($opciones_indicador[$indicador->getOpcionesEstructura(2)]) && $opciones_indicador[$indicador->getOpcionesEstructura(2)] == $key)
                            selected 
                            @endif>
                            {{$opcion}}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
</div>
