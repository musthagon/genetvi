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
          @if( isset($evaluacionesPendientes) && !$evaluacionesPendientes->isEmpty() )
            <div class="col-lg-6 col-xs-6">
              <!-- small box -->
              <div class="small-box bg-yellow">
                <div class="inner">
                  <h3>{{count($evaluacionesPendientes)}}</h3>

                  <p>Evaluaciones de EVA que tengo pendiente</p>
                </div>
                <div class="icon">
                  <i class="ion ion-clipboard"></i>
                  <ion-icon name="pencil"></ion-icon>
                </div>
                <a href="{{ route('mis_invitaciones_evaluar') }}" class="small-box-footer">Más información <i class="fa fa-arrow-circle-right"></i></a>
              </div>
            </div>
          @endif
      </div>


      <!-- Instrucciones -->
      <div class="nav-tabs-custom">
        <ul class="nav nav-tabs">
          <li class="pull-left header">
            <p class="fa fa-th"></p>Instrucciones para Profesores
          </li>
          <li class="active">
            <a href="#tab_1_1" data-toggle="tab" aria-expanded="true">Bienvenido a GENETVI</a>
          </li>
          <li>
            <a href="#tab_2_2" data-toggle="tab" aria-expanded="false">¿Cómo veo mis cursos?</a>
          </li>
          <li>
            <a href="#tab_3_2" data-toggle="tab" aria-expanded="false">¿Cómo puedo evaluar algún curso?</a>
          </li>              
        </ul>
        <div class="tab-content">
        <div class="tab-pane active" id="tab_1_1">
            <b>Los profesores del Campus Virtual tienen acceso a GENETVI con el objetivo de:</b>

            <ol>
              <li>Visualizar las mejoras que deben implementar en sus EVA.</li>
              <li>Contribuir a evaluar otros EVA a los que han sido invitados.</li>
            </ol>
            
        </div>
        <!-- /.tab-pane -->
        <div class="tab-pane" id="tab_2_2">
            <b>Solo puedes ver los cursos donde estas matriculado como Docente:</b>

            <p>Debes acceder a la sección de <a href="{{route('mis_cursos')}}">"Mis Cursos"</a>. Allí podrás ver tus cursos disponibles, y si recientemente has empezado otro curso como docente y no aperece listado, podras sincronizarlos con el Campus</p>
            
        </div>
        <!-- /.tab-pane -->
        <div class="tab-pane" id="tab_3_2">
            <b>Invitaciones a evaluar:</b>

            <p>Cuando tengas invitaciones a evaluar, verás en esta página una alerta de cuantas evaluaciones tienes pendiente. O puedes ingresar a<a href="{{route('mis_invitaciones_evaluar')}}">"Mis Evaluaciones"</a> y revisar el listado de las mismas</p>
        </div>
        <!-- /.tab-pane -->
        </div>
        <!-- /.tab-content -->
      </div>



  </section>
@stop

@section('javascript')
  <script>
    $(function (){
      


    });
  </script>
@stop
