@foreach($curso->responsablesCurso as $responsable)
    <div class="pull-left image">

    @if( strpos( $responsable->avatar, env('CVUCV_GET_WEBSERVICE_ENDPOINT1', setting('site.CVUCV_GET_WEBSERVICE_ENDPOINT1')) ) !== false )
        <img src="{{env('CVUCV_GET_WEBSERVICE_ENDPOINT2',setting('site.CVUCV_GET_WEBSERVICE_ENDPOINT2'))}}/{{strtok($responsable->avatar, env('CVUCV_GET_WEBSERVICE_ENDPOINT1', setting('site.CVUCV_GET_WEBSERVICE_ENDPOINT1')))}}/user/icon/f1?token={{env('CVUCV_ADMIN_TOKEN',setting('site.CVUCV_ADMIN_TOKEN'))}}" class="img-circle" alt="User Image"> 
    @else
        <img src="{{$responsable->avatar}}" class="img-circle" alt="User Image">
    @endif

    </div>
    {{$responsable->name}} {{$responsable->cvucv_lastname}}
@endforeach






