<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	  <meta name="author" content="colorlib.com">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Evaluación de @isset($curso) {{$curso->getNombre()}} @else Curso @endif</title>

    <!-- Font Icon -->
    <link rel="stylesheet" href="/adminlte/bower_components/bootstrap/dist/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="/adminlte/bower_components/font-awesome/css/font-awesome.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="/adminlte/bower_components/Ionicons/css/ionicons.min.css">

    <link rel='stylesheet' href='/css/foundation.min.css'>
    <link rel="stylesheet" href="/css/jquery.steps.css">
    <link rel="stylesheet" href="/css/linkert-table.css">

    <!-- Main css -->
    <link rel="stylesheet" href="/custom-wizard/css/style.css">

    <style>
      .hide-div{
        display:none;
      }
      .show-div{
        display:initial;
      }
      .descripcion{
        padding: 50px 20px 50px 20px;
        text-align: justify;
      }
      .anonimo{
        padding: 50px 20px 0px 20px;
        text-align: center;
        font-style: italic;
      }
      .message_title{
        font-size: 4.25rem;
      }
      .message_font{
        font-size: 6.75rem;
      }
      .message_icon1{
        line-height: 1;
        color: #24b663;
      }
      .message_icon2{
        line-height: 1;
        color: red;
      }
      .center{
        text-align: center;
      }
    </style>
</head>

<body>
    <div class="main">

        <div class="container">
          
          @if(isset($message) && isset($alert_type))

          <h2 class="message_title">{{ $alert_type }}</h2>

          <div class="center" >

            @if($alert_type=="gracias")
              <i class="fa fa-check message_font message_icon1"></i>
            @else
              <i class="fa fa-close message_font message_icon2"></i>
            @endif


            <div class="descripcion center">
              {{ $message }}
            </div>
          </div>

          @elseif(isset($curso) && isset($instrumento) && isset($invitacion))

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
                            @if($categoria->categoriaPersonalizada())
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
                            <legend class='likert-legend'>{{$categoriaIndex+1}}-{{$indicadorIndex+1}}. {{$indicador->getNombre()}} 
                              @if($indicador->requerido())
                                <span class="obligatorio">*</span>
                              @endif
                              <label for="{{$indicador->getID()}}" class="likert-legend error">El campo es requerido </label>
                              
                            </legend>
                          </td>
                          <td class='responses'>
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

        </div>

    </div>

    <!-- JS -->
    <!-- jQuery 3 -->
    <script src="/adminlte/bower_components/jquery/dist/jquery.min.js"></script>
    
    <script type="text/javascript" src="/js/jquery.validate.min.js"></script>
    <script type="text/javascript"  src="/js/jquery.steps.js"></script>

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
                          '{{$indicador->id}}' : {required :true},
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
        });
    </script>

    </body>

</html>