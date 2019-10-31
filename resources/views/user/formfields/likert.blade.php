<table class='likert-table'>
    <tr>
        @foreach($categoria->likertOpciones() as $value => $opcion)
            <td class='response styled-radio'>
                <input  name='{{$indicador->getID()}}' type='radio' value="{{$value}}" required @if(old($indicador->getID()) == $value) checked @endif>
                <label class='likert-label'>{{$opcion}}</label>
            </td>
        @endforeach                              
    </tr>
</table>