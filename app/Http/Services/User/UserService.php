<?php


namespace App\Http\Services\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\AdminDetails;
use App\Models\ResetPassword;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;
use App\Mail\NotifyMail;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

class UserService
{
    public function getAllUsers()
    {
        $users = User::get();
        return $users;
    }

    public function register(Request $request)
    {
        try
        {
            $request->validate([
                                    'name' => 'required',
                                    'email' => 'required',
                                    'password' => 'required',
                                    'confirmpassword' => 'required',
                                ]);
            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            if($request->password == $request->confirmpassword)
            {
                $user->password = Hash::make($request->password);
            }
            else 
            {
                return response()->json("Not Confirm Password");
            }
            $user->save();
            $token = $user->createToken('myapptoken')->plainTextToken;
            $response = [
                            'user' => $user,
                            'message' => 'success',
                            'token' => $token
                        ];         
            return response($response, 201);
        }
        catch (\Exception  $e)
        {
            return response()->json($e->getMessage(), 404);
        };
    }

    public function login(Request $request)
    {
        try
        {
            $request->validate([
                                    'email'   => 'required',
                                    'password' => 'required'
                                ]);

            $user = User::where('email', $request->email)->first();
            if (Auth::attempt(['email' => $request->email, 'password' => $request->password], false))
            {
                $token =  $user->createToken($request->email)->plainTextToken;
                return response()->json([   'Access-token' =>'bearer  '. $token,
                                            'name' => $user->name,
                                            'email' => $user->email,
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

            $user = User::where('email',$request->email)->first();
            if($user)
            {
                
                $randomNumber = random_int(1000, 9999);
                $details = [
                                'title' => 'Reset',
                                'body' =>  $randomNumber,
                            ];

                Mail::to($request->email)->send(new NotifyMail($details));
                $reset1 = ResetPassword::where('email',$user->email)->get();
                foreach($reset1 as $a)
                {
                    $a->delete();
                }
                $reset = new ResetPassword();
                $reset->email = $user->email;
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


    public function changeafterconfirm(Request $request,$id)
    {
        try
        {
            $request->validate([
                                    'newpassword' => 'required',
                                    'confirmpassword' => 'required',
                                ]);

            $user = User::find($id);
        
            if($request->newpassword == $request->confirmpassword)
            {
                $user->password = Hash::make($request->newpassword);
                $user->save();
                return response()->json([ $user ]);
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
}
