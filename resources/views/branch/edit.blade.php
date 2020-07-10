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
			@if(session('errorAccess'))
            <div class="alert alert-danger alert-dismissible">
              <button type="button" class="close" data-dismiss="alert" aria-hiddem="true"></button>
              <h4><i class="icon fa fa-ban"></i> {{session('errorAccess')}} </h4>
            </div>
          	@endif
 
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">Actualizar Sucursal</h3>
				</div>
				<div class="panel-body">					
					<div class="table-container">
						<form id="edit_branch" method="POST" action="{{ route('branch.update', $branches[0]->code) }}"  role="form">
							{{ csrf_field() }}
							<input name="_method" type="hidden" value="PATCH">
							@include('branch.statecityedit')
							<div class="row">
								<div class="col-xs-6 col-sm-6 col-md-6">
									* Nombre:
									<div class="form-group">
										<input type="text" name="name" id="name" class="form-control input-sm" value="{{old('name')??$branches[0]->name}}">
									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-xs-6 col-sm-6 col-md-6">
									<div class="form-group">
										* Dirección:
										<input type="text" name="address" id="address" class="form-control input-sm" value="{{old('address')??$branches[0]->address}}">
									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-xs-6 col-sm-6 col-md-6">
									<div class="form-group">
										* Teléfono:
										<input type="text" name="phone_num" id="phone_num" class="form-control input-sm" value="{{old('phone_num')??$branches[0]->phone_num}}">
									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-xs-12 col-sm-12 col-md-12">
									<input type="submit" value="Actualizar" class="btn btn-success btn-block">
									<a href="{{ route('branch.index') }}" class="btn btn-info btn-block" >Atrás</a>
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