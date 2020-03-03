@extends('voyager::master')

@section('page_title', __($curso->getNombre()))

@section('page_header')
    <div class="container-fluid">
        <h1 class="page-title">
            <i class="icon voyager-settings"></i> {{$curso->getNombre()}}
        </h1>  
        
        @include('dashboards.revisiones_publicas')

    </div>
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
