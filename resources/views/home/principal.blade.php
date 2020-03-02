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
          @if( isset($cursosDocente) && !$cursosDocente->isEmpty())
            <div class="col-lg-6 col-xs-6">
              <!-- small box -->
              <div class="small-box bg-aqua">
                <div class="inner">
                  <h3>{{count($cursosDocente)}}</h3>

                  <p>Mis cursos</p>
                </div>
                <div class="icon">
                  <i class="ion ion-bag"></i>
                </div>
                <a href="{{ route('mis_cursos') }}" class="small-box-footer">Más información <i class="fa fa-arrow-circle-right"></i></a>
              </div>
            </div>
          @endif
          @if( isset($evaluacionesPendientes) && !$evaluacionesPendientes->isEmpty() )
            <div class="col-lg-6 col-xs-6">
              <!-- small box -->
              <div class="small-box bg-yellow">
                <div class="inner">
                  <h3>{{count($evaluacionesPendientes)}}</h3>

                  <p>Evaluaciones de cursos que tengo pendiente</p>
                </div>
                <div class="icon">
                  <i class="ion ion-person-add"></i>
                </div>
                <a href="{{ route('mis_invitaciones_evaluar') }}" class="small-box-footer">Más información <i class="fa fa-arrow-circle-right"></i></a>
              </div>
            </div>
          @endif
      </div>

      {!!setting('sitio.instrucciones')!!}

  </section>
@stop

@section('javascript')
  <script>
    $(function (){
      


    });
  </script>
@stop
