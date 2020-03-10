@extends('layouts.users')

@section('page_description')
  <h1>
    {{$informacion_pagina['titulo']}}
    <small>{{$informacion_pagina['descripcion']}}</small>
  </h1>
@stop

@section('content')

    @include('dashboards.general_main_content')

@stop


@section('css')
    
    @include('dashboards.general_css')
    
@stop

@section('javascript')
    
    @include('dashboards.general_javascript')
    
@stop
