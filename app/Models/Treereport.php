<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Treereport extends Authenticatable
{
    use HasFactory, Notifiable;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

     protected $fillable = [
        'user_id',
        'subject',
        'location',
        'issue_details',
        'date',
        'time',
        'task_id',
        'tree_id',
        'status'
    ];


 
}
