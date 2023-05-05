<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Treeimage extends Authenticatable
{
    use HasFactory, Notifiable;

     protected $fillable = [
        'tree_id',
        'treeImage',
        'treeimage_date',
        'status'
    ];
}
