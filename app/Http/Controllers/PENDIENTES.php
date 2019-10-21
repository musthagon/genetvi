<div class="cd-filter-block">
					<h4>Revisores</h4>

					<ul class="cd-filter-content cd-filters list">
						<li>
							<input class="filter" data-filter="" type="radio" name="radioButton" id="radio1" checked>
							<label class="radio-label" for="radio1">Todos</label>
						</li>

						<li>
							<input class="filter" data-filter=".revisores" type="radio" name="radioButton" id="revisores">
							<label class="radio-label" for="revisores">Revisores</label>
						</li>
					</ul> <!-- cd-filter-content -->
				</div> <!-- cd-filter-block -->


@if(!empty($listadoParticipantesRevisores[$periodo_index][$instrumento_index]))
                            <div class="chartTarget col-xs-12 mix {{$periodo_string}} {{$instrumento_string}} revisores">
                            <div class="box">
                                <div class="box-header">
                                <h3 class="box-title">Revisores del curso <br>Del Instrumento {{$instrumento->nombre}}<br>En el periodo lectivo {{$periodo->nombre}}</h3>
                                </div>
                                <!-- /.box-header -->
                                <div class="box-body">

                                <table id="revisores-data-table" class="table table-bordered table-hover">
                                    <thead>
                                    <tr>
                                        <th>Nombre</th>
                                        <th>Correo</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    

                                    @foreach($listadoParticipantesRevisores[$periodo_index][$instrumento_index] as $revisorIndex=>$revisor)
                                        <tr>
                                        <td>
                                            
                                            <a href="{{env('CVUCV_GET_SITE_URL','https://campusvirtual.ucv.ve')}}/user/view.php?id={{$revisor->cvucv_id}}&course={{$curso->id}}" target="_blank">
                                                <div class="pull-left image">

                                                    @if( strpos( $revisor->avatar, env('CVUCV_GET_WEBSERVICE_ENDPOINT1', setting('site.CVUCV_GET_WEBSERVICE_ENDPOINT1')) ) !== false )
                                                        <img src="{{env('CVUCV_GET_WEBSERVICE_ENDPOINT2',setting('site.CVUCV_GET_WEBSERVICE_ENDPOINT2'))}}/{{strtok($revisor->avatar, env('CVUCV_GET_WEBSERVICE_ENDPOINT1', setting('site.CVUCV_GET_WEBSERVICE_ENDPOINT1')))}}/user/icon/f1?token={{env('CVUCV_ADMIN_TOKEN',setting('site.CVUCV_ADMIN_TOKEN'))}}" class="img-circle" alt="User Image"> 
                                                    @else
                                                        <img src="{{$revisor->avatar}}" class="img-circle" alt="User Image">
                                                    @endif

                                                </div>{{$revisor->name}}
                                            </a>
                                        </td>
                                        <td>{{$revisor->email}}</td>                              
                                        </tr>
                                    @endforeach
                                      
                                </table>

                                </div><!-- /.box-body -->
                            </div><!-- /.box -->
                            </div>
                        @endif


