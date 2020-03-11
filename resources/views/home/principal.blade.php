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

      @if($esCoordinador != null)
      <div class="nav-tabs-custom">
        <ul class="nav nav-tabs">
          <li class="pull-left header">
            <p class="fa fa-th"></p>Instrucciones para Coordinadores EaD
          </li>
          <li class="active">
            <a href="#c_tab_1_1" data-toggle="tab" aria-expanded="true">Bienvenido a GENETVI</a>
          </li>             
        </ul>
        <div class="tab-content">
        <div class="tab-pane active" id="c_tab_1_1">
          Los Coordinadores EaD de la UCV pueden acceder al panel administrativo con el objetivo de hacer seguimiento al proceso de evaluación de los EVA de cada una de sus facultades o dependencias, siguiendo los siguientes pasos:
          <ol>
              <li>Configurar el periodo lectivo de su facultad o dependencia. En la sección de <a href="{{route('voyager.periodos-lectivos.index')}}" target="_blank">/Evaluación/Periodos Lectivos</a></li>
              <li>Sincronizar las categorías y/o cursos que se van a evaluar en el periodo lectivo previamente seleccionado. Navegando por cada una de las categorías dentro de su facultad o dependencia en la sección de <a href="{{route('gestion.evaluaciones')}}" target="_blank">Cursos del CV-UCV</a> </li>
              <li>Por último, buscar los respectivos cursos a evaluar y activar la función “ iniciar la evaluación”. Navegando por cada una de las categorías dentro de su facultad o dependencia en la sección de <a href="{{route('gestion.evaluaciones')}}" target="_blank">Cursos del CV-UCV</a></li>
          </ol>       
        </div>
        <!-- /.tab-pane -->

        </div>
        <!-- /.tab-content -->
      </div>
      @endif

  </section>
@stop

@section('javascript')
  <script>
    $(function (){
      


    });
  </script>
@stop
