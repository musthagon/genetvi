<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="author" content="colorlib.com">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Evaluación de {{$curso->cvucv_fullname}}<</title>

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
</head>

<body>

    <div class="main">

        <div class="container">
            <h2>Evaluación de {{$curso->cvucv_fullname}}</h2>
          <!-- Instru -->
          @if(!empty($instrumento))
          <form id="wizard" 
            action="{{ route('evaluacion_procesar', ['curso' => $curso, 'instrumento' => $instrumento]) }}" 
            method="POST">

            <!-- CSRF TOKEN -->
            {{ csrf_field() }}

            @foreach($instrumento->categorias as $categoriaIndex => $categoria)
            <!-- Cat -->
            <h2>{{$categoria->nombre_corto}}</h2>
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
                    @foreach($categoria->indicadores as $indicadorIndex => $indicador)
                    <!-- Inds -->
                    <fieldset>
                      <tr class='likert-row'>
                        <td class='question'>
                          <legend class='likert-legend'>{{$categoriaIndex+1}}-{{$indicadorIndex+1}}. {{$indicador->nombre}} <span class="obligatorio">*</span></legend>
                        </td>
                        <td class='responses'>
                          <table class='likert-table'>
                            <tr>
                              <td class='response styled-radio'>
                                <input  name='{{$indicador->id}}' type='radio' value="2" >
                                <label class='likert-label'>Siempre</label>
                              </td>
                              <td class='response styled-radio'>
                                <input  name='{{$indicador->id}}' type='radio' value="1" >
                                <label class='likert-label'>A veces</label>
                              </td>
                              <td class='response styled-radio'>
                                <input  name='{{$indicador->id}}' type='radio' value="0" >
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

            <div class="validation-error"><label class="validation-error" style=""> <span class="obligatorio">*</span> Existen campos obligatorios.</label></div>
          </form>
          @endif

        </div>

    </div>

    <!-- JS -->
    <!-- jQuery 3 -->
    <script src="/adminlte/bower_components/jquery/dist/jquery.min.js"></script>
    
    <script type="text/javascript" src="/js/jquery.validate.min.js"></script>
    <script type="text/javascript"  src="/js/jquery.steps.js"></script>
    
    
    <!-- 
    <script src="/custom-wizard/js/main.js"></script>-->

    <script>
        $(function (){
        
            @if(!empty($instrumento))
                var form = $("#wizard");
                form.validate({
                    /*showErrors: function(errorMap, errorList) {
                        alert(`Your form contains ${this.numberOfInvalids()} errors`);
                    },*/
                    invalidHandler: function(event, validator) {
                        // 'this' refers to the form
                        var errors = validator.numberOfInvalids();
                        if (errors) {
                        var message = errors == 1
                            ? 'You missed 1 field. It has been highlighted'
                            : 'You missed ' + errors + ' fields. They have been highlighted';
                        alert('test');
                        $("div.error span").html(message);
                        $("div.error").show();
                        } else {
                        $("div.error").hide();
                        }
                    },
                    //errorPlacement: function errorPlacement(error, element) {  element.before(error); },
                    rules: {
                        @foreach($instrumento->categorias as $categoria)
                        @foreach($categoria->indicadores as $indicador)
                        '{{$indicador->id}}' : {required :true},
                        @endforeach
                        @endforeach               
                        
                    }


                });
                form.validate({
                    
                    /*invalidHandler: function(event, validator) {
                        // 'this' refers to the form
                        var errors = validator.numberOfInvalids();
                        if (errors) {
                        var message = errors == 1
                            ? 'You missed 1 field. It has been highlighted'
                            : 'You missed ' + errors + ' fields. They have been highlighted';
                        alert('test');
                        $("div.error span").html(message);
                        $("div.error").show();
                        } else {
                        $("div.error").hide();
                        }
                    }*/
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