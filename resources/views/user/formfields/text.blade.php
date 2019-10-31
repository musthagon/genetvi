<div class="box-body">
    <div class="row">
        <div class="col-md-12">
            <div class="form-group2">
                <input type="text" class="form-control" 
                    style="width: 100%;" 
                    name="{{$indicador->getID()}}"
                    placeholder="{{$indicador->getNombre()}}"
                    value="{{old($indicador->getID())}}"/>
            </div>
        </div>
    </div>
</div>