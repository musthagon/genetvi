<div class='input-group date'>
    <input type="text" class="form-control" name="{{ $row->field }}"
       placeholder="{{ $row->getTranslatedAttribute('display_name') }}"
       value="@if(isset($dataTypeContent->{$row->field})){{ date('m-d-Y H:i:s',strtotime( old($row->field, $dataTypeContent->{$row->field}) )) }}@else{{old($row->field)}}@endif">
    <span class="input-group-addon">
        <span class="glyphicon glyphicon-calendar"></span>
    </span>
</div>