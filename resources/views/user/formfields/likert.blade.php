<table class='likert-table'>
    <tr>
        <td class='response styled-radio'>
        <input  name='{{$indicador->getID()}}' type='radio' value="2" required>
        <label class='likert-label'>Siempre</label>
        </td>
        <td class='response styled-radio'>
        <input  name='{{$indicador->getID()}}' type='radio' value="1" >
        <label class='likert-label'>A veces</label>
        </td>
        <td class='response styled-radio'>
        <input  name='{{$indicador->getID()}}' type='radio' value="0" >
        <label class='likert-label'>Nunca</label>
        </td>                               
    </tr>
</table>