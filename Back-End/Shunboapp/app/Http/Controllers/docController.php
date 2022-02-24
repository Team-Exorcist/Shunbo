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
        //send code to this email
        docController::verifyCode($email);
        return response(["res"=> 1],200);
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
            $doctor = Doctor::where('email', $verificationcode->email)->first();
            $token = $doctor->createToken('docToken')->plainTextToken;
            Verificationcode::where('email', $req->email)->delete();
            return ['updatePassToken' => $token, 'res' => 1];
        }else{
            return ['res' => 0];
        }
    }

    function updatePassword(Request $req){
        //
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

        $result = Doctor::where('email', $verificationcode->email)->update(['isverified' => 1]);

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
            'email' => 'required| string| unique:doctors,email',
            'mobile' => 'required| string| min:11| max:11| unique:doctors,mobile',
            'password' => 'required| string| min:6'
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
            return ["res" => 0];
        }
    }

    function login(Request $req){
        $doctor = Doctor::where('email', $req->email)->first();

        if(!$doctor || !Hash::check($req->password, $doctor->password)){
            return response([
                "res" => 'login failed'
            ], 401);
        }

        $token = $doctor->createToken('docToken')->plainTextToken;

        $response = [
            'res' => 'logged in',
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
