<?php

namespace App\Http\Controllers\adminpanel;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Manager;
use App\Models\Admin;
use App\Models\Job;
use App\Models\Tree;
use App\Models\Treereport;
use DB;

class DashboardController extends Controller
{
    public function index() {
        $adminName = "";
        $adminimage = "";
        $loginUserId = AUTH::user()->id;
        $admin = Admin::where('id','=',$loginUserId)->get();
        
        if (count($admin)>0) 
        {
            $adminName = $admin[0]->first_name." ".$admin[0]->last_name;
            if ($admin[0]->userImage != "" && $admin[0]->userImage != null) 
            {
                if(file_exists(public_path()."/uploads/userImages/".$admin[0]->userImage))
                {
                    $adminimage = asset('uploads/userImages/'.$admin[0]->userImage);
                }
            }
        }
    	$data = array(
            'Admin' => Admin::where('id','=',$loginUserId)->get(),
    		'Admins' => Admin::count(),
    		'Users' => User::count(),
    		'Managers' => Manager::count(),
    		'To do' => Job::where('status',1)->count(),
    		'In progress' => Job::where('status',2)->count(),
    		'Done' => Job::where('status',3)->count(),
    		'Task' => Job::where('status',1)->count() + Job::where('status',2)->count() + Job::where('status',3)->count(),
            'New' => Treereport::where('status','New')->count(),
            'Checked' => Treereport::where('status','Checked')->count(),
            'Report' => Treereport::where('status','New')->count() + Treereport::where('status','Checked')->count(),
    	);
        
        $age1 = Tree::where('age_range','=','0 to 10 years')->get();
        $age2 = Tree::where('age_range','=','11 to 30 years')->get();
        $age3 = Tree::where('age_range','=','31 years to 50 years')->get();
        $age4 = Tree::where('age_range','=','51 years to 80 years')->get();
        $age5 = Tree::where('age_range','=','81 years to 100 years')->get();
        $age6 = Tree::where('age_range','=','Above 100 years')->get();
       /* echo $agecount1 = count($age1);       
        echo $agecount2 = count($age2);
        echo $agecount3 = count($age3);
        echo $agecount4 = count($age4);
        echo $agecount5 = count($age5);
        echo $agecount6 = count($age6);*/

        $agecount1 = count($age1);       
        $agecount2 = count($age2);
        $agecount3 = count($age3);
        $agecount4 = count($age4);
        $agecount5 = count($age5);
        $agecount6 = count($age6);
        
        $tree_height1 = Tree::where('height','<=',10)->where('height','>=',0)->get();
        $tree_height2 = Tree::where('height','<=',30)->where('height','>=',10.01)->get();
        $tree_height3 = Tree::where('height','<=',50)->where('height','>=',30.01)->get();
        $tree_height4 = Tree::where('height','<=',70)->where('height','>=',50.01)->get();
        $tree_height5 = Tree::where('height','>=',70.01)->get();

        /*echo "===". $treeheight1 = count($tree_height1);       
        echo "===".$treeheight2 = count($tree_height2);
        echo "===".$treeheight3 = count($tree_height3);
        echo "===".$treeheight4 = count($tree_height4);
        echo "===".$treeheight5 = count($tree_height5);*/

        $treeheight1 = count($tree_height1);       
        $treeheight2 = count($tree_height2);
        $treeheight3 = count($tree_height3);
        $treeheight4 = count($tree_height4);
        $treeheight5 = count($tree_height5);
       
        /*$tree = DB::table('trees')
           ->select(
            DB::raw('age_range as age_range'),
            DB::raw('count(*) as number'))
           ->groupBy('age_range')
           ->get();*/
         
    	return view('adminpanel.dashboard', compact('data','adminName','admin','adminimage','agecount1','agecount2','agecount3','agecount4','agecount5','agecount6','treeheight1','treeheight2','treeheight3','treeheight4','treeheight5'));
    }

   
}