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
			<div class="alert alert-info alert-dismissible">
				{{Session::get('success')}}
			</div>
			@endif
 
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">Actualizar Empleado</h3>
				</div>
				<div class="panel-body">					
					<div class="table-container">
						<form id="edit_employee" method="POST" action="{{ route('employee.update', $employees[0]->id) }}"  role="form">
							{{ csrf_field() }}
							<input name="_method" type="hidden" value="PATCH">
							@include('employee.statecitybranchedit')
							<div class="row">
								<div class="col-xs-6 col-sm-6 col-md-6">
									* Nombre 1:
									<div class="form-group">
										<input type="text" name="name_1" id="name_1" class="form-control input-sm" value="{{old('name_1')??$employees[0]->name_1}}">
									</div>
								</div>
							</div>
							
							<div class="row">
								<div class="col-xs-6 col-sm-6 col-md-6">
									Nombre 2:
									<div class="form-group">
										<input type="text" name="name_2" id="name_2" class="form-control input-sm" value="{{old('name_2')??$employees[0]->name_2}}">
									</div>
								</div>
							</div>
							
							<div class="row">
								<div class="col-xs-6 col-sm-6 col-md-6">
									* Apellido 1:
									<div class="form-group">
										<input type="text" name="surname_1" id="surname_1" class="form-control input-sm" value="{{old('surname_1')??$employees[0]->surname_1}}">
									</div>
								</div>
							</div>
							
							<div class="row">
								<div class="col-xs-6 col-sm-6 col-md-6">
									Apellido 2:
									<div class="form-group">
										<input type="text" name="surname_2" id="surname_2" class="form-control input-sm" value="{{old('surname_2')??$employees[0]->surname_2}}">
									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-xs-6 col-sm-6 col-md-6">
									<div class="form-group">
										* Identificación:
										<input type="text" name="id_card_num" id="id_card_num" class="form-control input-sm" value="{{old('id_card_num')??$employees[0]->id_card_num}}">
									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-xs-6 col-sm-6 col-md-6">
									<div class="form-group">
										* Dirección:
										<input type="text" name="address" id="address" class="form-control input-sm" value="{{old('address')??$employees[0]->address}}">
									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-xs-6 col-sm-6 col-md-6">
									<div class="form-group">
										* Teléfono:
										<input type="text" name="phone_num" id="phone_num" class="form-control input-sm" value="{{old('phone_num')??$employees[0]->phone_num}}">
									</div>
								</div>
							</div>
							
							<div class="row">
								<div class="col-xs-6 col-sm-6 col-md-6">
									<div class="form-group">
										* Móvil:
										<input type="text" name="mobile_num" id="mobile_num" class="form-control input-sm" value="{{old('mobile_num')??$employees[0]->mobile_num}}">
									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-xs-12 col-sm-12 col-md-12">
									<input type="submit" value="Actualizar" class="btn btn-success btn-block">
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