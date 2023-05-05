<?php



namespace App\Models;



use Illuminate\Database\Eloquent\Model;

use Illuminate\Notifications\Notifiable;

use Illuminate\Foundation\Auth\User as Authenticatable;



class Job extends Authenticatable

{

	use Notifiable;



    



    protected $fillable = [

        'job_title',

        'description',

        'job_date',

//        'category_id',

        'manager_id',
        'updateid',
        'user_id',

  //      'tags',

        'address',
        'location',

    //    'latitude',

      //  'longitude',

        'start_time',

        'end_time',

        'status',

        'species',

        'age_range',

        'vitality',

        'solid_type',

        'height',

        'trunk_diametr',

        'deffects',

        'comment',

        'image',

        'tree_id',

    ];



}

