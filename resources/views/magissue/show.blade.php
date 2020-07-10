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
						<strong>Número</strong>
					</h3>
				</div>
				<div class="panel-body">					
					<div class="table-container">
						<form method="POST" action="{{ route('magissue.update', $magissues[0]->reg_num) }}"  role="form">
							{{ csrf_field() }}
							<input name="_method" type="hidden" value="PATCH">
							<div class="row">
								<div class="col-xs-6 col-sm-6 col-md-6">
									<strong>Revista:</strong>
									<div class="form-group">
										{{$magissues[0]->magazine->title}}
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-xs-6 col-sm-6 col-md-6">
									<strong>Fecha:</strong>
									<div class="form-group">
										{{$magissues[0]->date}}
									</div>
								</div>
							</div>							
							<div class="row">
								<div class="col-xs-6 col-sm-6 col-md-6">
									<strong>Número de páginas:</strong>
									<div class="form-group">
										{{$magissues[0]->pages_num}}
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-xs-6 col-sm-6 col-md-6">
									<strong>Número de ejemplares:</strong>
									<div class="form-group">
										{{$magissues[0]->copies_num}}
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-xs-12 col-sm-12 col-md-12">
								    <a href="{{ route('magissue.index') }}" class="btn btn-info btn-block" >Atrás</a>
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