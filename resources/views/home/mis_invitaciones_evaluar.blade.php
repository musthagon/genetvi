@extends('layouts.users')

@section('css')

  <link rel='stylesheet' href='/css/foundation.min.css'>
  <link rel="stylesheet" href="/css/jquery.steps.css">
  
  
  <style>
    @media (max-width: 767px){
      .box {overflow: auto;}
    }
    .course_acciones{
      display: flex;
      justify-content: center;
      flex-flow: wrap;
    }
    .course_acciones_item{
      flex: 0 1 auto;
      margin-bottom: 10px;
    }
  </style>
@stop

@section('page_description')
  <h1>
    {{$informacion_pagina['titulo']}}
    <small>{{$informacion_pagina['descripcion']}}</small>
  </h1>
@stop

@section('content')

  <!-- Main content -->
  <section class="content">
    <div class="row">

    @if( !isset($evaluacionesPendientes) || $evaluacionesPendientes->isEmpty())
        <div class="col-md-12">
          <div class="box box-default">
            <div class="box-header with-border">
              <i class="fa fa-bullhorn"></i>

              <h3 class="box-title">Notificaciones</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <div class="callout callout-info">
                <h4>No tienes evaluaciones pendientes</h4>
                <p>Cuando tengas invitaciones a realizar alguna evaluación de un curso, se mostrarán aquí</p>
              </div>
              
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
        </div>
    @endif

    @if( isset($evaluacionesPendientes) && !$evaluacionesPendientes->isEmpty())
    <div class="col-xs-12">
      <div class="box">
        <div class="box-header">
          <h3 class="box-title">Evaluaciones Pendientes</h3>
        </div>
        <!-- /.box-header -->
        <div class="box-body">

          <table id="cursos-data-table2" class="table table-bordered table-hover">
            <thead>
              <tr>
                <th>Nombre del Curso</th>
                <th>Descripción del Curso</th>
                <th>Descripción de la Invitación</th>
                <th>Estatus de la Evaluación</th>
                <th>Acciones</th>
              </tr>
            </thead>
                @foreach($evaluacionesPendientes as $invitacion)
                  <tr>
                    @php $curso = $invitacion->curso; @endphp
                    <td>
                      @if (isset($curso))
                        <a href="{{env('CVUCV_GET_SITE_URL',setting('site.CVUCV_GET_SITE_URL')).'/course/view.php?id='.$curso->getID()}}" target="_blank"> 
                          {{ isset($curso) ? $curso->getNombre() : 'Nombre del Curso' }}
                        </a>
                      @else
                        {{ isset($curso) ? $curso->getNombre() : 'Nombre del Curso' }}
                      @endif
                    </td>
                    <td class="course_summary">{!! isset($curso) ? $curso->getDescripcion() : 'Descripción del Curso'!!}</td>
                    @php $instrumento = $invitacion->instrumento; $periodo = $invitacion->periodo; $momento_evaluacion = $invitacion->momento_evaluacion; @endphp
                    <td> 
                      @if( isset($periodo) && isset($momento_evaluacion) && isset($instrumento) )
                        Invitacion a evaluar con el instrumento {{$instrumento->getNombre()}}, en el periodo léctivo {{$periodo->getNombre()}} en {{$momento_evaluacion->getNombre()}}
                      @else
                        Invitacion a evaluar con el instrumento {{$instrumento->getNombre()}} en el periodo léctivo XXXX en el momento de evaluación XXXX
                      @endif
                    </td>
                    <td class="course_estatus">
                      {{$invitacion->estatus_invitacion->getNombre()}}  
                    </td>
                    <td class="course_acciones">
                      <a class="course_acciones_item" target="_blank" href="{{ route('evaluacion_link', ['token' => $invitacion->getToken()]) }}" title="Ver">
                          <button class="btn-sm btn-primary" style="margin-right: 5px;">
                            <i class="voyager-list"></i> Ver 
                          </button>
                      </a>                  
                    </td>
                  </tr>
                @endforeach
                    
            </table>

          </div><!-- /.box-body -->
        </div><!-- /.box -->
      </div>
      

      
    </div>
    @endif

    @if(isset($evaluacionesRestantes) && !$evaluacionesRestantes->isEmpty())
    <div class="col-xs-12">
      <div class="box">
        <div class="box-header">
          <h3 class="box-title">Evaluaciones Anteriores</h3>
        </div>
        <!-- /.box-header -->
        <div class="box-body">

          <table id="cursos-data-table2" class="table table-bordered table-hover">
            <thead>
              <tr>
                <th>Nombre del Curso</th>
                <th>Descripción del Curso</th>
                <th>Descripción de la Invitación</th>
                <th>Estatus de la Evaluación</th>
                <th>Acciones</th>
              </tr>
            </thead>
                @foreach($evaluacionesRestantes as $invitacion)
                  <tr>
                    @php $curso = $invitacion->curso; @endphp
                    <td><a href="{{env('CVUCV_GET_SITE_URL',setting('site.CVUCV_GET_SITE_URL')).'/course/view.php?id='.$curso->getID()}}" target="_blank"> {{$curso->getNombre()}}</a></td>
                    <td class="course_summary">{!!$curso->getDescripcion()!!}</td>
                    @php $instrumento = $invitacion->instrumento; $periodo = $invitacion->periodo; $momento_evaluacion = $invitacion->momento_evaluacion; @endphp
                    <td> 
                      Invitacion a evaluar con el instrumento {{$instrumento->getNombre()}}, en el periodo léctivo {{$periodo->getNombre()}} en {{$momento_evaluacion->getNombre()}}
                    </td>
                    <td class="course_estatus">
                      {{$invitacion->estatus_invitacion->getNombre()}}  
                    </td>
                    <td class="course_acciones">
                                        
                    </td>
                  </tr>
                @endforeach
                    
            </table>

          </div><!-- /.box-body -->
        </div><!-- /.box -->
      </div>
      

      
    </div>
    @endif

  </section>
@stop

@section('javascript')

  <script type="text/javascript"  src="/js/jquery.steps.js"></script>
  <script type="text/javascript" src="/js/jquery.validate.min.js"></script>
  
  <!-- SlimScroll -->
  <script src="/adminlte/bower_components/jquery-slimscroll/jquery.slimscroll.min.js"></script>
  <!-- FastClick -->
  <script src="/adminlte/bower_components/fastclick/lib/fastclick.js"></script>
  <script>
    $(function (){
      

      $('#cursos-data-table, #cursos-data-table2').DataTable({
        'paging'      : false,
        'lengthChange': false,
        'searching'   : false,
        'ordering'    : true,
        'info'        : false,
        'autoWidth'   : false
      })

    });
  </script>


@stop
