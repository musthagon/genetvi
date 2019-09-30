<!-- Instru -->
<form id="wizard" action="{{ route('evaluar') }}" method="POST">
  <!-- CSRF TOKEN -->
  {{ csrf_field() }}

  @foreach($instrumento->categorias as $categoria)
  <!-- Cat -->
  <h2>{{$categoria->nombre}}</h2>
  <section>
    
    <table class='likert-form likert-table form-container'>
      <thead>
        <tr class='likert-header'>

          <!-- Cat - title -->
          <th class='question'>{{$categoria->nombre}}</th>
          <th class='responses'>
            <table class='likert-table'>
              <tr>
                <!-- Ops -->
                <th class='response'>Siempre</th>
                <th class='response'>A veces</th>
                <th class='response'>Nunca</th>
              </tr>
            </table>
          </th>
        </tr>
        <tbody class='likert'>
          @foreach($categoria->indicadores as $indicador)
          <!-- Inds -->
          <fieldset>
            <tr class='likert-row'>
              <td class='question'>
                <legend class='likert-legend'>{{$indicador->nombre}}</legend>
              </td>
              <td class='responses'>
                <table class='likert-table'>
                  <tr>
                    <td class='response styled-radio'>
                      <input  name='{{$indicador->nombre}}' type='radio' value='1' >
                      <label class='likert-label'>Siempre1</label>
                    </td>
                    <td class='response styled-radio'>
                      <input  name='{{$indicador->nombre}}' type='radio' value='2'>
                      <label class='likert-label'>A veces</label>
                    </td>
                    <td class='response styled-radio'>
                      <input  name='{{$indicador->nombre}}' type='radio' value='3'>
                      <label class='likert-label'>Nunca</label>
                    </td>          
                  </tr>
                </table>             
              </td>
            </tr>
          </fieldset>
          @endforeach
        </tbody>
      </thead>
    </table>

  </section>
  @endforeach

  <div class="validation-error"><label class="validation-error" style="">* Existen campos obligatorios.</label></div>
</form>