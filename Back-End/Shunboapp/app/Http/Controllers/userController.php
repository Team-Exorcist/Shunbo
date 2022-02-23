<?php

namespace App\Http\Controllers;
use App\Models\User;

use App\Mail\Testmail;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Verificationcode;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class userController extends Controller{

    function test(){
        return " user hello";
    }

    function sendMail($mailaddress, $code){

        $mailbody = [
            'title' => 'Verify Email',
            'body' => 'Your verification code is '.$code
        ];

        Mail::to($mailaddress)->send(new TestMail($mailbody));
    }


    public function verifyCode($mailaddress){

        $code = mt_rand(100000,999999);

        $verificationcode = new Verificationcode();
        $verificationcode->email = $mailaddress;
        $verificationcode->code = $code;
        $verificationcode->save();

        userController::sendMail($mailaddress, $code);
    }

    //after giving the mail address send an email with code
    function forgotPassword(Request $req){
        $email = $req->email;
        //send code to this email
        verifyCode($email);
    }


    function matchCode(Request $req){
        $verificationcode = Verificationcode::where('email', $req->email)->first();
        if(!$verificationcode){
            return response([
                "res" => 'wrong email'
            ], 401);
            if($verificationcode->code != $req->code){
                return response([
                    "res" => 'wrong code'
                ], 401);
            }
        }
        if($result){
            $user = User::where('email', $verificationcode->email)->first();
            $token = $user->createToken('docToken')->plainTextToken;
            Verificationcode::where('email', $req->email)->delete();
            return ['updatePassToken' => $token, 'res' => 1];
        }else{
            return ['res' => 0];
        }
    }

    function verifyMail(Request $req){
        
        $verificationcode = Verificationcode::where('email', $req->email)->first();
        if(!$verificationcode){
            return response([
                "res" => 'wrong email'
            ], 401);
            if($verificationcode->code != $req->code){
                return response([
                    "res" => 'wrong code'
                ], 401);
            }
        }

        $result = User::where('email', $verificationcode->email)->update(['isverified' => 1]);

        if($result){
            Verificationcode::where('email', $req->email)->delete();
            return ['res' => 1];
        }else{
            return ['res' => 0];
        }

    }

    function register(Request $req){
        $req->validate([
            'name' => 'required|string|min:6',
            'email' => 'required| string| unique:users,email',
            'mobile' => 'required| string| min:11| max:11| unique:users,mobile',
            'password' => 'required| string| min:6'
        ]);

        $user = new User();
        $user->name = $req->name;
        $user->email = $req->email;
        $user->mobile = $req->mobile;
        $user->isverified = 0;
        $user->password = Hash::make($req->password);
        $result = $user->save();

        $email = $req->email;
        userController::verifyCode($email);

        //$token = $user->createToken('userToken')->plainTextToken;

        $response = [
            'res' => 1,
            'info' => $user
            //'token' => $token
        ];

        if($result){
            return response($response, 200);
        }else{
            return ["res" => 0];
        }
    }

    function login(Request $req){
        $user = User::where('email', $req->email)->first();

        if(!$user || !Hash::check($req->password, $user->password)){
            return response([
                "res" => 'login failed'
            ], 401);
        }

        $token = $user->createToken('docToken')->plainTextToken;

        $response = [
            'res' => 'logged in',
            'user' => $user,
            'token' => $token
        ];
        return response($response, 200);
    }

    function logout(Request $req){
        auth()->user()->tokens()->delete();

        return [
            "res" => "logged out"
        ];
    }

}
