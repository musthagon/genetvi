<div class="box-body">
    <div class="row">
        <div class="col-md-12">
            <div class="form-group2">
                <select class="form-control select2" 
                    style="width: 100%;" 
                    name="{{$indicador->getID()}}"
                    data-placeholder="{{$indicador->getNombre()}}">
                    <option></option>
                    @foreach($indicador->getOpciones(1) as $key => $opcion)
                        @if (old($indicador->getID()) == $opcion)
                            <option value="{{$opcion}}" selected>{{$opcion}}</option>
                        @else
                            <option value="{{$opcion}}" 
                                @if( $indicador->getOpciones(2)  == $key)
                                selected 
                                @endif>
                                {{$opcion}}
                            </option>
                        @endif
                    @endforeach
                </select>
            </div>
        </div>
    </div>
</div>
