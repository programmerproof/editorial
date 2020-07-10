	<div class="row">
		<div class="col-xs-6 col-sm-6 col-md-6">
			* Departamento:
				<div class="form-group">
					<select name="state_code" id="state_code" class="form-control">
						<option value="">-- Seleccionar --</option>
								@foreach($states as $state)
								<option value="{{$state->code}}"
										@if(old('state_code') == $state->code )
												selected="selected"
										@endif
										> {{ $state->name }} </option>
								@endforeach	
					</select>
				</div>
		</div>
	</div>
	<div class="row">
		<div class="col-xs-6 col-sm-6 col-md-6">
			* Ciudad:
				<div class="form-group">
					<select name="city_code" id="city_code" class="form-control">
						<option value="">-- Seleccionar --</option>
						@if (!empty($cities)) 
							@foreach($cities as $city)
								<option value="{{$city->code}}"
							   		   @if(old('city_code') == $city->code )
											selected="selected"
									   @endif
									   > {{ $city->name }} </option>
							@endforeach
						@endif
					</select>
				</div>
			</div>
	</div>
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


@push('js')
<script> 
	document.getElementById('state_code').value = "{{ old('state_code')??$employees[0]->branch->city->state->code }}";
	document.getElementById('city_code').value = "{{ old('city_code')??$employees[0]->branch->city->code }}";
	document.getElementById('branch_code').value = "{{ old('branch_code')??$employees[0]->branch->code }}";
				Bind.event({
				form : 'edit_employee',
				type : 'onchange',
				request : 'get',
				url : "{{action('CityController@search', 'state_code')}}",
				trigger : 'state_code',
				node : 'select',
				listener : 'city_code',
				finally : function(form){ 
					if(Bind.element({
									 form : form,
									 name : 'state_code'  
									}).value == ''){ 	
					   Bind.initElement({
							form : form,
							node : 'select',
							listener : 'branch_code'
					   });				
					}
				 }
				}); 
				Bind.event({
				form : 'edit_employee',
				type : 'onchange',
				request : 'get',
				url : "{{action('BranchController@search', 'city_code')}}",
				trigger : 'city_code',
				node : 'select',
				listener : 'branch_code'
				});
</script>
@endpush	