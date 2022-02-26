<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\Post;
use App\Models\Verificationcode;
use App\Models\Comment;

use App\Mail\Testmail;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class userController extends Controller{

    function test(){
        return " user hello";
    }

    function createPost(Request $req){
        $post = new Post();
        $post->uid = $req->uid;
        $post->msg = $req->msg;
        $result = $post->save();

        if($result){
            return response(["res" => 1], 200);
        }else{
            response(["res" => 0], 401);
        }
    }

    function makeComment(Request $req){
        $comment = new Comment();
        $comment->pid = $req->pid;
        $comment->uid = $req->uid;
        $comment->msg = $req->msg;

        $result = $comment->save();

        if($result){
            return response(["res" => 1], 200);
        }else{
            response(["res" => 0], 401);
        }
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
    function resetPassword(Request $req){
        $email = $req->email;
        //send code to this email
        userController::verifyCode($email);
        return response(["res" => 1], 200);
    }


    // function matchCode(Request $req){
    //     $verificationcode = Verificationcode::where('email', $req->email)->first();
    //     if(!$verificationcode){
    //         return response([
    //             "res" => 'wrong email'
    //         ], 401);
    //         if($verificationcode->code != $req->code){
    //             return response([
    //                 "res" => 'wrong code'
    //             ], 401);
    //         }
    //     }
    //     if($result){
    //         $user = User::where('email', $verificationcode->email)->first();
    //         $token = $user->createToken('docToken')->plainTextToken;
    //         Verificationcode::where('email', $req->email)->delete();
    //         return ['updatePassToken' => $token, 'res' => 1];
    //     }else{
    //         return ['res' => 0];
    //     }
    // }

    function changePassword(Request $req){
        $req->validate([
            'email' => 'required| string',
            'password' => 'required| string| min:6'
        ]);
        $email = $req->email;
        $code = $req->code;
        $newPassword = $req->password;

        $password = Hash::make($req->password);
  
        $verificationCode = Verificationcode::where('email', $req->email)->first();
        if(!$verificationCode){
            return response([
                "res" => 'wrong email'
            ], 401);
            if($verificationCode->code != $req->code){
                return response([
                    "res" => 'wrong code'
                ], 401);
            }
        }

        $result = User::where('email', $req->email)->update(['password' => $password]);
        if($result){
            Verificationcode::where('email', $req->email)->delete();
            return response([
                "res" => 1
            ],200);
        }else{
            return response([
                "res" => 0
            ],401);
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
            return response(["res" => 0],401);
        }
    }

    function login(Request $req){
        $user = User::where('email', $req->email)->first();

        if(!$user || !Hash::check($req->password, $user->password)){
            return response([
                "res" => 'login failed'
            ], 401);
        }

        $token = $user->createToken('userToken', ['user'])->plainTextToken;

        $response = [
            'res' => '1',
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
