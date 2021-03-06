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
					<h3 class="panel-title">Actualizar Número</h3>
				</div>
				<div class="panel-body">					
					<div class="table-container">
						<form id="edit_writes" method="POST" action="{{ route('writes.update', $writes[0]->id) }}"  role="form">
							{{ csrf_field() }}
							<input name="_method" type="hidden" value="PATCH">
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
								<div class="col-xs-6 col-sm-6 col-md-6">
									* Periodista:
										<div class="form-group">
											<select name="journalist_id" id="journalist_id" class="form-control">
												<option value="">-- Seleccionar --</option>
														@foreach($journalists as $journalist)
														<option value="{{$journalist->id}}"
																@if(old('journalist_id') == $journalist->id )
																		selected="selected"
																@endif
																> {{ $journalist->name_1 }}  {{ $journalist->name_2 }}   {{ $journalist->surname_1 }}   {{ $journalist->surname_2 }} </option>
														@endforeach
											</select>
										</div>
								</div>
							</div>

							<div class="row">
								<div class="col-xs-12 col-sm-12 col-md-12">
									<input type="submit" value="Actualizar" class="btn btn-success btn-block">
									<a href="{{ route('writes.index') }}" class="btn btn-info btn-block" >Atrás</a>
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
	document.getElementById('magazine_reg_num').value = "{{ old('magazine_reg_num')??$writes[0]->magazine->reg_num }}";
	document.getElementById('journalist_id').value = "{{ old('journalist_id')??$writes[0]->journalist->id }}";
</script>
@endpush
@endsection