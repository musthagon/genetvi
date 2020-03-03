@extends('layouts.users')

@section('content')
    <div class="container-fluid">
        <h2 class="page-header">
            <i class="fa fa-globe"></i> {{$curso->getNombre()}}
        </h2>
        
        @include('dashboards.revisiones_publicas')
    
    
    </div>


    @include('dashboards.general_main_content')

    
@stop


@section('css')
    
    @include('dashboards.general_css')
    
@stop

@section('javascript')
    
    @include('dashboards.general_javascript')
    
@stop
