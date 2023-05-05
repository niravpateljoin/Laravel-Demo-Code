<?php

namespace App\Http\Controllers\front;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tree;
use App\Models\Treeimage;
use App\Models\Admin;
use App\Models\User;
use App\Models\Manager;
use App\Models\Job;
use Validator;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

use Illuminate\Pagination\Paginator;

class TreeController extends Controller 
{    
    /*public function viewtree($id)
    {
        $data = Tree::where('id', '=', $id)->get();
        return view('front.qrtree',compact('data'));
    }*/

    public function view($id) 
    {
        $tree = Tree::where('id', '=', $id)->get();

        $treeimage = Treeimage::where('tree_id','=',$id)->get();

        return view('front.viewtree', compact('tree','treeimage'));
    }
}