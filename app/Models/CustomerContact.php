<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;

class CustomerContact extends Model
{
    use Searchable;
    use SoftDeletes;

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    public $table = 'customer_contact';
	protected $primaryKey = 'contact_id';
	protected $appends = ['contact_name'];
	
	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
     protected $fillable = [
         'contact_customer_id', 'contact_user_id', 'contact_fname', 'contact_lname', 'contact_email', 'contact_direct_no','contact_extension', 'contact_cell_phone', 'contact_primary',
		 'contact_type', 'contact_job_position', 'contact_created_by_customer','contact_language','contact_company_name','customer_vat_number'
     ];
	
	/**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'remember_token'
    ];



    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray()
    {
        $array = $this->toArray();

        // Customize array...
        $filter_array = [
            'contact_customer_id' => isset($array['contact_customer_id'])?$array['contact_customer_id']:'',
            'contact_fname'=> isset($array['contact_fname'])?$array['contact_fname']:'',
            'contact_lname'=> isset($array['contact_lname'])?$array['contact_lname']:'',
            'contact_email'=> isset($array['contact_email'])?$array['contact_email']:'',
            'contact_direct_no'=> isset($array['contact_direct_no'])?$array['contact_direct_no']:'',
            'contact_id'=> isset($array['contact_id'])?$array['contact_id']:'',
        ];
        return $filter_array;
    }

	// DEFINE RELATIONSHIPS --------------------------------------------------
    // each contact belongs to one customer
    public function contact() {
        return $this->belongsTo('Customer'); // this matches the Eloquent model
    }

    public function customer(){
        return $this->belongsTo(Customer::class,  'contact_customer_id','customer_id'); // this matches the Eloquent model
    }

    public function getContactNameAttribute($value){
        return ucfirst($this->contact_fname) . ' ' . ucfirst($this->contact_lname);
    }
}
