<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Doctor;
use Illuminate\Support\Facades\Hash;

class docController extends Controller
{
    function test(){
        return "hello";
    }

    public function register(Request $req){
        $req->validate([
            'name' => 'required|string|min:6',
            'email' => 'required| string| unique:doctors,email',
            'mobile' => 'required| string| min:11| max:11',
            'password' => 'required| string| min:6'
        ]);

        $doctor = new Doctor();
        $doctor->name = $req->name;
        $doctor->email = $req->email;
        $doctor->mobile = $req->mobile;
        $doctor->password = Hash::make($req->password);
        $doctor->save();

}
}
