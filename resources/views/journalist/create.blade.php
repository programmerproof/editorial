@extends('adminlte::page')

@section('title', 'AdminLTE')

@section('content')
<div class="row">
	<section class="content">
		<div class="col-md-8 col-md-offset-2">
			@if (count($errors) > 0)
				<div class="alert alert-danger">
					<strong>Error!</strong> Revise los campos obligatorios.<br><br>
					<ul>
						@foreach ($errors->all() as $error)
						<li>{{ $error }}</li>
						@endforeach
					</ul>
				</div>
			@endif
			@if(Session::has('success'))
				<div class="alert alert-success alert-dismissible">
					{{Session::get('success')}}
				</div>
			@endif
			@if(session('errorAccess'))
            <div class="alert alert-danger alert-dismissible">
              <button type="button" class="close" data-dismiss="alert" aria-hiddem="true"></button>
              <h4><i class="icon fa fa-ban"></i> {{session('errorAccess')}} </h4>
            </div>
          	@endif
 
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">Nuevo periodista</h3>
				</div>
				<div class="panel-body">					
					<div class="table-container">
						<form id="create_journalist" method="POST" action="{{ route('journalist.store') }}"  role="form">
							{{ csrf_field() }}
							<div class="row">
								<div class="col-xs-6 col-sm-6 col-md-6">
									* Epecialidad:
										<div class="form-group">
											<select name="jrnspeciality_id" id="jrnspeciality_id" class="form-control">
												<option value="">-- Seleccionar --</option>
														@foreach($jrnspecialities as $jrnspeciality)
														<option value="{{$jrnspeciality->id}}"
																@if(old('jrnspeciality_id') == $jrnspeciality->id )
																		selected="selected"
																@endif
																> {{ $jrnspeciality->name }} </option>
														@endforeach
											</select>
										</div>
								</div>
							</div>
							<div class="row">
								<div class="col-xs-6 col-sm-6 col-md-6">
									<div class="form-group">
										* Nombre 1:
										<input type="text" name="name_1" id="name_1" class="form-control" placeholder=""
											   value="{{old('name_1')??''}}">
									</div>
								</div>
							</div>
							
							<div class="row">
								<div class="col-xs-6 col-sm-6 col-md-6">
									<div class="form-group">
										Nombre 2:
										<input type="text" name="name_2" id="name_2" class="form-control" placeholder=""
											   value="{{old('name_2')??''}}">
									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-xs-6 col-sm-6 col-md-6">
									<div class="form-group">
										* Apellido 1:
										<input type="text" name="surname_1" id="surname_1" class="form-control" placeholder=""
											   value="{{old('surname_1')??''}}">
									</div>
								</div>
							</div>
							
							<div class="row">
								<div class="col-xs-6 col-sm-6 col-md-6">
									<div class="form-group">
										Apellido 2:
										<input type="text" name="surname_2" id="surname_2" class="form-control" placeholder=""
											   value="{{old('surname_2')??''}}">
									</div>
								</div>
							</div>
							
							<div class="row">
								<div class="col-xs-6 col-sm-6 col-md-6">
									<div class="form-group">
										* Identificación:
										<input type="text" name="id_card_num" id="id_card_num" class="form-control" placeholder=""
											   value="{{old('id_card_num')??''}}">
									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-xs-6 col-sm-6 col-md-6">
									<div class="form-group">
										* Dirección:
										<input type="text" name="address" id="address" class="form-control" placeholder=""
											   value="{{old('address')??''}}">
									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-xs-6 col-sm-6 col-md-6">
									<div class="form-group">
										* Teléfono:
										<input type="text" name="phone_num" id="phone_num" class="form-control" placeholder=""
											   value="{{old('phone_num')??''}}">
									</div>
								</div>
							</div>
							
							<div class="row">
								<div class="col-xs-6 col-sm-6 col-md-6">
									<div class="form-group">
										* Móvil:
										<input type="text" name="mobile_num" id="mobile_num" class="form-control" placeholder=""
											   value="{{old('mobile_num')??''}}">
									</div>
								</div>
							</div>
 
							<div class="row">
 
								<div class="col-xs-12 col-sm-12 col-md-12">
									<input type="submit" value="Guardar" class="btn btn-success btn-block">
									<a href="{{ route('journalist.index') }}" class="btn btn-info btn-block" >Atrás</a>
								</div>	
 
							</div>
						</form>
					</div>
				</div>
 
			</div>
		</div>
	</section>
	@endsection