<table class='likert-table'>
    <tr>
        @foreach($categoria->getLikertType() as $index => $value)
            <td class='response styled-radio'>
                <input  name='campo{{$indicador->getID()}}_{{$categoria->getID()}}' type='radio' value="{{$value}}" required @if(old('campo{{$indicador->getID()}}_{{$categoria->getID()}}') == $value) checked @endif>
                <label class='likert-label'>{{$value}}</label>
            </td>
        @endforeach                              
    </tr>
</table>