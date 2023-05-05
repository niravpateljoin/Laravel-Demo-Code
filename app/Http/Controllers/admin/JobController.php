<?php

namespace App\Http\Controllers\adminpanel;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Job;
use App\Models\Category;
use App\Models\User;
use App\Models\Manager;
use App\Models\Jobimage;
use App\Models\Admin;
use App\Models\Tree;
use Validator;
use Illuminate\Pagination\Paginator;
use DB;
use Carbon\Carbon;
class JobController extends Controller 
{
    private $pagination = 10;

    public function manage() 
    {
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

        $task = Job::query()->count();
        $todo = Job::where('status',1)->count();
        $inprogress = Job::where('status',2)->count();
        $done = Job::where('status',3)->count();
        $data = Job::query()->orderby('id','desc')->paginate($this->pagination);
        $job = Job::query()->where('status',1)->where('status',2)->where('status',3)->get();
        $Todo = DB::table('jobs')
                ->select('jobs.*','managers.first_name','users.first_name as firstName')
                ->join('managers','jobs.manager_id','managers.id')
                ->join('users','jobs.user_id','users.id')
                ->where('jobs.status',1)
                ->orderby('job_date','ASC')
                ->orderby('start_time','ASC')
                ->paginate($this->pagination);
      
        $Inprogress = DB::table('jobs')
                ->select('jobs.*','managers.first_name','users.first_name as firstName')
                ->join('managers','jobs.manager_id','managers.id')
                ->join('users','jobs.user_id','users.id')
                ->where('jobs.status',2)
                ->orderby('job_date','ASC')
                ->orderby('start_time','ASC')
                ->paginate($this->pagination);
     
        $Done = DB::table('jobs')
                ->select('jobs.*','managers.first_name','users.first_name as firstName')
                ->join('managers','jobs.manager_id','managers.id')
                ->join('users','jobs.user_id','users.id')
                ->where('jobs.status',3)
                ->orderby('job_date','ASC')
                ->orderby('start_time','ASC')
                ->paginate($this->pagination);

        $manager = Manager::query()->get();
        $user = User::query()->get();
        //$category = Category::query()->get();

        return view('adminpanel.managejob', compact('data','task','todo','inprogress','done','Todo','Inprogress','Done','adminName','manager','user','admin','job','adminimage'));
    }

    public function search(Request $request)
    {

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

        $data1 = array(
            'Admins' => Admin::count(),
            'Users' => User::count(),
            'Managers' => Manager::count(),
            'To do' => Job::where('status',1)->count(),
            'In progress' => Job::where('status',2)->count(),
            'Done' => Job::where('status',3)->count(),
            'Task' => Job::where('status',1)->count() + Job::where('status',2)->count() + Job::where('status',3)->count(),
        );

        $manager = Manager::query()->get();
        $task = Job::query()->count();
        $todo = Job::where('status',1)->count();
        $inprogress = Job::where('status',2)->count();
        $done = Job::where('status',3)->count();
        $data = Job::query()->orderby('id','desc')->paginate($this->pagination);
        $job = Job::query()->where('status',1)->where('status',2)->where('status',3)->get();

        $input = $request->all();
        $qry = Job::query()->where('status',1);
        //$qry = Job::whereBetween('job_date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->where('status',1); 
        
        if(trim($input["search"])!="") 
        {
            $search = $input["search"];

            if (strpos($search, 'W') !== false) {
                $selected_week_start_day = date("Y-m-d", strtotime($input["search"]));
                $create_carbon_week = new Carbon($selected_week_start_day);
                $weekStartDate = $create_carbon_week->startOfWeek()->format('Y-m-d');
                $weekEndDate = $create_carbon_week->endOfWeek()->format('Y-m-d');

                $qry = Job::whereBetween('job_date', [$weekStartDate, $weekEndDate])->where('status',1);
            }
            else{
                $qry->where([
                    ["job_date", "like", "%{$search}%"],
                ]);
            }
        }

        $Todo = $qry->paginate($this->pagination);
        $Todo->appends($input);

        $input = $request->all();
        $qry1 = Job::query()->where('status',2); 
        //$qry1 = Job::whereBetween('job_date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->where('status',2);
        $job_date = "";
        if(trim($input["search"])!="") 
        {
            $job_date = $search = $input["search"];
            
            if (strpos($search, 'W') !== false) {
                $selected_week_start_day = date("Y-m-d", strtotime($input["search"]));
                $create_carbon_week = new Carbon($selected_week_start_day);
                $weekStartDate = $create_carbon_week->startOfWeek()->format('Y-m-d');
                $weekEndDate = $create_carbon_week->endOfWeek()->format('Y-m-d');

                $qry1 = Job::whereBetween('job_date', [$weekStartDate, $weekEndDate])->where('status',2);
            }
            else{
                $qry1->where([
                    ["job_date", "like", "%{$search}%"],
                ]);
            }
        }
        $Inprogress = $qry1->paginate($this->pagination);
        $Inprogress->appends($input);

        $input = $request->all();
        $qry2 = Job::query()->where('status',3); 
        //$qry2 = Job::whereBetween('job_date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->where('status',3);
        if(trim($input["search"])!="") 
        {
            $search = $input["search"];
            
            if (strpos($search, 'W') !== false) {
                $selected_week_start_day = date("Y-m-d", strtotime($input["search"]));
                $create_carbon_week = new Carbon($selected_week_start_day);
                $weekStartDate = $create_carbon_week->startOfWeek()->format('Y-m-d');
                $weekEndDate = $create_carbon_week->endOfWeek()->format('Y-m-d');

                $qry2 = Job::whereBetween('job_date', [$weekStartDate, $weekEndDate])->where('status',3);
            }
            else{
                $qry2->where([
                    ["job_date", "like", "%{$search}%"],
                ]);
            }
        }
        $Done = $qry2->paginate($this->pagination);
        $Done->appends($input);

        return view('adminpanel.managejob', compact('data','adminName','data1','manager','admin','Todo','Inprogress','Done','task','todo','inprogress','done','job_date','adminimage'));

    }

    public function searchjobmanager(Request $request)
    {
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

        $data1 = array(
            'Admins' => Admin::count(),
            'Users' => User::count(),
            'Managers' => Manager::count(),
            'To do' => Job::where('status',1)->count(),
            'In progress' => Job::where('status',2)->count(),
            'Done' => Job::where('status',3)->count(),
            'Task' => Job::where('status',1)->count() + Job::where('status',2)->count() + Job::where('status',3)->count(),
        );

        $manager = Manager::query()->get();
        $task = Job::query()->count();
        $todo = Job::where('status',1)->count();
        $inprogress = Job::where('status',2)->count();
        $done = Job::where('status',3)->count();
        $data = Job::query()->orderby('id','desc')->paginate($this->pagination);
        $job = Job::query()->where('status',1)->where('status',2)->where('status',3)->get();

        $input = $request->all();
        $qry = Job::query()->where('status',1); 

        if(trim($input["manager_id"])!="") 
        {
            $manager_id = $input["manager_id"];
            $qry->where([
                ["manager_id", "like", "%{$manager_id}%"],
            ]);
        }

        $Todo = $qry->paginate($this->pagination);
        $Todo->appends($input);

        $input = $request->all();
        $qry1 = Job::query()->where('status',2); 

        
        if(trim($input["manager_id"])!="") 
        {
            $qry1->where([
                ["manager_id", "like", "%{$manager_id}%"],
            ]);
        }
        $Inprogress = $qry1->paginate($this->pagination);
        $Inprogress->appends($input);

        $input = $request->all();
        $qry2 = Job::query()->where('status',3); 

        if(trim($input["manager_id"])!="") 
        {
            $manager_id = $input["manager_id"];
            $qry2->where([
                ["manager_id", "like", "%{$manager_id}%"],
            ]);
        }
        $Done = $qry2->paginate($this->pagination);
        $Done->appends($input);

        return view('adminpanel.managejob', compact('data','adminName','data1','manager','admin','Todo','Inprogress','Done','task','todo','inprogress','done','adminimage'));

    }

    
    /*public function search(Request $request) 
    {
        $input = $request->all();
        $qry = Job::query(); 

        if(trim($input["search"])!="") 
        {
            $search = $input["search"];
            $qry->where([
                ["job_date", "like", "%{$search}%"],
            ]);
        }

        $data = $qry->paginate($this->pagination);
        $data->appends($input);

        return view('adminpanel.managejob', compact('data'));
    }*/

    public function add($id) 
    {    

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

        $data1 = array(
            'Admin' => Admin::where('id','=',$loginUserId)->get(),
            'Admins' => Admin::count(),
            'Users' => User::count(),
            'Managers' => Manager::count(),
            'To do' => Job::where('status',1)->count(),
            'In progress' => Job::where('status',2)->count(),
            'Done' => Job::where('status',3)->count(),
            'Task' => Job::where('status',1)->count() + Job::where('status',2)->count() + Job::where('status',3)->count(),
        );

        $task = Job::query()->count();
        $todo = Job::where('status',1)->count();
        $inprogress = Job::where('status',2)->count();
        $done = Job::where('status',3)->count();
        $manager = Manager::query()->get();
        $user = User::query()->get();
        //$category = Category::query()->get();
        $input = Job::where('tree_id', '=', $id)->get();
        $address = Tree::where('id','=', $id)->get();
        $data = array('type'=>'add', 'input'=>$input,'manager' => $manager,'data1' => $data1,'user' => $user,'treeId' => $id);

        return view('adminpanel.addjob', compact('data','data1','manager','user','adminName','task','todo','inprogress','done','admin','address','adminimage'));
    }

    public function save(Request $request) 
    {
        $input = $request->all();

        $id = $input['id'];
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

        $data1 = array(
            'Admin' => Admin::where('id','=',$loginUserId)->get(),
            'Admins' => Admin::count(),
            'Users' => User::count(),
            'Managers' => Manager::count(),
            'To do' => Job::where('status',1)->count(),
            'In progress' => Job::where('status',2)->count(),
            'Done' => Job::where('status',3)->count(),
            'Task' => Job::where('status',1)->count() + Job::where('status',2)->count() + Job::where('status',3)->count(),
        );
        $validator=Validator::make($input,$this->getRules('Add',$input),$this->messages());

        $category = Category::query()->get();
        $manager = Manager::query()->get();
        $user = User::query()->get();

        if($validator->fails())
        {
            $data = array('type'=>'add','category' => $category,'adminName' => $adminName,'data1'=>$data1,'manager' => $manager,'user'=> $user,'input'=>$input,'error'=>$validator->messages());

            return view('adminpanel.addjob', compact('data','adminName','data1','admin','adminimage'));
            exit();
        }

        $input['manager_id'] = $input['manager_id'];
        $input['user_id'] = $input['user_id'];
        $input['description'] = $input['description'];
        $input['start_time'] = $input['start_time'];
        $input['end_time'] = $input['end_time'];
        $input['tree_id'] = $input['treeId'];

        $input['status'] = 1;

        $job = Job::create($input);

        if($job->id>0) 
        {
            return redirect()->route('adminpanel.job.manage')->with('success', 'Created successfully.');
        } 
        else 
        {
            return redirect('/adminpanel/addjob/'.$id)->with('success', 'Added Successfully');
           // return redirect()->route('adminpanel.job.add')->withErrors(['Error creating record. Please try again.']);
        }
    }

    public function edit($id) 
    {
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
        $data1 = array(
            'Admins' => Admin::count(),
            'Users' => User::count(),
            'Managers' => Manager::count(),
            'To do' => Job::where('status',1)->count(),
            'In progress' => Job::where('status',2)->count(),
            'Done' => Job::where('status',3)->count(),
            'Task' => Job::where('status',1)->count() + Job::where('status',2)->count() + Job::where('status',3)->count(),
        );

        $task = Job::query()->count();
        $todo = Job::where('status',1)->count();
        $inprogress = Job::where('status',2)->count();
        $done = Job::where('status',3)->count();

        $how_hear_abouts = array();

        //$category = Category::query()->get();
        $manager = Manager::query()->get();
        $user = User::query()->get();
        $input = Job::where('id', '=', $id)->get();
        $address = Tree::where('id','=', $id)->get();
        foreach ($input as $d) 
        {
            $user_id = $d->user_id;
            $getUser = User::where('id', '=', $user_id)->get();
            if (count($getUser) > 0) 
            {
                $d->getUserName = $getUser[0]->first_name;
            }
        }        
        $data = array('type'=>'edit', 'input'=>$input, 'how_hear_abouts'=>$how_hear_abouts,'manager' => $manager,'data1'=>$data1,'user' => $user,'address'=>$address);

        return view('adminpanel.addjob', compact('data','manager','user','adminName','task','todo','inprogress','done','data1','admin','address','adminimage'));
    }

    public function update(Request $request) 
    {
        $input = $request->all();

        $id = $input['id'];

        $validator = Validator::make( $input, $this->getRules('Edit', $input), $this->messages()); 

        $manager = Manager::query()->get();
        $user = User::query()->get();

        if ($validator->fails())
        { 
            $data = array('type'=>'Edit','manager'=>$manager,'user'=>$user,'input'=>$input,'error'=>$validator->messages());

            return view('adminpanel.addjob', compact('data','admin'));
            exit();
        }

       // $update["manager_id"] = $input['manager_id'];
        //$update["user_id"] = $input['user_id'];
        $update["job_title"] = $input['job_title'];
        $update["description"] = $input['description'];
        $update["job_date"] = $input['job_date'];
        $update["start_time"] = $input['start_time'];
        $update["end_time"] = $input['end_time'];
        //$update["category_id"] = $input['category_id'];
        //$update["tags"] = $input['tags'];
        $update["address"] = $input['address'];
        //$update["latitude"] = $input['latitude'];
        //$update["longitude"] = $input['longitude'];
        $update["status"] = 1;

        $job = Job::where('id', '=', $id)->update($update);

        return redirect()->route('adminpanel.job.manage')->with('success', 'Updated successfully.');
    }

    public function delete($id) 
    {
        Job::where('id','=',$id)->delete();

        return redirect()->route('adminpanel.job.manage')->with('success', 'Deleted successfully.');
    }

    public function view($id) 
    {
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

        $assignUser = DB::table('jobs')
            ->select('jobs.*','users.first_name as firstName','managers.first_name','trees.location')
            ->leftjoin('users','jobs.user_id','users.id')
            ->leftjoin('managers','jobs.manager_id','managers.id')
            ->leftjoin('trees','jobs.tree_id','trees.id')
            ->where('jobs.id',$id)
            ->get();

        $jobimage = Jobimage::where('job_id','=',$id)->get();

        return view('adminpanel.viewjob', compact('assignUser','jobimage','adminName','admin','adminimage'));
    }

    public function updateAll(Request $request)  
    {  
        $ids = $request->ids;  
        $taskStatus = $request->taskStatus;
        $catIds = explode(',',$ids);
        if($taskStatus == 'New'){
            $update['status'] = 1;
        }
        if($taskStatus == 'Inprocess'){
            $update['status'] = 2;
        }
        if($taskStatus == 'Done'){
            $update['status'] = 3;
        }
        
        $del=0;
        for($cat=0; $cat<count($catIds); $cat++){
            Job::where('id',$catIds[$cat])->update($update);
            $del++;     
        }
        
        echo $del;
        
    }

    private function getRules($type, $input) 
    {

        $return = array();
        return $return;

    }



    private function messages() {

        return [

            

        ];

    }



    private function getRequiredMessage($string) {

        return 'The ' . $string . ' field is required.';

    }



    private function getGreaterMessage($string, $maxchar) {

        return 'The ' . $string . ' may not be greater than ' . $maxchar . ' characters.';

    }



    public function ajaxgetuser($id)

    {

        $arr['data'] = User::Where('manager_id','=',$id)->get(); 

        echo json_encode($arr);

    }

}



?>