<div class="box-body">
    <div class="row">
        <div class="col-md-12">
            <div class="form-group2">
                <select class="form-control select2" 
                    style="width: 100%;" 
                    name='campo{{$indicador->getID()}}_{{$categoria->getID()}}[]' 
                    data-placeholder="{{$indicador->getNombre()}}"
                    multiple>
                    @foreach($indicador->getOpciones(1) as $key => $opcion)
                        <option value="{{$opcion}}"
                            @if((collect(old('campo{{$indicador->getID()}}_{{$categoria->getID()}}'))->contains($opcion))) selected 
                                @elseif( collect(old('campo{{$indicador->getID()}}_{{$categoria->getID()}}'))->isEmpty() && $indicador->getOpciones(2)  == $key)selected @endif>
                            {{$opcion}}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
</div>