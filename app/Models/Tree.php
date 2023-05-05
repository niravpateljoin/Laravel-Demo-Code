<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tree extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    //protected $guard = 'manager';

    // protected $dates = ['date_planted'];
     protected $fillable = [
        'treeid',
        'userid',
        'qrImage',
        'address',
        'location',
        'species',
        'height',
        'trunk_diameter',
        'defects',
        'date_planted',
        'comments',
        'age_range',
        'vitality',
        'soil_type',
        'updated_id',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
