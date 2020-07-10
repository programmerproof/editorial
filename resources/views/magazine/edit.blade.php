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
					<h3 class="panel-title">Actualizar Revista</h3>
				</div>
				<div class="panel-body">					
					<div class="table-container">
						<form id="edit_magazine" method="POST" action="{{ route('magazine.update', $magazines[0]->reg_num) }}"  role="form">
							{{ csrf_field() }}
							<input name="_method" type="hidden" value="PATCH">
							<div class="row">
								<div class="col-xs-6 col-sm-6 col-md-6">
									* Título:
									<div class="form-group">
										<input type="text" name="title" id="title" class="form-control input-sm" value="{{old('title')??$magazines[0]->title}}">
									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-xs-6 col-sm-6 col-md-6">
									* Epecialidad:
										<div class="form-group">
											<select name="magperiodicity_id" id="magperiodicity_id" class="form-control">
												<option value="">-- Seleccionar --</option>
														@foreach($magperiodicities as $magperiodicity)
														<option value="{{$magperiodicity->id}}"
																@if(old('magperiodicity_id') == $magperiodicity->id )
																		selected="selected"
																@endif
																> {{ $magperiodicity->name }} </option>
														@endforeach
											</select>
										</div>
								</div>
							</div>

							<div class="row">
								<div class="col-xs-6 col-sm-6 col-md-6">
									* Epecialidad:
										<div class="form-group">
											<select name="magtype_id" id="magtype_id" class="form-control">
												<option value="">-- Seleccionar --</option>
														@foreach($magtypes as $magtype)
														<option value="{{$magtype->id}}"
																@if(old('magtype_id') == $magtype->id )
																		selected="selected"
																@endif
																> {{ $magtype->name }} </option>
														@endforeach
											</select>
										</div>
								</div>
							</div>

							<div class="row">
								<div class="col-xs-12 col-sm-12 col-md-12">
									<input type="submit" value="Actualizar" class="btn btn-success btn-block">
									<a href="{{ route('magazine.index') }}" class="btn btn-info btn-block" >Atrás</a>
								</div>	
							<input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />
							</div>
						</form>
					</div>
				</div>
 
			</div>
		</div>
	</section>
@push('js')
<script> 
	document.getElementById('magperiodicity_id').value = "{{ old('magperiodicity_id')??$magazines[0]->magperiodicity->id }}";
	document.getElementById('magtype_id').value = "{{ old('magtype_id')??$magazines[0]->magtype->id }}";
</script>
@endpush
@endsection