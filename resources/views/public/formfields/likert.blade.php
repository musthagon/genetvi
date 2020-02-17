<table class='likert-table'>
    <tr>
        @foreach($categoria->getLikertType() as $index => $value)
            <td class='response styled-radio'>
                <input  name='{{$indicador->getID()}}' type='radio' value="{{$value}}" required @if(old($indicador->getID()) == $value) checked @endif>
                <label class='likert-label'>{{$value}}</label>
            </td>
        @endforeach                              
    </tr>
</table>