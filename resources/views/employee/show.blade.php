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
			<div class="alert alert-info">
				{{Session::get('success')}}
			</div>
			@endif
 
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">
						<strong>Sucursal</strong>
					</h3>
				</div>
				<div class="panel-body">					
					<div class="table-container">
						<form method="POST" action="{{ route('employee.update',$employees[0]->code) }}"  role="form">
							{{ csrf_field() }}
							<input name="_method" type="hidden" value="PATCH">
							<div class="row">
								<div class="col-xs-6 col-sm-6 col-md-6">
									<strong>Departamento:</strong>
									<div class="form-group">
										{{$employees[0]->branch->city->state->name}}
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-xs-6 col-sm-6 col-md-6">
									<strong>Ciudad:</strong>
									<div class="form-group">
										{{$employees[0]->branch->city->name}}
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-xs-6 col-sm-6 col-md-6">
									<strong>Ciudad:</strong>
									<div class="form-group">
										{{$employees[0]->branch->name}}
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-xs-6 col-sm-6 col-md-6">
									<strong>Identificación:</strong>
									<div class="form-group">
										{{$employees[0]->id_card_num}}
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-xs-6 col-sm-6 col-md-6">
									<strong>Nombres:</strong>
									<div class="form-group">
										{{$employees[0]->name_1}} {{$employees[0]->name_2}}
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-xs-6 col-sm-6 col-md-6">
									<strong>Apellidos:</strong>
									<div class="form-group">
										{{$employees[0]->surname_1}} {{$employees[0]->surname_2}}
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-xs-6 col-sm-6 col-md-6">
									<strong>Dirección:</strong>
									<div class="form-group">
										{{$employees[0]->address}}
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-xs-6 col-sm-6 col-md-6">
									<strong>Teléfono:</strong>
									<div class="form-group">
										{{$employees[0]->phone_num}}
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-xs-6 col-sm-6 col-md-6">
									<strong>Teléfono:</strong>
									<div class="form-group">
										{{$employees[0]->mobile_num}}
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-xs-12 col-sm-12 col-md-12">
								    <a href="{{ route('employee.index') }}" class="btn btn-info btn-block" >Atrás</a>
								</div>	
							<input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />
							</div>
						</form>
					</div>
				</div>
 
			</div>
		</div>
	</section>
	@endsection