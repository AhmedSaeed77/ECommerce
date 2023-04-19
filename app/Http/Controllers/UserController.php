<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Services\User\UserService;

class UserController extends Controller
{
    protected $userservice;
    public function __construct(UserService $userservice)
    {
        $this->userservice = $userservice;
    }

    public function register(Request $request)
    {
        $user = $this->userservice ->register($request);
        $newuser [] = $user;
        return returnData($newuser, 'done',200);
    }

    public function login(Request $request)
    {
        $user = $this->userservice ->login($request);
        $newuser [] = $user;
        return returnData($newuser, 'done',200);
    }

    public function resetpassword(Request $request)
    {
        $user = $this->userservice ->reset($request);
        $newuser [] = $user;
        return returnData($newuser, 'done',200);
    }

    public function confirmemail(Request $request)
    {
        $user = $this->userservice ->resetUserconfirm($request);
        $newuser [] = $user;
        return returnData($newuser, 'done',200);
    }

    public function changepassword(Request $request,$id)
    {
        $user = $this->userservice ->changeafterconfirm($request,$id);
        $newuser [] = $user;
        return returnData($newuser, 'done',200);
    }
}
