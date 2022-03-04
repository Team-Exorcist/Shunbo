<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\Doctor;
use App\Models\Post;
use App\Models\Verificationcode;
use App\Models\Comment;
use App\Models\Appointment;

use App\Mail\Testmail;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;

class userController extends Controller{

    function test(){
        return " user hello";
    }

    function getUser($uid){
        $user = User::find($uid);
        return $user;
    }

    function getAppointments($uid){
        $app = DB::table('appointments')->where('uid', $uid)->orderByDesc('created_at')->get();
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
        $posts =  userController::getPosts();
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
            $comments = userController::getComments($pid);

            $mainpost = array("pid" => $pid, "puid" => $puid, "pdid" => $pdid, "pusername" => $pusername,
                                "pmsg" => $pmsg, "pvotes" => $pvotes, "pisdoctor" => $pisdoctor, "pupdated_at" =>$pupdated_at,
                                "pcreated_at" => $pcreated_at, "comments" => $comments);

            array_push($listofposts, $mainpost);
        }
        return $listofposts;
    }


////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////


    function getDoctorList(){
        $doctors = DB::table('doctors')->get();
        if($doctors){
            return $doctors;
        }else{
            return ['res' => 0];
        }
    }

    function isDocFree($reqtime, $docID, $date){
        $times = Appointment::where('did', $docID)->where('date', $date)->pluck('time');

        foreach($times as $time){
            if($time == $reqtime){
                return FALSE;
            }
        }
        return TRUE;

        //could be used in another function
        // $freetime = [];
        // for($i = 1; $i <= 8; $i++ ){
        //     if(userController::isDocFree($i,  $req->did, $req->date)){
        //         $freetime['time'.$i] = $i;
        //     }
        // }
    }

    function makeAppointment(Request $req){

        if($req->time < 1 || $req->time > 8){
            return response(['res' => 0] , 300);
        }

        $doctor = Doctor::find($req->did);
        $dname = $doctor->name;
        $times = Appointment::where('did', $req->did)->where('date', $req->date)->pluck('time');

        foreach($times as $time){
            if($req->time == $time){
                return response(['res'=> 0],300);
            }
        }

        $appointment = new Appointment();

        $appointment->did = $req->did;
        $appointment->uid = $req->uid;
        $appointment->dname = $dname;
        $appointment->uname = $req->uname;
        $appointment->ugender = $req->ugender;
        $appointment->time = $req->time;
        $appointment->date = $req->date;

        $result = $appointment->save();

        if($result){
            return response(['res' => 1],200);
        }else{
            return response(['res' => 0],200);
        }

    }

    ////////////////////////////////////////////////////////////////////////////

    function createPost(Request $req){
        $req->validate([
            'msg' => 'required | max: 4500'
        ]);

        $user = User::find($req->uid);
        $username = $user->name;

        $post = new Post();
        $post->uid = $req->uid;
        $post->username = $username;
        $post->msg = $req->msg;
        $post->isdoctor = 0;
        $result = $post->save();

        if($result){
            return response(["res" => 1], 200);
        }else{
            response(["res" => 0], 401);
        }
    }

    function makeComment(Request $req){
        
        $req->validate([
            'msg' => 'required | max: 500'
        ]);
        $user = User::find($req->uid);
        $username = $user->name;

        $comment = new Comment();
        $comment->pid = $req->pid;
        $comment->uid = $req->uid;
        $comment->username = $username;
        $comment->msg = $req->msg;
        $comment->isdoctor = 0;

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

        userController::sendMail($mailaddress, $code);
    }

    //after giving the mail address send an email with code
    function resetPassword(Request $req){
        $email = $req->email;
        $verificationcode = User::where('email', $req->email)->first();
        if(!$verificationcode){
            return response([
                "res" => '402'
            ], 401);
        }else{
                    //send code to this email
            userController::verifyCode($email);
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

        }
        if($verificationCode->code != $req->code){
            return response([
                "res" => '404'
            ], 404);
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
                "res" => '402'
            ], 401);
        }
        if($verificationcode->code != $req->code){
            return response([
                "res" => '404'
            ], 401);
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
            'name' => array('required', 'string', 'min:4', 'regex:/^([A-Za-z]){4,}/'),
            'email' => 'required| string| email|unique:users,email',
            'mobile' => 'required| string| max:11| unique:users,mobile| regex:/^(01)([3-9]){1}([0-9]){8}/',
            'password' => array('required', 'string', 'regex:/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/')
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
        $req->user()->currentAccessToken()->delete();
        return [
            "res" => "1"
        ];
    }

}
