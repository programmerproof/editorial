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
					<h3 class="panel-title">Nuevo Número</h3>
				</div>
				<div class="panel-body">					
					<div class="table-container">
						<form id="create_sells" method="POST" action="{{ route('sells.store') }}"  role="form">
							{{ csrf_field() }}
							<div class="row">
								<div class="col-xs-6 col-sm-6 col-md-6">
									* Sucursal:
										<div class="form-group">
											<select name="branch_code" id="branch_code" class="form-control">
												<option value="">-- Seleccionar --</option>
														@foreach($branches as $branch)
														<option value="{{$branch->code}}"
																@if(old('branch_code') == $branch->code )
																		selected="selected"
																@endif
																> {{ $branch->name }} </option>
														@endforeach
											</select>
										</div>
								</div>
							</div>
							<div class="row">
								<div class="col-xs-6 col-sm-6 col-md-6">
									* Revista:
										<div class="form-group">
											<select name="magazine_reg_num" id="magazine_reg_num" class="form-control">
												<option value="">-- Seleccionar --</option>
														@foreach($magazines as $magazine)
														<option value="{{$magazine->reg_num}}"
																@if(old('magazine_reg_num') == $magazine->reg_num )
																		selected="selected"
																@endif
																> {{ $magazine->title }} </option>
														@endforeach
											</select>
										</div>
								</div>
							</div>
 
							<div class="row">
 
								<div class="col-xs-12 col-sm-12 col-md-12">
									<input type="submit" value="Guardar" class="btn btn-success btn-block">
									<a href="{{ route('sells.index') }}" class="btn btn-info btn-block" >Atrás</a>
								</div>	
 
							</div>
						</form>
					</div>
				</div>
 
			</div>
		</div>
	</section>
	@endsection