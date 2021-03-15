<?php

namespace App\Http\Controllers;

use Auth;
use Validator;
use App\Customer;
use App\CustomerContact;
use App\Country;
use App\Province;

use Illuminate\Http\Request;

use App\Http\Traits\TableSetupTrait;

use App\Http\Traits\CustomFieldTrait;

class CustomerContactController extends Controller
{
    use TableSetupTrait;
    use CustomFieldTrait;

	protected $logged_in_user_customer_id;

	/**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('preventBackHistory');
		$this->middleware('auth');
		$this->middleware('CheckPermission');
		$this->middleware('SystemLogger');

		$this->middleware(function ($request, $next) {
            $this->logged_in_user_customer_id = Auth::user()->user_customer_id;
            $this->logged_in_user_group_id = Auth::user()->user_group_id;
            return $next($request);
        });
	}

	/**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function view()
    {
		$data['module'] = 'Arbox Administration';
		$data['submodule'] = 'Contact';
		$data['action'] = 'List';
		$data['actionDisplay'] = trans('general.list');

		$contact_type_array = $this->getConfigDataByKey('contact_type');

        if($this->logged_in_user_group_id == 1) { //for arbox user only
            $contactList = CustomerContact::leftJoin('customer', 'customer_contact.contact_customer_id', '=', 'customer.customer_id')
                ->get(['customer_contact.*','customer_company_name']);
         } else {
            $contactList = CustomerContact::where('customer_parent_id', '=', $this->logged_in_user_customer_id)
//                ->orWhere('customer.customer_id', '=', $this->logged_in_user_customer_id)
               ->leftJoin('customer', 'customer_contact.contact_customer_id', '=', 'customer.customer_id')
                ->get(['customer_contact.*','customer_company_name']);
        }
        

		return view('contact.list')->with('data', $data)->with('contactList', $contactList)->with('contact_type_array',$contact_type_array);
    }

	public function remove($contact_id)
    {
        CustomerContact::where('contact_id', '=', $contact_id)->delete();

		return redirect()->back()->with('deletesuccess', 'Contact has been deleted successfully.');
    }

	public function add()
    {
        $data['module'] = 'Arbox Administration';
		$data['submodule'] = 'Contact';
		$data['action'] = 'Add';
		$data['actionDisplay'] = trans('general.add');

		$contact_type_array = $this->getConfigDataByKey('contact_type');
		$countryList = Country::all()->toArray();
		$provinceList = Province::all()->toArray();
		$customerList = Customer::get();
        //update list for the arbox and customer 
        if($this->logged_in_user_group_id == 1) { // for arbox user only 
            $customerList = Customer::all();
        } else {
            $customerList = Customer::where(function ($query) {
                    $query->where('customer_parent_id', '=', $this->logged_in_user_customer_id);
            })->get();
        }

        $table = (new CustomerContact())->getTable();
        $this->generateFields($table,$this->logged_in_user_customer_id);

        return view('contact.addeditForm')->with('data', $data)->with('customerList',$customerList)->with('contact_type_array',$contact_type_array)->with('countryList',$countryList)
										->with('provinceList',$provinceList)->with('render_custom_fields', $this->render_custom_fields);
    }

	public function edit($contact_id)
    {
        $data['module'] = 'Arbox Administration';
		$data['submodule'] = 'Contact';
		$data['action'] = 'Edit';
		$data['actionDisplay'] = trans('general.edit');

		$contact_type_array = $this->getConfigDataByKey('contact_type');
		$countryList = Country::all()->toArray();
		$provinceList = Province::all()->toArray();

        if($this->logged_in_user_group_id == 1) { // for arbox user only 
            $customerList = Customer::all();
        } else {
            $customerList = Customer::where(function ($query) {
                    $query->where('customer_parent_id', '=', $this->logged_in_user_customer_id);
            })->get();
        }
        $user_customer_id = $this->logged_in_user_customer_id;

        
         $contactDetail = CustomerContact::whereHas('customer',function($query) use ($user_customer_id){
            $query->where('customer_parent_id', '=', $user_customer_id);
            if($this->logged_in_user_group_id == 1) { //for arbox user 
                $query->Orwhere('customer_parent_id','=',0);
            } else { //for all other customer
                 $query->Orwhere('customer_contact.contact_customer_id','=',$user_customer_id);
            }
        })
        ->where('contact_id', '=', $contact_id)->first();
        //redirect to listing with access denied message
        if(!$contactDetail){
            return redirect()->route('contact.view')->with('PermissionError',trans('general.access_denied'));
        }
        $table = (new CustomerContact())->getTable();
        $this->generateFields($table,$this->logged_in_user_customer_id,$contact_id);

		return View('contact.addeditForm')->with('data', $data)->with('contactDetail', $contactDetail)->with('customerList',$customerList)->with('contact_type_array',$contact_type_array)->with('countryList',$countryList)
										->with('provinceList',$provinceList)->with('render_custom_fields', $this->render_custom_fields);
    }

	public function addedit(Request $request)
    {
		$back							= $request->back;
		$action							= $request->action;
		$contact_customer_id			= $request->contact_customer_id;
		$contact_fname					= $request->contact_fname;
		$contact_lname					= $request->contact_lname;
		$contact_email 					= $request->contact_email;
		$contact_direct_no				= $request->contact_direct_no;
		$contact_primary				= $request->contact_primary;
		$contact_type					= $request->contact_type;
		$contact_address		        = $request->contact_address;
		$contact_city			        = $request->contact_city;
		$contact_province		        = (empty($request->contact_province) ? ' ' : $request->contact_province);
		$contact_country		        = $request->contact_country;
		$contact_zipcode		        = $request->contact_zipcode;
        $contact_extension      		= $request->contact_extension;
		$contact_job_position			= $request->contact_job_position;
		$contact_cell_phone				= $request->contact_cell_phone;
		$contact_created_by_customer	= $this->logged_in_user_customer_id;


		if(isset($contact_primary))
			$contact_primary = 1;
		else
			$contact_primary = 0;

            $validator = Validator::make(
                array(
                    'contact_customer_id'	=> $contact_customer_id,
                    'contact_fname'			=> $contact_fname,
                    'contact_lname'			=> $contact_lname,
                    'contact_email'			=> $contact_email,
                    //'contact_direct_no'		=> $contact_direct_no,
                    'contact_type'          => $contact_type,
                    //'contact_cell_phone'	=> $contact_cell_phone,
                    //'contact_address'       => $contact_address,
                    //'contact_city'	       	=> $contact_city,
                    //'contact_country'    	=> $contact_country
                ),
                array(
                    'contact_customer_id'	=> 'required',
                    'contact_fname'			=> 'required|max:50',
                    'contact_lname'			=> 'required|max:50',
                    'contact_email'			=> 'required|email|max:255',
                    //'contact_direct_no'		=> 'phone_number',
                    'contact_type'          => 'required',
                    //'contact_cell_phone'	=> 'phone_number',
                    //'contact_address'    	=> 'required',
                    //'contact_city'	      	=> 'required',
                    //'contact_country'      => 'required'
                )
            );

		if($validator->fails())
		{
			return redirect()->back()->withErrors($validator)->withInput();
		}
		else
		{
			$default_exist = array();

			if($contact_primary)
			{
				if( $action == "Add" )
					$default_exist = CustomerContact::where('contact_primary', '=', 1)->where('contact_customer_id', '=', $contact_customer_id)->first();
				else
				{
					$contact_id	= $request->contact_id;
					$default_exist 	= CustomerContact::where('contact_primary', '=', 1)->where('contact_customer_id', '=', $contact_customer_id)->where('contact_id', '!=', $contact_id)->first();
				}
			}

			if(isset($default_exist) && count($default_exist) > 0)
			{
				$default_contact_id = $default_exist->contact_id;

				CustomerContact::where('contact_id', '=', $default_contact_id)->update(
																							array(
																									'contact_primary' => 0
																								)
																						);
			}
          
			if( $action == "Add" )
			{
				$contact = new CustomerContact;

				$contact->contact_customer_id			= $contact_customer_id;
				$contact->contact_fname					= $contact_fname;
				$contact->contact_lname					= $contact_lname;
				$contact->contact_email					= $contact_email;
				$contact->contact_direct_no				= $contact_direct_no;
				$contact->contact_extension     		= $contact_extension;
				$contact->contact_cell_phone			= $contact_cell_phone;
				$contact->contact_primary				= $contact_primary;
				$contact->contact_type					= $contact_type;
				$contact->contact_address				= $contact_address;
				$contact->contact_city				    = $contact_city;
				$contact->contact_province			    = $contact_province;
				$contact->contact_country				= $contact_country;
				$contact->contact_zipcode				= $contact_zipcode;
				$contact->contact_job_position			= $contact_job_position;
				$contact->contact_created_by_customer	= $contact_created_by_customer;

				$contact->save();

                /* Save custom fields */
                if($request->has('custom_fields'))
                {
                    $this->saveCustomFields($contact->contact_id,'customer_contact',$request->input('custom_fields'));
                }
				if($back)
					return redirect($back);
				else
					return redirect('contact/view')->with("addsuccess","Contact has been inserted successfully.");
			}
			else
			{
				$contact_id	= $request->contact_id;

				CustomerContact::where('contact_id', '=', $contact_id)->update(
													array(
															'contact_customer_id'	=> $contact_customer_id,
															'contact_fname'			=> $contact_fname,
															'contact_lname'			=> $contact_lname,
															'contact_email'			=> $contact_email,
															'contact_address'		=> $contact_address,
															'contact_city'			=> $contact_city,
															'contact_province'		=> $contact_province,
															'contact_country'		=> $contact_country,
															'contact_zipcode'		=> $contact_zipcode,
															'contact_direct_no'		=> $contact_direct_no,
															'contact_extension'     => $contact_extension,
															'contact_cell_phone'	=> $contact_cell_phone,
															'contact_primary'		=> $contact_primary,
															'contact_type'			=> $contact_type,
															'contact_job_position'	=> $contact_job_position
														)
													);

                /* Save custom fields */
                if($request->has('custom_fields'))
                {
                    $this->saveCustomFields($contact_id,'customer_contact',$request->input('custom_fields'));
                }

                if($back)
					return redirect($back);
				else
					return redirect('contact/view')->with("editsuccess","Contact has been updated successfully.");
			}
		}
    }

    public function getProvince(Request $request)
	{
		$country_id = $request->country_id;

		$result = Province::where('country_id', '=', $country_id)->get()->toArray();

		echo json_encode($result);
		exit;
	}

	public function getContact(Request $request)
	{
		$customer_id = $request->customer_id;

		$contactList = CustomerContact::where('contact_customer_id', '=', $customer_id)->get();

		if(!empty($contactList))
		{
			$result['status'] = "success";
			$result['contactList'] = $contactList;
		}
		else
		{
			$result['status'] = "no data";
			$result['contactList'] = '';
		}

		echo json_encode($result);
		exit;
	}

	public function getContactDetail(Request $request)
	{
		$contact_id = $request->contact_id;
		$contactDetail = CustomerContact::find($contact_id);
		echo json_encode($contactDetail);
		exit;
	}

	public function searchContact(Request $request){

        $customerContacts = CustomerContact::with('customer')
            ->where(function($q) use ($request) {
                $q->where('contact_fname','LIKE',$request->q.'%')->orWhere('contact_lname','LIKE',$request->q.'%');
            })->where('contact_customer_id',$this->logged_in_user_customer_id)->get();

        return response()->json($customerContacts);
    }
}