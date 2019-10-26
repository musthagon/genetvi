@extends('layouts.evaluacion_cursos_link_layout')

@section('content')

  @if(isset($curso) && isset($instrumento) && isset($invitacion))

    <h2>Evaluación de {{$curso->getNombre()}}</h2>

    <div id="instrucciones" >
      <div class="descripcion">
        {!!$instrumento->getDescripcion()!!}
      </div>
      <button id="iniciar" type="button" class="btn btn-block btn-success btn-lg">Iniciar</button>
      @if($instrumento->getAnonimo())
      <div class="anonimo">
        Las respuestas de este instrumento son anónimas
      </div>
      @endif
    </div>


    <!-- Instru -->
    @if(!empty($instrumento))
    <form id="wizard" class="hide-div"
      action="{{ route('evaluacion_link_procesar', ['invitacion' => $invitacion->getID()]) }}" 
      method="POST">

      <!-- CSRF TOKEN -->
      {{ csrf_field() }}

      @foreach($instrumento->categoriasOrdenadas() as $categoriaIndex => $categoria)
      <!-- Cat -->
      <h2>{{$categoria->getNombre()}}</h2>
      <section>
        
        <table class='likert-form likert-table form-container table-hover'>
          <thead>
            <tr class='likert-header'>

              <!-- Cat - title -->
              <th class='question'>{{$categoria->getNombre()}}</th>
              <th class='responses'>
                <table class='likert-table'>
                  <tr>
                    <!-- Ops -->
                    @if(!$categoria->categoriaPersonalizada())
                      <th class='response'>Siempre</th>
                      <th class='response'>A veces</th>
                      <th class='response'>Nunca</th>
                    @else
                    <th class='response'>Opciones</th>
                    @endif
                  </tr>
                </table>
              </th>
            </tr>
            <tbody class='likert'>
              @foreach($categoria->indicadoresOrdenados() as $indicadorIndex => $indicador)
              <!-- Inds -->
              <fieldset>
                <tr class='likert-row'>
                  <td class='question'>
                    <legend class='likert-legend'>
                      <div class="field-title">
                        {{$categoriaIndex+1}}-{{$indicadorIndex+1}}. {{$indicador->getNombre()}} 

                        @if($indicador->requerido())
                          <span class="obligatorio">*</span>
                        @endif
                      <div>  

                      <label for="{{$indicador->getID()}}@if($indicador->multipleField())[]@endif" class="likert-legend error">El campo es requerido</label>
                      
                    </legend>
                  </td>
                  <td class='responses'>
                    @if(!$categoria->categoriaPersonalizada())
                      @include('user.formfields.likert')
                    @else
                      @include('user.formfields.'.$indicador->getTipo())
                    @endif            
                  </td>
                </tr>
              </fieldset>
              @endforeach
            </tbody>
          </thead>
        </table>
        @if($categoria->existenIndicadoresObligatorios())
          <div class="validation-error"><label class="validation-error" style=""> <span class="obligatorio">*</span> Existen campos obligatorios.</label></div>
        @endif
      </section>
      @endforeach

    </form>
    @endif

  @endif

@stop

@section('css')
  <style>

    #wizard .select2-selection--multiple ul input{
      border: none;
    }
    #wizard textarea {
      resize: none;
    }

    #wizard .select2-selection--multiple:before {
        content: "";
        position: absolute;
        right: 7px;
        top: 42%;
        border-top: 5px solid #888;
        border-left: 4px solid transparent;
        border-right: 4px solid transparent;
    }
    #wizard .select2 li input{
      width: initial !important;
    }
  </style>
@stop

@section('javascript')  
    <script>
        $(function (){
          
          $("#iniciar").on('click', function(event){
              $("#wizard").removeClass("hide-div");
              $("#instrucciones").addClass("hide-div");
          });

          @if(!empty($instrumento))
              var form = $("#wizard");
              form.validate({

                  errorPlacement: function errorPlacement(error, element) {},
                    rules: {
                      @foreach($instrumento->categorias as $categoria)
                      @foreach($categoria->indicadores as $indicador)
                        @if($indicador->requerido())
                          '{{$indicador->id}}@if($indicador->multipleField())[]@endif' : {required :true},
                        @endif
                      @endforeach
                      @endforeach               
                  }
              });

              form.steps({
                  headerTag: "h2",
                  bodyTag: "section",
                  transitionEffect: "slide",
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
          @endif

          $('.select2').select2({
            minimumResultsForSearch: -1,
            allowClear: true,
            placeholder: function() {
                $(this).data('placeholder');
            }
          });


        });
    </script>
@stop