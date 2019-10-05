@extends('layouts.users')

@section('css')
  <link rel='stylesheet' href='/css/foundation.min.css'>
  <link rel="stylesheet" href="/css/jquery.steps.css">
  <link rel="stylesheet" href="/css/linkert-table.css">
  <link rel="stylesheet" href="/adminlte/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">
  <style>
    @media (max-width: 767px){
      .box {overflow: auto;}
    }
    
  </style>
@stop

@section('content')

<!-- Main content -->
<section class="content">
  <div class="row">
    <div class="col-xs-12">

      <!-- Default box -->
      @if(!empty($instrumento))
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title">{{$instrumento->nombre}}</h3>
        </div>

        <div class="box-body">
          <p class="text-muted well well-sm no-shadow" style="margin-top: 10px;">
            {{$instrumento->descripcion}}
          </p>
          <!-- Instru -->
          <form id="wizard" 
            action="{{ route('evaluacion_procesar', ['curso' => $curso, 'instrumento' => $instrumento]) }}" 
            method="POST">

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
                                <input  name='{{$indicador->id}}' type='radio' value="2" @if(old($indicador->id) == 2) checked @endif>
                                <label class='likert-label'>Siempre</label>
                              </td>
                              <td class='response styled-radio'>
                                <input  name='{{$indicador->id}}' type='radio' value="1" @if(old($indicador->id) == 1) checked @endif>
                                <label class='likert-label'>A veces</label>
                              </td>
                              <td class='response styled-radio'>
                                <input  name='{{$indicador->id}}' type='radio' value="0" @if(old($indicador->id) == 0) checked @endif>
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

        </div>

        <!-- /.box-footer-->
      </div>
      <!-- /.box -->
      @endif

    </div>

  </div>
  


</section>
@stop

@section('javascript')

  <script type="text/javascript"  src="/js/jquery.steps.js"></script>
  <script type="text/javascript" src="/js/jquery.validate.min.js"></script>
  <!-- DataTables -->
  <script src="/adminlte/bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
  <script src="/adminlte/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
  <!-- SlimScroll -->
  <script src="/adminlte/bower_components/jquery-slimscroll/jquery.slimscroll.min.js"></script>
  <!-- FastClick -->
  <script src="/adminlte/bower_components/fastclick/lib/fastclick.js"></script>
  <script>
    $(function (){
      
      @if(!empty($instrumento))
        var form = $("#wizard");
        form.validate({
              errorPlacement: function errorPlacement(error, element) { element.before(error); },
              rules: {
                @foreach($instrumento->categorias as $categoria)
                @foreach($categoria->indicadores as $indicador)
                  '{{$indicador->id}}' : {required :true},
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
                  //alert("Submitted!");
                  form.submit();
              }
          });
      @endif
    });
  </script>


@stop
