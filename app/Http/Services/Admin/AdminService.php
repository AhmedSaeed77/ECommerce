<?php


namespace App\Http\Services\Admin;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\Admin;
use App\Models\AdminDetails;
use App\Models\ResetPassword;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;
use App\Mail\NotifyMail;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

class AdminService
{
    public function getAllAdmins()
    {
        $admins = Admin::get();
        return $admins;
    }
    public function getOneAdmin($id)
    {
        $admins = Admin::find($id);
        return $admins;
    }

    public function store(Request $request)
    {
        return $this->createOrUpdate( new Admin() ,$request);
    }

    public function update(Request $request,$id)
    {
        $admin = Admin::find($id);
        return $this->createOrUpdate( $admin ,$request);
    }

    public function createOrUpdate(Admin $admin,Request $request)
    { 
        try
        {
            $request->validate([
                                    'name' => 'required',
                                    'email' => 'required',
                                    'password' => 'required',
                                    'confirmpassword' => 'required',
                                ]);

            $admin->name = $request->name;
            $admin->email = $request->email;
            if($request->password == $request->confirmpassword)
            {
                $admin->password = Hash::make($request->password);
            }
            else 
            {
                return response()->json("Not Confirm Password");
            }
            
            $admin->country = $request->country;
            $admin->phone = $request->phone;
            $admin->gender = $request->gender;
            $admin->experience = $request->experience;
            $admin->description = $request->description;
            //$admin->vimeo_token = $request->vimeo_token;
            $admin->save();
            // $admindetails = new AdminDetails();
            // $admindetails->admin_id = $admin->id;
            // $admindetails->path_id = $request->path_id;
            // $admindetails ->course_id = $request->course_id;
            // $admindetails->part_id = $request->part_id;
            // $admindetails->save();
            foreach($request->details as $details)
            {
                $admindetails = new AdminDetails();
                $admindetails->admin_id = $admin->id;
                $admindetails->path_id = $details['path_id'];
                $admindetails ->course_id = $details['course_id'];
                $admindetails->part_id = $details['part_id'];
                $admindetails->save();
            }
            $token = $admin->createToken('myapptoken')->plainTextToken;
            $response = [
                            'admin' => $admin,
                            'message' => 'success',
                            'token' => $token
                        ];         
            $admin->sendEmailVerificationNotification();
            return response($response, 201);
        }
        catch (\Exception  $e)
        {
            return response()->json($e->getMessage(), 404);
        }
    }

    public function login(Request $request)
    {
        try
        {
            $request->validate([
                                    'email'   => 'required',
                                    'password' => 'required'
                                ]);

            $admin = Admin::where('email', $request->email)->first();
            if (auth('admin')->attempt(['email' => $request->email, 'password' => $request->password], false))
            {
                $token =  $admin->createToken($request->email)->plainTextToken;
                return response()->json([   'Access-token' =>'bearer  '. $token,
                                            'name' => $admin->name,
                                            'email' => $admin->email,
                                            'admin_id' => $admin->id,
                                        ]);
            }
            else
            {
                return response()->json("Email Or Password Invalid");
            }
        }
        catch (\Exception  $e)
        {
            return response()->json($e->getMessage(), 200);
        }
    }

    public function reset(Request $request)
    {

        try
        {
            $request->validate([
                                    'email' => 'required',
                                ]);

            $admin = Admin::where('email',$request->email)->first();
            if($admin)
            {
                
                $randomNumber = random_int(1000, 9999);
                $details = [
                                'title' => 'Reset',
                                'body' =>  $randomNumber,
                            ];

                Mail::to($request->email)->send(new NotifyMail($details));
                $reset1 = ResetPassword::where('user_id',$admin->id)->get();
                foreach($reset1 as $a)
                {
                    $a->delete();
                }
                $reset = new ResetPassword();
                $reset->user_id = $admin->id;
                $reset->reset = $randomNumber;
                $reset->save();
                return response()->json("Email Send");
            }
            else
            {
                return response()->json('Email Not Found');
            }
        }
        catch (\Exception $e)
        {
            return response()->json($e->getMessage(), 200);
        }
    }

    public function resetUserconfirm(Request $request)
    {
        try
        {
            $request->validate([
                                    'confirm' => 'required',
                                ]);

            $reset = ResetPassword::where('reset',$request->confirm)->first();
            if($reset)
            {
                return response()->json('Email Is Confirm');
            }
            else
            {
                return response()->json('Email Not Confirm');
            }
        }
        catch (\Exception  $e)
        {
            return response()->json($e->getMessage(), 200);
        }
    }

    public function delete($id)
    {
        $admin = Admin::find($id);
        $admin->delete();
    }

    public function changePasswordAdmin(Request $request,$id)
    {
        try
        {
            $request->validate([
                                    'oldpassword'   => 'required',
                                    'newpassword' => 'required',
                                    'confirmpassword' => 'required',
                                ]);

            $admin = Admin::find($id);
            if (Hash::check($request->oldpassword,$admin->password))
            {
                if($request->newpassword == $request->confirmpassword)
                {
                    $admin->password = Hash::make($request->newpassword);
                    $admin->save();
                    return response()->json([ $admin ]);
                }   
                else
                {
                    return response()->json([ 'Not Confirm' ]);
                } 
            }
            else
            {
                return response()->json('Dose not old password');
            }
        }
        catch (\Exception  $e)
        {
            return response()->json($e->getMessage(), 200);
        }
        
    }

    public function changeafterconfirm(Request $request,$email)
    {
        try
        {
            $request->validate([
                                    'newpassword' => 'required',
                                    'confirmpassword' => 'required',
                                ]);

            $admin = Admin::where('email',$email)->first();
            if($request->newpassword == $request->confirmpassword)
            {
                $admin->password = Hash::make($request->newpassword);
                $admin->save();
                return response()->json([ $admin ]);
            }   
            else
            {
                return response()->json([ 'Not Confirm' ]);
            } 
        }
        catch (\Exception  $e)
        {
            return response()->json($e->getMessage(), 200);
        }
    }

    // public function logout(Request $request)
    // {
    //     $request->user('admin')->token()->revoke();
    //     return response()->json(['message' => 'Successfully logged out']);
    // }

    public function logout(Request $request ) 
    {
        $request->session()->flush();
        Auth::logout();
        return response()->json(['message' => 'Successfully logged out']);
        
    }
    

// -----------------------------------------------------------------------------------------------------
    // for super admin add,update,delete,search teachers

    public function storeteacher(Request $request)
    {
        $request->validate([
            'name'=>'required',
            'email'=>'required',
            'password'=>'required',
            'country'=>'required',
            'phone'=>'required',
            'gender'=>'required',
            'teachinglang'=>'required',
            'description'=>'required',
            'vimeo_token'=>'required'

          ]);
        $admin =Admin::create($request->all());
        return $admin;
    }

    public function deleteteacher(Request $request,$id)
    {
         $admin =Admin::find($id);
         if (is_null($admin)) {
            return response()->json(['message' => 'admin not found'], 404);
        }
         $admin->delete();
        return response()->json(null,204);
        // return $request;
    }

    public function updateteacher(Request $request,$id)
    {
         $admin =Admin::find($id);
         if (is_null($admin)) {
            return response()->json(['message' => 'admin not found'], 404);
        }
         $admin->update($request->all());
        return response($admin,200);

    }

    public function searchteacher($name)
    {
         $admin = Admin::where('name','like','%'.$name.'%')->get();

        return $admin;
        // return $request;
    }

   
}
