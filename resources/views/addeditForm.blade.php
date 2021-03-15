@extends('layouts.app')
@section('title'){{trans('pagetitle.contact_addedit_title')}}
@stop
@section('content')

<div class="container-fluid">
	<form class="form" role="form" method="POST" action="{{ url('/contact/addedit') }}">
		{{ csrf_field() }}
		<div id="header-band" class="row" style="padding-top:25px">
			<div class="col-md-6 pull-left">
				<h2>{{ $data['submodule'].' '.$data['actionDisplay'] }}</h2>
			</div>
			
			<div class="col-md-6 pull-right btn-group mr-0 margin0">
				@checkPermission('contact.addedit')
				<button type="submit" class="btn btn-blue-no-border-radius pull-right" data-toggle="tooltip" title="{{trans('general.save')}}"><i class="fa fa-save"></i></button>
				@endCheckPermission				
				@if(isset($_GET['name']))
					<?php $back = old('back') ?>
					@if(isset($back))
						<a href="{{ $back }}" class="btn btn-blue-no-border-radius crawlall pull-right" role="button" data-toggle="tooltip" title="{{trans('general.cancel')}}"><i class="fas fa-step-backward"></i></a>
					@else
						<a href="{{ URL::previous().'#contact' }}" class="btn btn-blue-no-border-radius crawlall pull-right" role="button" data-toggle="tooltip" title="{{trans('general.cancel')}}"><i class="fas fa-step-backward"></i></a>
					@endif
				@else
					<a href="{{ url('/contact/view') }}" class="btn btn-blue-no-border-radius crawlall pull-right" role="button" data-toggle="tooltip" title="{{trans('general.cancel')}}"><i class="fas fa-step-backward"></i></a>
				@endif
				
			</div>
			<input type="hidden" name="primary_key" id="primary_key" value="@if(isset($contactDetail->contact_id)){{ $contactDetail->contact_id }}@else{{ 0 }}@endif">
			<input type="hidden" name="action" id="action" value="{{ $data['action'] }}">
			
			@if( $data['action'] == 'Edit' )
				<input type="hidden" name="contact_id" id="contact_id" value="{{ $contactDetail->contact_id }}">
			@endif
			
			@if(isset($_GET['name']))
				<input type="hidden" name="back" id="back" value="@if(!empty(old('back'))){{ old('back') }}@else{{ URL::previous().'#contact' }}@endif">
			@endif
			<div class="col-md-12">
				@include('layouts.message')
			</div>
		</div>
		<div class="row" style="margin-top:15px;">
			<div class="col-md-12">
				<div class="well round-border-no-margin" >
					<div class="row">
						<div class="col-md-6 contact-border-right" >
							<div class="row">
								<div class="col-md-12">
									<h3 class="pull-left">{{ trans('general.details') }}</h3>
									<span class="pull-right"><?php
									if (isset($contactDetail->contact_primary))
									{
										if ($contactDetail->contact_primary == 1)
											$primary = 1;
										else
											$primary = 0;
									} 
									else
										$primary = 0;
									?>
									<label>{{trans('general.default')}}</label>
									<br/><input type="checkbox" name="contact_primary" id="contact_primary" value="1" @if ($primary == 1) checked="checked" @endif data-toggle="toggle" data-size="small" data-style="android" data-style="slow" data-on="Yes" data-off="No">&nbsp;&nbsp;
									</span>
								</div>
							</div>
							
							<div class="row">
								<div class="form-row">
									<div class="form-group col-md-7">
										<label>{{ trans('contact.job_position') }}</label>
										<input type="text" name="contact_job_position" id="contact_job_position" class="form-control" maxlength="50" value="@if(isset($contactDetail->contact_job_position)){{ $contactDetail->contact_job_position }}@else{{ old('contact_job_position') }}@endif">
										@if ($errors->has('contact_job_position'))
											<span class="help-block error">
												<strong>{{ $errors->first('contact_job_position') }}</strong>
											</span>
										@endif
									</div>
								</div>

								<div class="form-row">
									<div class="form-group col-md-7">
											<label>{{trans('customer.customer')}}</label>
											<?php
											if (isset($contactDetail->contact_customer_id))
												$selected_customer = $contactDetail->contact_customer_id;
											else
											{
												if (isset($_GET['name']))
													$selected_customer = $_GET['name'];
												else
													$selected_customer = old('contact_customer_id');
											}
											?>
											@if(isset($_GET['name']))
												@foreach($customerList as $customer)
													@if ($selected_customer == $customer->customer_id)
														<input type="text" class="form-control" value="{{ $customer->customer_company_name}}" readonly>
														<input type="hidden" name="contact_customer_id" id="contact_customer_id" class="form-control" value="{{ $customer->customer_id }}" readonly>
													@endif
												@endforeach
											@else
												<select name="contact_customer_id" class="form-control" id="contact_customer_id">
													<option value="">{{trans('general.select')}}</option>
													@foreach($customerList as $customer)
														<option value="{{ $customer->customer_id }}" @if ($selected_customer == $customer->customer_id) selected="selected" @endif >{{ $customer->customer_company_name }}</option>
													@endforeach
												</select>
											@endif
											
											@if ($errors->has('contact_customer_id'))
												<span class="help-block error">
													<strong>{{ $errors->first('contact_customer_id') }}</strong>
												</span>
											@endif
									</div>
								</div>


								<div class="form-row">
									<div class="form-group col-md-7">
										<label class="required_field">{{ trans('contact.contact_type') }}</label>
										<select name="contact_type" class="form-control" id="contact_type">
											<?php
											if (isset($contactDetail->contact_type))
												$selected_contact_type = $contactDetail->contact_type;
											else
												$selected_contact_type = old('contact_type');
											?>
											<option value="">{{ trans('general.select') }}</option>
											@foreach($contact_type_array as $key => $value)
												<option value="{{ $key }}" @if ($selected_contact_type == $key) selected="selected" @endif >{{ $value }}</option>
											@endforeach
										</select>
										@if ($errors->has('contact_type'))
											<span class="help-block error">
												<strong>{{ $errors->first('contact_type') }}</strong>
											</span>
										@endif

									</div>
								</div>
								<div class="form-row">
									<div class="form-group col-md-6">
										<label class="required_field">{{trans('contact.first_name')}}</label>
										<input type="text" name="contact_fname" id="contact_fname" class="form-control" maxlength="50" value="@if(isset($contactDetail->contact_fname)){{ $contactDetail->contact_fname }}@else{{ old('contact_fname') }}@endif">
										@if ($errors->has('contact_fname'))
											<span class="help-block error">
												<strong>{{ $errors->first('contact_fname') }}</strong>
											</span>
										@endif
									</div>
									<div class="form-group col-md-6">
										<label class="required_field">{{trans('contact.last_name')}}</label>
										<input type="text" name="contact_lname" id="contact_lname" class="form-control" maxlength="50" value="@if(isset($contactDetail->contact_lname)){{ $contactDetail->contact_lname }}@else{{ old('contact_lname') }}@endif">
										@if ($errors->has('contact_lname'))
											<span class="help-block error">
												<strong>{{ $errors->first('contact_lname') }}</strong>
											</span>
										@endif
									</div>
								</div>
								<div class="form-row">
									<div class="form-group col-md-8">
										<label class="required_field">{{trans('contact.email')}}</label>
										<input type="text" name="contact_email" id="contact_email" class="form-control" maxlength="50" value="@if(isset($contactDetail->contact_email)){{ $contactDetail->contact_email }}@else{{ old('contact_email') }}@endif">
										@if ($errors->has('contact_email'))
											<span class="help-block error">
												<strong>{{ $errors->first('contact_email') }}</strong>
											</span>
										@endif
									</div>
								</div>
							</div>
						</div>
						<div class="col-md-6">
							<div class="row">
								<div class="form-row">
									<div class="form-group col-sm-8 col-md-7">
										<label>{{ trans('contact.contact_number') }}</label>
										<input type="text" name="contact_direct_no" id="contact_direct_no" class="form-control" value="@if(isset($contactDetail->contact_direct_no)){{ $contactDetail->contact_direct_no }}@else{{ old('contact_direct_no') }}@endif">
										@if ($errors->has('contact_direct_no'))
											<span class="help-block error">
												<strong>{{ $errors->first('contact_direct_no') }}</strong>
											</span>
										@endif
									</div>
									<div class="form-group col-sm-4 col-md-3">
										<label>{{ trans('contact.extension') }}</label>
										<input type="text" name="contact_extension" id="contact_extension" class="form-control" value="@if(isset($contactDetail->contact_extension)){{ $contactDetail->contact_extension }}@else{{ old('contact_extension') }}@endif">
										@if ($errors->has('contact_extension'))
											<span class="help-block error">
												<strong>{{ $errors->first('contact_extension') }}</strong>
											</span>
										@endif
									</div>
								</div>
								<div class="clearfix"></div>
                                <div class="form-row">
                                    <div class="form-group col-md-7">
										<label>{{ trans('contact.cell_phone') }}</label>
										<input type="text" name="contact_cell_phone" id="contact_cell_phone" class="form-control" value="@if(isset($contactDetail->contact_cell_phone)){{ $contactDetail->contact_cell_phone }}@else{{ old('contact_cell_phone') }}@endif">
										@if ($errors->has('contact_cell_phone'))
											<span class="help-block error">
												<strong>{{ $errors->first('contact_cell_phone') }}</strong>
											</span>
										@endif
									</div>
								</div>
								<div class="form-row">
									<div class="form-group col-md-7">
										<label>{{ trans('contact.address') }}</label>
										<input type="text" name="contact_address" id="contact_address" class="form-control" maxlength="100" value="@if(isset($contactDetail->contact_address)){{ $contactDetail->contact_address }}@else{{ old('contact_address') }}@endif">
										@if ($errors->has('contact_address'))
											<span class="help-block error">
												<strong>{{ $errors->first('contact_address') }}</strong>
											</span>
										@endif
									</div>
								</div>
								<div class="form-row">
									<div class="form-group col-md-4">
										<label>{{ trans('contact.zipcode') }}</label>
										<input type="text" name="contact_zipcode" id="contact_zipcode" class="form-control" maxlength="10" value="@if(isset($contactDetail->contact_zipcode)){{ $contactDetail->contact_zipcode }}@else{{ old('contact_zipcode') }}@endif">
									</div>
								</div>
								<div class="clearfix"></div>
                                <div class="form-row">
                                    <div class="form-group col-md-7">
										<label>{{ trans('contact.city') }}</label>
										<input type="text" name="contact_city" id="contact_city" class="form-control" maxlength="50" value="@if(isset($contactDetail->contact_city)){{ $contactDetail->contact_city }}@else{{ old('contact_city') }}@endif">
										@if ($errors->has('contact_city'))
											<span class="help-block error">
												<strong>{{ $errors->first('contact_city') }}</strong>
											</span>
										@endif
									</div>
								</div>
								<div class="clearfix"></div>
                                <div class="form-row">
									<?php
									if (isset($contactDetail->contact_country))
										$selected_country = $contactDetail->contact_country;
									else
										$selected_country = old('contact_country');
									?>
									<?php
									if (isset($contactDetail->contact_province))
										$selected_province = $contactDetail->contact_province;
									else
										$selected_province = old('contact_province');
									?>
									
									<div class="form-group col-md-6">
										<label>{{ trans('contact.country') }}</label>
										<select name="contact_country" id="contact_country" class="form-control">
											<option value="">{{ trans('general.select') }}</option>
											@if(!empty($countryList))
											<?php 
												//sort country list according to specifications
												usort($countryList,function($a,$b)
												{
													if(strcmp ($a['iso3_code'],"United States")==0) return -1;
													elseif(strcmp ($b['iso3_code'],"United States")==0) return 1;
													elseif(strcmp ($a['iso3_code'],"Canada")==0) return -1;
													elseif(strcmp ($b['iso3_code'],"Canada")==0) return 1;
													else return strcmp($a['iso3_code'],$b['iso3_code']);
												}); 
											?>
												@foreach($countryList as $country)
													<option value="{{ $country['iso2_code'] }}" @if( $country['iso2_code'] == $selected_country ) selected="selected" @endif>{{ $country['iso3_code'] }}</option>
												@endforeach
											@endif
										</select>
										@if ($errors->has('contact_country'))
											<span class="help-block error">
												<strong>{{ $errors->first('contact_country') }}</strong>
											</span>
										@endif
									</div>
									<div class="form-group col-md-6">
										<label>{{ trans('contact.state_province') }}</label>
										<select name="contact_province" id="contact_province" class="form-control">
											<option value="">{{trans('general.select')}}</option>
											@if(isset($selected_province) && !empty($provinceList))
												@foreach($provinceList as $province)
													@if($province['country_id'] == $selected_country)
														<option value="{{ $province['region_id'] }}" @if( $province['region_id'] == $selected_province ) selected="selected" @endif>{{ $province['default_name'] }}</option>
													@endif
												@endforeach
											@endif
										</select>
										@if ($errors->has('contact_province'))
											<span class="help-block error">
												<strong>{{ $errors->first('contact_province') }}</strong>
											</span>
										@endif

									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		@include("common.plug_customfield")
	</form>
</div>

@endsection

@section('js')
	{{--<script type="text/javascript" src="{{ asset('js/jquery.inputmask.js') }}"></script>
	--}}
	<script>
		//$('#contact_direct_no').inputmask("999-999-9999");
        phoneValidation($('#contact_direct_no'));
        phoneValidation($('#contact_cell_phone'));

        /* Get province names - START */
		$('#contact_country').change(function () {
			var country_id = this.value;
			
			var provinceURL = host + '/contact/getProvince';
			
			jQuery.ajax({
				url: provinceURL,
				type: "POST",
				dataType: "json",
				data: {
					"_token": "{{ csrf_token() }}",
					"country_id": country_id
				},
				success: function (response) {
					$("select#contact_province").find('option').remove();
					
					for (i = 0; i < response.length; i++) {
						$("select#contact_province").append($("<option></option>")
								.attr("value", response[i]['region_id']).text(response[i]['default_name']));
					}
				}
			});
		});
	</script>
@endsection