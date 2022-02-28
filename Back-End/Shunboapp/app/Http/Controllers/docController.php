<?php

namespace App\Http\Controllers;
use App\Models\Doctor;

use App\Mail\Testmail;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Verificationcode;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class docController extends Controller
{
    function test(){
        return "hello";
    }


    function createPost(Request $req){

        $post = new Post();
        $post->did = $req->did;
        $post->username = $req->username;
        $post->msg = $req->msg;
        $post->isdoctor = 1;
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
        $comment->did = $req->did;
        $comment->username = $req->username;
        $comment->msg = $req->msg;
        $comment->isdoctor = 1;

        $result = $comment->save();

        if($result){
            return response(["res" => 1], 200);
        }else{
            response(["res" => 0], 401);
        }

    }

    function vote(Request $req){

        $post = Post::find($req->id);
        $vote = $post->votes + 1;

        $result = Post::where('id', $req->id)->update(['votes' => $vote]);

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

        docController::sendMail($mailaddress, $code);
    }

    //after giving the mail address send an email with code
    function resetPassword(Request $req){
        $email = $req->email;
        $verificationcode = Doctor::where('email', $req->email)->first();
        if(!$verificationcode){
            return response([
                "res" => '402'
            ], 401);
        }else{
                    //send code to this email
            docController::verifyCode($email);
            return response(["res" => 1], 200);
        }
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
    //         $doctor = Doctor::where('email', $verificationcode->email)->first();
    //         $token = $doctor->createToken('docToken')->plainTextToken;
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

        $result = Doctor::where('email', $req->email)->update(['password' => $password]);
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
        
        $codes = $req->code;
        $verificationcode = Verificationcode::where('email', $req->email)->first();

        if(!$verificationcode){
            return response([
                "res" => 'wrong email'
            ], 200);
        }
        if($verificationcode->code == $req->code){
            $result = Doctor::where('email', $verificationcode->email)->update(['isverified' => 1]);

            if($result){
                Verificationcode::where('email', $req->email)->delete();
                return ['res' => 1];
            }else{
                return ['res' => 0];
            }
        }else{
            return response([
                "res" => '404',
            ], 200);
        }






    }

    function register(Request $req){
        $req->validate([
            'name' => array('required', 'string', 'min:4', 'regex:/^([A-Za-z]){4,}/'),
            'email' => 'required| string| email|unique:doctors,email',
            'mobile' => 'required| string| max:11| unique:doctors,mobile| regex:/^(01)([3-9]){1}([0-9]){8}/',
            'password' => array('required', 'string', 'max:10', 'regex:/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/')
        ]);

        $doctor = new Doctor();
        $doctor->name = $req->name;
        $doctor->email = $req->email;
        $doctor->mobile = $req->mobile;
        $doctor->isverified = 0;
        $doctor->password = Hash::make($req->password);
        $result = $doctor->save();

        $email = $req->email;
        docController::verifyCode($email);

        //$token = $doctor->createToken('docToken')->plainTextToken;

        $response = [
            'res' => 1,
            'info' => $doctor
            //'token' => $token
        ];

        if($result){
            return response($response, 200);
        }else{
            return response(["res" => 0],422);
        }
    }

    function login(Request $req){
        $doctor = Doctor::where('email', $req->email)->first();

        if(!$doctor || !Hash::check($req->password, $doctor->password)){
            return response([
                "res" => 'login failed'
            ], 401);
        }

        $token = $doctor->createToken('docToken',['doctor'])->plainTextToken;

        $response = [
            'res' => '1',
            'user' => $doctor,
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
