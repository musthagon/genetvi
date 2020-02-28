@extends('layouts.evaluacion_cursos_link_layout')

@section('content')

  @if(isset($curso) && isset($instrumento) && isset($invitacion) && isset($CategoriasPerfilInstrumento) && isset($CategoriasInstrumento))

    <h2>Evaluación de {{$curso->getNombre()}}</h2>

    <div id="instrucciones" >
      <div class="descripcion">
        {!!$instrumento->getInstrucciones()!!}
      </div>


      <!-- Instru -->
      @if(!empty($instrumento))

      @if( $edit || empty($CategoriasPerfilInstrumento) )
      <form id="wizard1" class=""
        action="{{ route('evaluacion_link_procesar2', ['token' => $invitacion->getToken(), 'invitacion' => $invitacion->getID()])  }}" 
        method="POST">
      @else
      <form id="wizard1" class=""
        action="{{ route('evaluacion_link_procesar1', ['token' => $invitacion->getToken(), 'invitacion' => $invitacion->getID()]) }}" 
        method="POST">
      @endif
      

        <!-- PUT Method if we are editing -->
        @if($edit)
            {{ method_field("PUT") }}
        @endif

        <!-- CSRF TOKEN -->
        {{ csrf_field() }}

        @php 
          $categorias = array();
          if(isset($CategoriasPerfilInstrumento) && !$edit && !empty($CategoriasPerfilInstrumento)){
            $categorias = $CategoriasPerfilInstrumento;
          }elseif(isset($CategoriasInstrumento)){
            $categorias = $CategoriasInstrumento;
          }
          $categoriaIndex = 0; 
      
        @endphp

        @foreach($categorias as $key => $categoria)
          <!-- Cat2 -->
          <h3>{{$categoria->getNombreCorto()}}</h3>
          <section id="questions-perfil-@php $key @endphp" class="perfil">
            
            <table class='likert-form likert-table form-container table-hover'>
              <thead>
                <tr class='likert-header'>

                  <!-- Cat - title -->
                  <th class='question'>{{$categoria->getNombre()}}</th>
                  <th class='responses'>
                    <table class='likert-table'>
                      <tr>
                        @if($categoria->tieneIndicadoresLikert())
                          @foreach($categoria->getLikertType() as $opcion)
                            <th class='response'>{{$opcion}}</th>
                          @endforeach
                        @else
                          Opciones de Respuesta
                        @endif
                      </tr>
                    </table>
                  </th>
                </tr>
                <tbody class='likert'>
                  @php $indicadorIndex = 0; @endphp
                  @foreach($categoria->indicadoresOrdenados() as $indicador)
                  <!-- Inds -->
                  <fieldset>
                    <tr class='likert-row'>
                      <td class='question'>
                        <legend class='likert-legend'>
                          <div class="field-title">
                            {{$categoriaIndex+1}}-{{$indicadorIndex+1}}. {{$indicador->getNombre()}} 

                            @if($indicador->getRequerido())
                              <span class="obligatorio">*</span>
                            @endif
                          <div>  

                          <label for="campo{{$indicador->getID()}}_{{$categoria->getID()}}@if($indicador->multipleField())[]@endif" class="likert-legend error">El campo es requerido</label>
                          
                        </legend>
                      </td>
                      <td class='responses'>
                      
                        @include('public.formfields.'.$indicador->getTipo())
                                    
                      </td>
                    </tr>
                  </fieldset>
                  @php $indicadorIndex = $indicadorIndex + 1; @endphp
                  @endforeach
                </tbody>
              </thead>
            </table>
            @if($categoria->existenIndicadoresObligatorios())
              <div class="validation-error"><label class="validation-error" style=""> <span class="obligatorio">*</span> Existen campos obligatorios.</label></div>
            @endif
            @if($instrumento->getPuedeRechazar() && !$edit)
              <input type="checkbox" name="aceptar" id="aceptar" onchange="activateButton(this)" checked=checked> Acepto evaluar este curso.
            @endif
          </section>
          @php $categoriaIndex = $categoriaIndex + 1; @endphp
        @endforeach

      </form>
      @endif
      
      
      <div class="anonimo">
        @if($instrumento->getAnonimo())
          Las respuestas de este instrumento son anónimas
        @else
          Las respuestas de este intrumento podrán ser visibles por el Sistema de Educación a Distancia SEDUCV, los coordinadores de facultad y por los profesores del curso
        @endif
      </div>
      
    </div>

  @endif

@stop

@section('css')
  <style>
    h3{
      font-size: 14px;
    }
    
  </style>
@stop

@section('javascript')  
    <script>
        

        $(function (){
          
          @if(!empty($instrumento))
              var form = $("#wizard1");

              form.validate({
                errorPlacement: function errorPlacement(error, element) {},
                  rules: {
                    @foreach($instrumento->categorias as $categoria)
                    @foreach($categoria->indicadores as $indicador)
                      @if($indicador->getRequerido())
                        'campo{{$indicador->getID()}}_{{$categoria->getID()}}@if($indicador->multipleField())[]@endif' : {required :true},
                      @endif
                    @endforeach
                    @endforeach               
                },
                highlight: function (element, errorClass, validClass) {
                    var target;
                    if ($(element).is('select')) {
                        target = $(element).parent('div');
                    } else {
                        target = $(element);
                    }
                    target.addClass(errorClass).removeClass(validClass);
                },
                unhighlight: function (element, errorClass, validClass) {
                    var target;
                    if ($(element).is('select')) {
                        target = $(element).parent('div');
                    } else {
                        target = $(element);
                    }
                    target.addClass(validClass).removeClass(errorClass);
                },
              });

              form.steps({
                  headerTag: "h3",
                  bodyTag: "section",
                  transitionEffect: "slideLeft",
                  onStepChanging: function (event, currentIndex, newIndex)
                  {
                      // Allways allow step back to the previous step even if the current step is not valid!
                      if (currentIndex > newIndex){
                          return true;
                      }
                      
                      form.validate().settings.ignore = ":disabled,:hidden";
                      return form.valid();
                  },
                  onFinishing: function (event, currentIndex)
                  {
                      form.validate().settings.ignore = ":disabled";
                      return form.valid();
                  },
                  onFinished: function (event, currentIndex)
                  {
                      form.submit();
                  }
              });

              $('a[href$="#finish"]').text("Enviar");
          @endif

          $('.select2').select2({
            minimumResultsForSearch: -1,
            allowClear: true,
            placeholder: function() {
                $(this).data('placeholder');
            }
          });

          $('.select2').select2({}).on("change", function (e) {
              $(this).valid()
          })

        });

    </script>
@stop