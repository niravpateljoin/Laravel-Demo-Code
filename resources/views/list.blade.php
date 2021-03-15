@extends('layouts.app')

@section('title')Contact/List
@stop

@section('content')
@php
    $logged_in_user_customer_id = Auth::user()->user_customer_id;
	$demo_account = config('constants.copy_customer_id');
@endphp
<div class="container-fluid">
	<div id="header-band" class="row">
		<div class="col-md-12">
			<h2 style="margin-top: 0px">{{ $data['submodule'].' '.$data['action'] }}</h2>
			@include('layouts.message')
		</div>
	</div>
	<div class="row" style="margin-top:15px">
		<div class="col-md-12">
			<div class="well round-border-no-margin">
				<div class="row">
					<div class="col-md-12">
						<div class="col-xs-12 col-sm-8 col-md-6 align-self-end pull-right">
							<a href="{{ url('/contact/add') }}" class="btn btn-blue-no-border-radius pull-right" role="button" data-toggle="tooltip" data-placement="top" title="{{trans('contact.add_contact')}}"><i class="fa fa-plus"></i>  </a>
							@if($logged_in_user_customer_id == $demo_account)
							<a href="{{url('/pvsyst/view')}}" class="btn btn-blue-no-border-radius pull-right crawlall" role="button" data-toggle="tooltip" data-placement="top" title=" {{ trans('general.mass_upload') }}"><i class="fa fa-upload" aria-hidden="true"></i></a>
							@include('layouts.export_buttons',['exportTableClass'=>'contact_contact_list'])
							@endif
						</div>
						<div class="col-md-12 table-scrollable table-responsive" style="padding:15px;border:none">
							<table class="table table-striped table-hover dynamicTable"  id='contact_contact_list'>
								<thead>
									<tr>
										<th><input type="checkbox" name="contact_check" id="contact_check" class="pr-checkall" title="Check All"></th> 
										<th>{{trans('contact.name')}}</th>
										<th>{{trans('contact.email')}} {{trans('contact.address')}}</th>
										<th>{{trans('contact.title')}}</th>
										<th>{{trans('contact.contact_number')}}</th>
										<th>{{trans('contact.customer_name')}}</th>
										<th>{{trans('contact.default')}}</th>
										<th>{{trans('contact.page_title')}} {{trans('general.type')}}</th>
										<th>{{trans('general.action')}}</th>
									</tr>
								</thead>
								<tbody>
									@if(count($contactList) > 0)
										@foreach($contactList as $contact)
											<tr>
												<td><input type="checkbox"></td> 
												<td>
													<a href="{{ url('/contact/edit').'/'.$contact->contact_id }}">{{ $contact->contact_fname.' '.$contact->contact_lname }}</a>
												</td>
												<td>{{ $contact->contact_email }}</td>
												<td>{{ $contact->contact_job_position }}</td>
												<td>{{ $contact->contact_direct_no }}</td>
												<td>{{ $contact->customer_company_name }}</td>
												<td>
													<?php $default_contact = $contact->contact_primary; ?>

													@if($default_contact)
														<i class="fa fa-check-circle" aria-hidden="true"></i>
													@else
														<i class="fa fa-times-circle" aria-hidden="true"></i>
													@endif
												</td>
												<td>
													<?php
													$type = $contact->contact_type;
													?>
													@foreach($contact_type_array as $key => $value)
														@if ($type == $key)
															{{ $value }}
														@endif
													@endforeach
												</td>
												<td>
													<a href="{{ url('/contact/remove').'/'.$contact->contact_id }}" onclick="return confirm('{{trans('general.delete_confirm')}}')"><i class="fa fa-trash" aria-hidden="true"></i></a>
												</td>
											</tr>
										@endforeach
									@endif
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

@endsection
@section('js')
<script type="text/javascript">
	$(document).ready(function () {
            var data = "contact_contact_list";
        datatableInit(data);
	});
</script>
@endsection