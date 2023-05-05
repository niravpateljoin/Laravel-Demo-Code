<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Userpackage;
use Validator;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /* Login api */
   	public function login(Request $request) 
    { 
       	$validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required',
        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }

        $input = $request->all(); 
		$email = $input['email'];        
		$password = $input['password']; 

		$msg1 = "Login Successfully";
		$msg2 = "Username or Password is incorrect. Please try again!";

        if ((Auth::guard('web')->attempt(['email' => $email, 'password' => $password], false))||(Auth::guard('web')->attempt(['phone' => $email, 'password' => $password], false))) 
        {
			$userLogin = User::query()
							->select('*')
							->where('email',$email)
                            ->orwhere('phone',$email)
							->get();  
		
			if(count($userLogin)>0)		
			{
				$tkn = md5(rand(1,999999));
				$update['userToken'] = $tkn;
				$user = User::where('id', '=', $userLogin[0]->id)->update($update);

				$userData['userid'] =  $userLogin[0]->id;
				$userData['userName'] =  $userLogin[0]->first_name;
				$userData['email'] =  $userLogin[0]->email;
				$userData['userToken'] =  $tkn;
			

				return response()->json(['status'=>1,'message'=>$msg1,'result'=>$userData]); 
            }
			
		}
		else
        {
			return response()->json(['status'=>0,'message'=>$msg2]); 
		}

	}

	public function edit_profile(Request $request) 
    { 
        $validator = Validator::make($request->all(), [
            'user_id' => 'required', 
            'user_token' => 'required',
            'first_name' => 'required',
            'last_name' => 'required', 
            'phone' => 'required', 
            'email' => 'required',
            //'password' => 'required',
            //'image' => 'required',
		]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
		}
		
		//-------------------Message------------------------------
		$msg1 = "Profile Updated Successfully";
		$msg2 = "Profile not Updated";
		$msg3 = "User already Registered with this email";
		$msg4 = "You are not authorized please login again";
		//-------------------Message------------------------------	

		$input = $request->all();
		$id = $input['user_id'];

		$user_email = User::query()
						->select('email')
						->where('email',$input['email'])
						->get();
						
		$verifiedUserId = Controller::checkToken($input['user_id'],$input['user_token']);
        if($verifiedUserId == $id)
		{
				$update['email'] = $input['email'];
				$update['first_name'] = $input['first_name'];
				$update['last_name'] = $input['last_name'];
				$update['phone'] = $input['phone'];
				
                if(isset($input['password']))
                {
                    $update['password'] = bcrypt($input['password']);

                }
				
				if(isset($input["image"])){
					$imagePath = $input["image"];
					$filename = rand(0000,9999).$imagePath->getClientOriginalName();
					$extension  = pathinfo($imagePath->getClientOriginalName(), PATHINFO_EXTENSION);
					$filename = rand(0000,9999)."userpic.".$extension;
					$upload_dir_path = public_path()."/uploads/userImages";
					$imagePath->move($upload_dir_path, $filename );
					$update['userImage'] = $filename;
				}

				$user = User::where('id', '=', $id)->update($update);

				if($user >= 0)
				{
					return response()->json(['status'=>1,'message'=>$msg1]); 
				}
				else
				{
					return response()->json(['status'=>0,'message'=>$msg2]); 
				}
		}
		else
		{
			return response()->json(['status'=>0,'message'=>$msg4]); 
		}
	}
	
	public function get_profile(Request $request) 
    { 
        $validator = Validator::make($request->all(), [
            'user_id' => 'required', 
            'user_token' => 'required',
        ]);
        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }

        $input = $request->all(); 
        $id = $input['user_id'];

        $msg1="User data found";
        $msg2="something is wrong. Please try again!";
        $msg3 = "You are not authorized please login again";

        $verifiedUserId = Controller::checkToken($input['user_id'],$input['user_token']);
        if($verifiedUserId == $id)
		{
			$getUserId = User::where('id',$id)->get();

	        if(count($getUserId) > 0)
	        {
                for ($g=0; $g < count($getUserId); $g++) 
                { 
                	$dataFile = "";
					$image = $getUserId[$g]->userImage;
                    if($image!="" && $image!=null) {
                        if(file_exists(public_path()."/uploads/userImages/".$image)) 
                        {
                            $dataFile = asset('/uploads/userImages/'.$image);
                        }
                    }

                   $userData = array(
                        'userid' => $getUserId[$g]->id,
                        'user_token' => $getUserId[$g]->userToken,
                        'image' => $dataFile,
                        'first_name' => $getUserId[$g]->first_name,
                        'last_name' => $getUserId[$g]->last_name,
                        'phone' => $getUserId[$g]->phone,
                        'email' => $getUserId[$g]->email,
                        'password' => $getUserId[$g]->password,
                   );
                }

	        	return response()->json(['status'=>1,'message'=>$msg1, 'result'=>$userData]);
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

    public function Logout(Request $req)
	{
		$validator = Validator::make($req->all(), [
            'email' => 'required',
        ]);
		if ($validator->fails()) {
            return response()->json($validator->errors());
        }
		$data = User::where('email', $req->email)->get();
        if($data->count())
		{
			$update['userToken']= '';
			User::where('email', $req->email)->update($update);
			
            return response()->json(['status'=>1,'message'=>'Logout successfully']);
            exit;
        }
        else
        {
            return response()->json(['status'=>0,'message'=>'Logout failed']);
            exit;
        }
	}
	public function forgotPassword(Request $request) 
    { 
        $validator = Validator::make($request->all(), [
            'email' => 'required',
        ]);
        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }
        $input = $request->all(); 
        $email = $input['email'];
        
        //-------------------Message------------------------------
        $msg1 = "Mail sent Successfully";
        $msg2 = "Mail not Found";
        $msg3 = "You are not authorized please login again";
        //-------------------Message------------------------------  
       
        $user = User::query()->where('email', '=', $email)->get();
        if(count($user) > 0)
        {
            $new_password = rand(100000,999999);

            $update['password'] = bcrypt($new_password);
            $user = User::where('email', '=', $email)->update($update);

            $to = $email;
            $from = "keshavtest8@gmail.com";
            $line1 = "You forgot your password ?";
            $line2 = "No problem, use this temporary Password : ".$new_password;
            $subject = "Forgot password";

            $headers = "From: $from";
            $headers = "From: " . $from . "\r\n";
            $headers .= "Reply-To: ". $from . "\r\n";
            $headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
            
            $body = "<!DOCTYPE html><html lang='en'><head><meta charset='UTF-8'><title>Express Mail</title></head><body>";
            $body .= "<table style='width: 100%;'>";
            $body .= "<thead style='text-align: center;'><tr><td style='border:none;' colspan='2'>";
            $body .= "</td></tr></thead><tbody>";
            $body .= "<tr><td style='border:none;' align = 'center'>{$line1}</td></tr>";
            $body .= "<tr><td></td></tr>";
            $body .= "<tr><td style='border:none;'> </td></tr>";
            $body .= "<tr><td></td></tr>";
            $body .= "<tr><td colspan='2' style='border:none;' align = 'center'>{$line2}</td></tr>";
            $body .= "</tbody></table>";
            $body .= "</body></html>";

            mail($to, $subject, $body, $headers);
            
            return response()->json(['status'=>1,'message'=>$msg1]); 
        }
        else
        {
            return response()->json(['status'=>0,'message'=>$msg2]); 
        }
    }

}
