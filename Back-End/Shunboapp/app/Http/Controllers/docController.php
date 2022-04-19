<?php

namespace App\Http\Controllers;
use App\Models\Doctor;
use App\Models\Post;
use App\Models\Verificationcode;
use App\Models\Comment;

use App\Mail\Testmail;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;

class docController extends Controller
{
    function test(){
        return "hello";
    }

    function getDoctor($did){
        $doctor = Doctor::find($did);
        return $doctor;
    }

    function updateBill(Request $req){
        $did = $req->did;
        $bill = $req->bill;

        $result = DB::table('doctors')->where('id', $did)->update(["price" => $bill]);
        if($result){
            return response(['res' => 1], 200);
        }else{
            return response(['res'=> 0], 200);
        }

    }


    function addMeetLink(Request $req){
        $aid = $req->aid;
        $link = $req->link;
        $app = DB::table('appointments')->where('id', $aid)->update(["meetlink" => $link]);
        if($app){
            return ['res' => 1];
        }
    }

    function getAppointments($did){
        $app = DB::table('appointments')->where('did', $did)->orderByDesc('created_at')->get();
        if($app){
            return $app;
        }else{
            return ['res' => 0];
        }
    }

////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////

    function getPosts(){
        $posts = DB::table('posts')->orderByDesc('created_at')->get();
        if($posts){
            return $posts;
        }else{
            return ['res' => 0];
        }  
    }

    function getComments($pid){
        $comments = DB::table('comments')->where('pid', $pid)->orderByDesc('created_at')->get();
        if($comments){
            return $comments;
        }else{
            return ['res' => 0];
        }  
    }

    function getFullPost(){
        $listofposts = [];
        $posts =  docController::getPosts();
        foreach($posts as $post){
            $pid = $post->id;
            $puid = $post->uid;
            $pdid = $post->did;
            $pusername = $post->username;
            $pmsg = $post->msg;
            $pvotes = $post->votes;
            $pisdoctor = $post->isdoctor;
            $pupdated_at = $post->updated_at;
            $pcreated_at = $post->created_at;
            $comments = docController::getComments($pid);

            $mainpost = array("pid" => $pid, "puid" => $puid, "pdid" => $pdid, "pusername" => $pusername,
                                "pmsg" => $pmsg, "pvotes" => $pvotes, "pisdoctor" => $pisdoctor, "pupdated_at" =>$pupdated_at,
                                "pcreated_at" => $pcreated_at, "comments" => $comments);

            array_push($listofposts, $mainpost);
        }
        return $listofposts;
    }


////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////

function createPost(Request $req){
    $req->validate([
        'msg' => 'required | max: 4500'
    ]);

    $user = Doctor::find($req->did);
    $username = $user->name;

    $post = new Post();
    $post->did = $req->did;
    $post->username = $username;
    $post->msg = $req->msg;
    $post->isdoctor = 1;
    $result = $post->save();

    if($result){
        return response(["res" => 1], 200);
    }else{
        response(["res" => 0], 201);
    }
}

function makeComment(Request $req){
    
    $req->validate([
        'msg' => 'required | max: 500'
    ]);

    $user = Doctor::find($req->did);
    $username = $user->name;

    $comment = new Comment();
    $comment->pid = $req->pid;
    $comment->did = $req->did;
    $comment->username = $username;
    $comment->msg = $req->msg;
    $comment->isdoctor = 1;

    $result = $comment->save();

    if($result){
        return response(["res" => 1], 200);
    }else{
        response(["res" => 0], 200);
    }
}

function vote($pid){
    $post = Post::find($pid);
    $vote = $post->votes + 1;

    $result = Post::where('id', $pid)->update(['votes' => $vote]);

    if($result){
        return response(["res" => 1], 200);
    }else{
        response(["res" => 0], 401);
    }
}

///////////////////////////////////////////////////////////////////////




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
            'password' => 'required| string| min:8'
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

        }
        if($verificationCode->code != $req->code){
            return response([
                "res" => '404'
            ], 401);
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
        $req->user()->currentAccessToken()->delete();
        return [
            "res" => "1"
        ];
    }
}
