<div class="box-body">
    <div class="row">
        <div class="col-md-12">
            <div class="form-group2">
                <select class="form-control select2" 
                    style="width: 100%;" 
                    name="{{$indicador->getID()}}[]" 
                    data-placeholder="{{$indicador->getNombre()}}"
                    multiple>
                    @foreach($indicador->getOpciones(1) as $key => $opcion)
                        <option value="{{$opcion}}"
                            @if((collect(old($indicador->getID()))->contains($opcion))) selected 
                                @elseif( collect(old($indicador->getID()))->isEmpty() && $indicador->getOpciones(2)  == $key)selected @endif>
                            {{$opcion}}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
</div>