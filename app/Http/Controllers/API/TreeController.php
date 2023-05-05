<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tree;
use App\Models\Treeimage;
use App\Models\Job;
use App\Models\User;
use App\Models\Treereport;
use Validator;
use Illuminate\Support\Facades\Auth;
use DB;

class TreeController extends Controller
{
    public function get_treeDetail(Request $request) 
    { 
        $validator = Validator::make($request->all(), [
            'qrcode_id' => 'required', 
            'user_token' => 'required',
            'user_id' => 'required',
        ]);
        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }

        $input = $request->all(); 
        $id = $input['user_id'];

        $msg1="Tree Data found";
        $msg2="This Tree Data Is Not Found";
        $msg3="You are not authorized please login again";

        $verifiedUserId = Controller::checkToken($input['user_id'],$input['user_token']);
        if($verifiedUserId == $id)
		{
			
            $getTree = Tree::where('id', $input['qrcode_id'])->get();

	        if($getTree->count())
	        {
                for ($g=0; $g < count($getTree); $g++) 
                {
                    $treeImg = Treeimage::where('tree_id','=',$getTree[$g]->id)->where('status','=',0)->get();
           
                    $treeimgArray = array();
                    for ($u=0; $u < count($treeImg); $u++) 
                    {
                        $treeimgArray[] = array(
                            'tree_image_id' => $treeImg[$u]->id,
                            'tree_image_url' => asset('/uploads/Tree_Images/'.$treeImg[$u]->treeImage),
                            'tree_image_date' =>  $treeImg[$u]->treeimage_date
                        );
                    }


                    $treeData[] = array(
                        'Id' => $getTree[$g]->id,
                        'treeId' => $getTree[$g]->treeid,
                        'location' => $getTree[$g]->location,
                        'address' => $getTree[$g]->address,
                        'species' =>  $getTree[$g]->species,
                        'ageRange' => $getTree[$g]->age_range,
                        'vitality'  => $getTree[$g]->vitality,
                        'soilType' => $getTree[$g]->soil_type,
                        'height' => $getTree[$g]->height,
                        'trunkDiameter' => $getTree[$g]->trunk_diameter,
                        'defects' => $getTree[$g]->defects,
                        'date_planted' => $getTree[$g]->date_planted,
                        'comment' => $getTree[$g]->comments,
                        'updatedPerson_Date' => $getTree[$g]->updated_id." ".$getTree[$g]->updatedDate,
                        'treeImageList' => $treeimgArray,
                    );
                }

	        	return response()->json(['status'=>1,'message'=>$msg1, 'result'=>$treeData]);
	        }
	        else
            {
	        	return response()->json(['status'=>0,'message'=>$msg2]);
	        }
		}
        else
        {
            return response()->json(['status'=>0,'message'=>$msg3]);
        }
    }

    public function get_treeList(Request $request) 
    { 
        $validator = Validator::make($request->all(), [ 
            'user_token' => 'required',
            'user_id' => 'required',
            //'search_tree_id' => 'required',
            'offset' => 'required',
            'limit' => 'required',

        ]);
        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }

        $input = $request->all(); 
        $id = $input['user_id'];
        $limit = $input['limit'];
        $offset = $input['offset'];

        $msg1="Tree Data found";
        $msg2="This Tree Data Is Not Found";
        $msg3="You are not authorized please login again";

        $verifiedUserId = Controller::checkToken($input['user_id'],$input['user_token']);
        if($verifiedUserId == $id)
        {
            if($input['search_tree_id'] != "")
            {
                $getTreeList = DB::select( DB::raw("SELECT * FROM trees WHERE treeid LIKE '%".$input['search_tree_id']."%' order by updated_at DESC LIMIT $offset,$limit"));
                
           /*     $getTreeList = Tree::query()
                                ->where('id','=',$input['search_tree_id'])
                                ->offset($offset)
                                ->limit($offset)
                                ->get();
*/
                if(count($getTreeList) > 0)
                {
                    for ($g=0; $g < count($getTreeList); $g++) 
                    {
                       $treeListData[] = array(
                            'Id' => $getTreeList[$g]->id,
                            'treeId' => $getTreeList[$g]->treeid,
                            'location' => $getTreeList[$g]->location,
                            'address' => $getTreeList[$g]->address,
                            'species' =>  $getTreeList[$g]->species,
                            'ageRange' => $getTreeList[$g]->age_range,
                            'vitality'  => $getTreeList[$g]->vitality,
                            'soilType' => $getTreeList[$g]->soil_type,
                            'height' => $getTreeList[$g]->height,
                            'trunkDiameter' => $getTreeList[$g]->trunk_diameter,
                            'defects' => $getTreeList[$g]->defects,
                            'date_planted' => $getTreeList[$g]->date_planted,
                            'comment' => $getTreeList[$g]->comments,
                            'updatedPerson_Date' => $getTreeList[$g]->updated_id." ".$getTreeList[$g]->updatedDate,
                       );
                    }

                    return response()->json(['status'=>1,'message'=>$msg1, 'result'=>$treeListData]);
                }
                else
                {
                    return response()->json(['status'=>0,'message'=>$msg2]);
                }
            }
            else
            {
                $getTreeList = Tree::query()
                                ->offset($offset)
                                ->limit($limit)
                                ->orderBy('updated_at','DESC')
                                ->get();

                if($getTreeList->count())
                {
                    for ($g=0; $g < count($getTreeList); $g++) 
                    {
                       $treeListData[] = array(
                            'Id' => $getTreeList[$g]->id,
                            'treeId' => $getTreeList[$g]->treeid,
                            'location' => $getTreeList[$g]->location,
                            'address' => $getTreeList[$g]->address,
                            'species' =>  $getTreeList[$g]->species,
                            'ageRange' => $getTreeList[$g]->age_range,
                            'vitality'  => $getTreeList[$g]->vitality,
                            'soilType' => $getTreeList[$g]->soil_type,
                            'height' => $getTreeList[$g]->height,
                            'trunkDiameter' => $getTreeList[$g]->trunk_diameter,
                            'defects' => $getTreeList[$g]->defects,
                            'date_planted' => $getTreeList[$g]->date_planted,
                            'comment' => $getTreeList[$g]->comments,
                            'updatedPerson_Date' => $getTreeList[$g]->updated_id." ".$getTreeList[$g]->updatedDate,
                       );
                    }

                    return response()->json(['status'=>1,'message'=>$msg1, 'result'=>$treeListData]);
                }
                else
                {
                    return response()->json(['status'=>0,'message'=>$msg2]);
                }
            }
        }
        else
        {
            return response()->json(['status'=>0,'message'=>$msg3]);
        }
    }

    public function save_treereport(Request $request) 
    { 
        $validator = Validator::make($request->all(), [
            'user_id' => 'required', 
            'user_token' => 'required',
            'subject' => 'required', 
            'location' => 'required', 
            'issue_details' => 'required',
            'date' => 'required',
            'time' => 'required',

        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }
        
        //-------------------Message------------------------------
        $msg1 = "Tree Report Added Successfully";
        $msg2 = "Tree Report not added";
        $msg3 = "Task Not Found";
        $msg4 = "Tree Not Found";
        $msg5 = "You are not authorized please login again";
        //-------------------Message------------------------------  

        $input = $request->all();
        $id = $input['user_id'];
                        
        $verifiedUserId = Controller::checkToken($input['user_id'],$input['user_token']);
        if($verifiedUserId == $id)
        {
            
            if(isset($input['task_id']))
            {
                $gettask = Job::query()->where('id',$input['task_id'])->get();

                if(count($gettask)>0)
                {
                    $treereport['user_id'] = $input['user_id'];
                    $treereport['subject'] = $input['subject'];
                    $treereport['location'] = $input['location'];
                    $treereport['issue_details'] = $input['issue_details'];
                    $treereport['date'] = date('Y-m-d',strtotime($input['date']));
                    $treereport['time'] = $input['time'];
                    $treereport['task_id'] = $input['task_id'];
                    $treereport['tree_id'] = '';

                    $addtreereport = Treereport::create($treereport);

                    return response()->json(['status'=>1,'message'=>$msg1]); 
                }
                else
                {
                    return response()->json(['status'=>0,'message'=>$msg3]); 
                }
            }
            else if(isset($input['tree_id']))
            {
                $gettree = Tree::query()->where('id',$input['tree_id'])->get();

                if(count($gettree)>0)
                {
                    $treereport['user_id'] = $input['user_id'];
                    $treereport['subject'] = $input['subject'];
                    $treereport['location'] = $input['location'];
                    $treereport['issue_details'] = $input['issue_details'];
                    $treereport['date'] = date('Y-m-d',strtotime($input['date']));
                    $treereport['time'] = $input['time'];
                    $treereport['tree_id'] = $input['tree_id'];
                    $treereport['task_id'] = '';

                    $addtreereport = Treereport::create($treereport);

                    return response()->json(['status'=>1,'message'=>$msg1]); 
                }
                else
                {
                    return response()->json(['status'=>0,'message'=>$msg4]); 
                }
            }
        }
        else
        {
            return response()->json(['status'=>0,'message'=>$msg5]); 
        }
    }

    public function edit_treereport(Request $request) 
    { 
        $validator = Validator::make($request->all(), [
            'user_id' => 'required', 
            'user_token' => 'required',
            'report_id' => 'required',  
            'subject' => 'required', 
            'location' => 'required', 
            'issue_details' => 'required',
            'date' => 'required',

        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }
        
        //-------------------Message------------------------------
        $msg1 = "Tree Report Updated Successfully";
        $msg2 = "Tree Report not Update";
        $msg3 = "Tree Not Found";
        $msg4 = "Task Not Found";
        $msg5 = "You are not authorized please login again";
        //-------------------Message------------------------------  

        $input = $request->all();
        $id = $input['user_id'];
                        
        $verifiedUserId = Controller::checkToken($input['user_id'],$input['user_token']);
        if($verifiedUserId == $id)
        {
            $treereport = Treereport::query()->where('id','=',$input['report_id'])->get();

            if(count($treereport)>0)
            {
                if(isset($input['task_id']))
                {
                    $gettree = Job::query()->where('id',$input['task_id'])->get();

                    if(count($gettree)>0)
                    {
                        $updatetreereport['user_id'] = $input['user_id'];
                        $updatetreereport['subject'] = $input['subject'];
                        $updatetreereport['location'] = $input['location'];
                        $updatetreereport['issue_details'] = $input['issue_details'];
                        $updatetreereport['date'] = date('Y-m-d',strtotime($input['date']));
                        $updatetreereport['time'] = $input['time'];
                        $updatetreereport['task_id'] = $input['task_id'];
                        $updatetreereport['tree_id'] = '';

                        $edittreereport = Treereport::query()->where('id','=',$input['report_id'])->update($updatetreereport);

                        return response()->json(['status'=>1,'message'=>$msg1]); 
                    }
                    else
                    {
                        return response()->json(['status'=>0,'message'=>$msg3]); 
                    }
                }
                else if(isset($input['tree_id']))
                {
                    $gettask = Tree::query()->where('id',$input['tree_id'])->get();

                    if(count($gettask)>0)
                    {
                        $updatetreereport['user_id'] = $input['user_id'];
                        $updatetreereport['subject'] = $input['subject'];
                        $updatetreereport['location'] = $input['location'];
                        $updatetreereport['issue_details'] = $input['issue_details'];
                        $updatetreereport['date'] = date('Y-m-d',strtotime($input['date']));
                        $updatetreereport['time'] = $input['time'];
                        $updatetreereport['tree_id'] = $input['tree_id'];
                        $updatetreereport['task_id'] = '';

                        $edittreereport = Treereport::query()->where('id','=',$input['report_id'])->update($updatetreereport);

                        return response()->json(['status'=>1,'message'=>$msg1]); 
                    }
                    else
                    {
                        return response()->json(['status'=>0,'message'=>$msg4]); 
                    }
                }
            }
            else
            {
                return response()->json(['status'=>0,'message'=>$msg2]);
            }
        }
        else
        {
            return response()->json(['status'=>0,'message'=>$msg5]); 
        }
    }

    public function edit_treedetail(Request $request) 
    { 
        $validator = Validator::make($request->all(), [
            'user_id' => 'required', 
            'user_token' => 'required',
            'tree_id' => 'required',
            'address' => 'required',
            'location' => 'required',
            'species' => 'required', 
            'age_range' => 'required', 
            'vitality' => 'required',
            'soil_type' => 'required',
            'height' => 'required',
            'trunk_diameter' => 'required',
            'date_planted' => 'required',
            'comments' => 'required',
            'image_counter' => 'required',

        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }
        
        //-------------------Message------------------------------
        $msg1 = "Tree Data Updated Successfully";
        $msg2 = "Tree Data not Updated";
        $msg3 = "You are not authorized please login again";
        //-------------------Message------------------------------  

        $input = $request->all();
        $id = $input['user_id'];
                        
        $verifiedUserId = Controller::checkToken($input['user_id'],$input['user_token']);
        if($verifiedUserId == $id)
        {
            $gettreeDetail = Tree::query()->where('id','=',$input['tree_id'])->get();

            if(count($gettreeDetail)>0)
            {
                $user = User::where('id','=',$input['user_id'])->get();
            
                if (count($user)>0) 
                {
                    $userName = $user[0]->first_name." ".$user[0]->last_name;
                   
                }
                $update["updated_id"] = $userName;
                
                date_default_timezone_set('Asia/Singapore'); 
                $t_date = date("Y-m-d h:i:s");

                $update['updatedDate'] = $t_date;
                $update['address'] = $input['address'];
                $update['location'] = $input['location'];
                $update["species"] = $input['species'];
                $update["height"] = $input['height'];
                $update["trunk_diameter"] = $input['trunk_diameter'];
                $update["defects"] = $input['defects'];
                $update['date_planted'] = $input['date_planted'];
                $update["comments"] = $input['comments'];  
                $update["age_range"] = $input['age_range'];
                $update["vitality"] = $input['vitality'];
                $update["soil_type"] = $input['soil_type'];  

                $tree = Tree::where('id', '=', $input['tree_id'])->update($update);
        
                if($input['image_counter'] > 0)
                {            
                    for($g=1; $g <= $input['image_counter']; $g++)
                    {   
                        if(isset($input['image'.$g])) 
                        {                    
                            $imagePath = $input['image'.$g];
                            $extension  = pathinfo($imagePath->getClientOriginalName(), PATHINFO_EXTENSION);
                            $filename = rand(0000,9999)."treesImage.".$extension;
                            $upload_dir_path = public_path()."/uploads/Tree_Images";
                            $imagePath->move($upload_dir_path, $filename );
                            $addtreeimage['treeImage'] = $filename;
                            $addtreeimage['tree_id'] = $input['tree_id'];
                            $addtreeimage['treeimage_date'] = date('Y-m-d');
                            $addtreeimage['status'] = 0;
                            $treeimage = Treeimage::create($addtreeimage);
                        }
                    }
                }
                $curl = curl_init();
        
                $link = 'http://treespower.com.sg/treespower/viewtree/'.$input['tree_id'];
                $qrlink = urlencode($link);

                curl_setopt_array($curl, array(
                      CURLOPT_URL => 'http://treespower.com.sg/treespower/public/qrDemo/index.php?contentId='.$qrlink,
                      CURLOPT_RETURNTRANSFER => true,
                      CURLOPT_ENCODING => '',
                      CURLOPT_MAXREDIRS => 60,
                      CURLOPT_TIMEOUT => 0,
                      CURLOPT_FOLLOWLOCATION => true,
                      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                      CURLOPT_CUSTOMREQUEST => 'GET',
                    ));

                $response = curl_exec($curl);
                
                curl_close($curl);
                $result = json_decode($response, true);
                $success = isset($result['success']) ? $result['success'] : 1;  
                if($success == 1) 
                {
                    $addtreeqr["qrImage"] = $result['fileName'];
                   
                }
                $treeqr = Tree::where('id', '=', $input['tree_id'])->update($addtreeqr);

                return response()->json(['status'=>1,'message'=>$msg1]); 

            }
            else
            {
                return response()->json(['status'=>0,'message'=>$msg2]); 
            }
        }
        else
        {
            return response()->json(['status'=>0,'message'=>$msg3]); 
        }
    }

    public function get_newTreeReport(Request $request) 
    { 
        $validator = Validator::make($request->all(), [
            'user_id' => 'required', 
            'user_token' => 'required',
            'report_status' => 'required',
        ]);
        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }

        $input = $request->all(); 
        $id = $input['user_id'];

        $msg1="New Tree Report Data found";
        $msg2="New Tree Report Data Is Not Found";
        $msg3="You are not authorized please login again";

        $verifiedUserId = Controller::checkToken($input['user_id'],$input['user_token']);
        if($verifiedUserId == $id)
        {
            if($input['report_status']=='New')
            {
                $newreportstatus = Treereport::where('user_id','=',$input['user_id'])->where('status','=',$input['report_status'])->get();

                if($newreportstatus->count())
                {
                    for ($g=0; $g < count($newreportstatus); $g++) 
                    {

                       $newReportData[] = array(
                            'report_id' => $newreportstatus[$g]->id,
                            'user_id' => $newreportstatus[$g]->user_id,
                            'subject' => $newreportstatus[$g]->subject,
                            'location' => $newreportstatus[$g]->location,
                            'issue_details' => $newreportstatus[$g]->issue_details,
                            'date' => $newreportstatus[$g]->date,
                            'time' => $newreportstatus[$g]->time,
                            'task_id' => $newreportstatus[$g]->task_id,
                            'tree_id' => $newreportstatus[$g]->tree_id,
                            'report_status' => $newreportstatus[$g]->status
                        );
                    }

                    return response()->json(['status'=>1,'message'=>$msg1, 'result'=>$newReportData]);
                }
                else
                {
                    return response()->json(['status'=>0,'message'=>$msg2]);
                }
            }   
            else
            {
                return response()->json(['status'=>0,'message'=>$msg2]);
            }
        }
        else
        {
            return response()->json(['status'=>0,'message'=>$msg3]);
        }
    }

    public function get_checkedTreeReport(Request $request) 
    { 
        $validator = Validator::make($request->all(), [
            'user_id' => 'required', 
            'user_token' => 'required',
            'report_status' => 'required',
        ]);
        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }

        $input = $request->all(); 
        $id = $input['user_id'];

        $msg1="Check Tree Report Data found";
        $msg2="Check Tree Report Data Is Not Found";
        $msg3="You are not authorized please login again";

        $verifiedUserId = Controller::checkToken($input['user_id'],$input['user_token']);
        if($verifiedUserId == $id)
        {
            if($input['report_status']=='Checked')
            {
                $checkreportstatus = Treereport::where('user_id','=',$input['user_id'])->where('status','=',$input['report_status'])->get();

                if($checkreportstatus->count())
                {
                    for ($g=0; $g < count($checkreportstatus); $g++) 
                    {

                       $checkReportData[] = array(
                            'report_id' => $checkreportstatus[$g]->id,
                            'user_id' => $checkreportstatus[$g]->user_id,
                            'subject' => $checkreportstatus[$g]->subject,
                            'location' => $checkreportstatus[$g]->location,
                            'issue_details' => $checkreportstatus[$g]->issue_details,
                            'date' => $checkreportstatus[$g]->date,
                            'time' => $checkreportstatus[$g]->time,
                            'task_id' => $checkreportstatus[$g]->task_id,
                            'tree_id' => $checkreportstatus[$g]->tree_id,
                            'report_status' => $checkreportstatus[$g]->status
                        );
                    }

                    return response()->json(['status'=>1,'message'=>$msg1, 'result'=>$checkReportData]);
                }
                else
                {
                    return response()->json(['status'=>0,'message'=>$msg2]);
                }
            }   
            else
            {
                return response()->json(['status'=>0,'message'=>$msg2]);
            }
        }
        else
        {
            return response()->json(['status'=>0,'message'=>$msg3]);
        }
    }

    public function get_treeReport(Request $request) 
    { 
        $validator = Validator::make($request->all(), [
            'user_id' => 'required', 
            'user_token' => 'required',
            'report_id' => 'required',
        ]);
        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }

        $input = $request->all(); 
        $id = $input['user_id'];

        $msg1="Tree Report Data found";
        $msg2="Tree Report Data Is Not Found";
        $msg3="You are not authorized please login again";

        $verifiedUserId = Controller::checkToken($input['user_id'],$input['user_token']);
        if($verifiedUserId == $id)
        {
            $treereport = Treereport::where('id','=',$input['report_id'])->get();

            if($treereport->count())
            {
                for ($g=0; $g < count($treereport); $g++) 
                {
                    $ReportData[] = array(
                        'report_id' => $treereport[$g]->id,
                        'user_id' => $treereport[$g]->user_id,
                        'subject' => $treereport[$g]->subject,
                        'location' => $treereport[$g]->location,
                        'issue_details' => $treereport[$g]->issue_details,
                        'date' => $treereport[$g]->date,
                        'time' => $treereport[$g]->time,
                        'task_id' => $treereport[$g]->task_id,
                        'tree_id' => $treereport[$g]->tree_id,
                        'report_status' => $treereport[$g]->status
                    );
                }

                return response()->json(['status'=>1,'message'=>$msg1, 'result'=>$ReportData]);
            }
            else
            {
                return response()->json(['status'=>0,'message'=>$msg2]);
            }
        }
        else
        {
            return response()->json(['status'=>0,'message'=>$msg3]);
        }
    }

    public function delete_treeimage(Request $request) 
    { 
        $validator = Validator::make($request->all(), [
            'user_id' => 'required', 
            'user_token' => 'required',
            'treeid' => 'required', 
            'imageid' => 'required',
        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }
        
        //-------------------Message------------------------------
        $msg1 = "Tree Image Deleted Successfully";
        $msg2 = "Tree Image not Deleted";
        $msg3 = "Tree Image Not Found";
        $msg5 = "You are not authorized please login again";
        //-------------------Message------------------------------  

        $input = $request->all();
        $id = $input['user_id'];
                        
        $verifiedUserId = Controller::checkToken($input['user_id'],$input['user_token']);
        if($verifiedUserId == $id)
        {
            $gettreeImage = Treeimage::query()->where('id',$input['imageid'])->where('tree_id',$input['treeid'])->where('status',0)->get();

            if(count($gettreeImage)>0)
            {
                $update['status'] = 1;

                $treeimage = Treeimage::where('id','=',$input['imageid'])->update($update);

                $treei = DB::table('treeimages')
                            ->select('treeimages.*','trees.id as tid')
                            ->join('trees','treeimages.tree_id','trees.id')
                            ->where('treeimages.status','=',1)
                            ->where('treeimages.id',$input['imageid'])
                            ->get();

                if($treei)
                {
                    $user = User::where('id','=',$input['user_id'])->get();
            
                    if (count($user)>0) 
                    {
                        $userName = $user[0]->first_name." ".$user[0]->last_name;
                       
                    }
                    $update1["updated_id"] = $userName;
                    date_default_timezone_set('Asia/Singapore'); 
                    $t_date = date("Y-m-d h:i:s");

                    $update1['updatedDate'] = $t_date;
                    $treeiu = Tree::query()->where('id','=',$treei[0]->tree_id)->update($update1);
                }

                else
                {
                    return response()->json(['status'=>0,'message'=>$msg2]); 
                }
                
            }
            return response()->json(['status'=>1,'message'=>$msg1]); 
        }
        else
        {
            return response()->json(['status'=>0,'message'=>$msg5]); 
        }
    }

    public function get_treeIdDetails(Request $request) 
    { 
        $validator = Validator::make($request->all(), [
            'tree_id' => 'required', 
            'user_token' => 'required',
            'user_id' => 'required',
        ]);
        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }

        $input = $request->all(); 
        $id = $input['user_id'];

        $msg1="Tree Data found";
        $msg2="This Tree Data Is Not Found";
        $msg3="You are not authorized please login again";

        $verifiedUserId = Controller::checkToken($input['user_id'],$input['user_token']);
        if($verifiedUserId == $id)
        {
            
            $getTrees = Tree::where('id', $input['tree_id'])->get();

            if($getTrees->count())
            {
                for ($g=0; $g < count($getTrees); $g++) 
                {
                    $treeImg = Treeimage::where('tree_id','=',$getTrees[$g]->id)->where('status','=',0)->get();
           
                    $treeimgArray = array();
                    for ($u=0; $u < count($treeImg); $u++) 
                    {
                        $treeimgArray[] = array(
                            'tree_image_id' => $treeImg[$u]->id,
                            'tree_image_url' => asset('/uploads/Tree_Images/'.$treeImg[$u]->treeImage),
                            'tree_image_date' =>  $treeImg[$u]->treeimage_date
                        );
                    }

                    $date2 = date('m/d/Y'); $date_planted = $getTrees[$g]->date_planted;
                    $diff = abs(strtotime($date2) - strtotime($date_planted));
                    $years = floor($diff / (365*60*60*24));                    

                    if ($date_planted == '') {
                        $age_range = "0 to 10 years";
                    }
                    elseif ($years >= 0 && $years <= 10) {
                        $age_range = "0 to 10 years";
                    }
                    elseif ($years >= 11 && $years <= 30) {
                        $age_range = "11 to 30 years";
                    }
                    elseif ($years >= 31 && $years <= 50) {
                        $age_range = "31 to 50 years";
                    }
                    elseif ($years >= 51 && $years <= 80) {
                        $age_range = "51 to 80 years";
                    }
                    elseif ($years >= 81 && $years <= 100) {
                        $age_range = "81 to 100 years";
                    }
                    elseif ($years > 100){
                        $age_range = "Above 100 years";
                    }
                    else{
                        $age_range = "0 to 10 years";
                    }                    

                    $treeData[] = array(
                        'Id' => $getTrees[$g]->id,
                        'treeId' => $getTrees[$g]->treeid,
                        'location' => $getTrees[$g]->location,
                        'address' => $getTrees[$g]->address,
                        'species' =>  $getTrees[$g]->species,
                        'ageRange' => $age_range,
                        'vitality'  => $getTrees[$g]->vitality,
                        'soilType' => $getTrees[$g]->soil_type,
                        'height' => $getTrees[$g]->height,
                        'trunkDiameter' => $getTrees[$g]->trunk_diameter,
                        'defects' => $getTrees[$g]->defects,
                        'date_planted' => $getTrees[$g]->date_planted,
                        'comment' => $getTrees[$g]->comments,
                        'updatedPerson_Date' => $getTrees[$g]->updated_id." ".$getTrees[$g]->updatedDate,
                        'treeImageList' => $treeimgArray,
                    );
                }

                return response()->json(['status'=>1,'message'=>$msg1, 'result'=>$treeData]);
            }
            else
            {
                return response()->json(['status'=>0,'message'=>$msg2]);
            }
        }
        else
        {
            return response()->json(['status'=>0,'message'=>$msg3]);
        }
    }
}