<?php

namespace App\Http\Controllers;

use App\Mail\Testmail;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Doctor;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class docController extends Controller
{
    function test(){
        return "hello";
    }

    function sendMail($mailaddress){
        $mailbody = [
            'title' => 'Verify Email',
            'body' => 'Your verification code is ...'
        ];

        Mail::to($mailaddress)->send(new TestMail($mailbody));
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
        $doctor->password = Hash::make($req->password);
        $result = $doctor->save();

        $token = $doctor->createToken('docToken')->plainTextToken;

        $response = [
            'res' => "Registered",
            'info' => $doctor,
            'token' => $token
        ];

        if($result){
            return response($response, 200);
        }else{
            return ["res" => "Registration Failed"];
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
