<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Userdata;
use Illuminate\Http\Request;

class UserdataController extends Controller
{
    public function index(){
        $userdata = Userdata::all();
        return response()->json([
            'status'=>200,
            'message'=>'Successful',
            'accountpreferences'=>$userdata,
        ]);
    }
    
    public function store(Request $request){
        $userdata = new Userdata();
        $userdata->title = $request->input('title');
        $userdata->first_name = $request->input('first_name');
        $userdata->middle_name = $request->input('middle_name');
        $userdata->last_name = $request->input('last_name');
        $userdata->email_address = $request->input('email_address');
        $userdata->phone_number = $request->input('phone_number');
        $userdata->user_role = $request->input('user_role');
        $userdata->id_number = $request->input('id_number');
        $userdata->date_of_birth = $request->input('date_of_birth');
        $userdata->gender = $request->input('gender');
        $userdata->password = $request->input('password');
        $userdata->app_version = $request->input('app_version');
        $userdata->referral_code = $request->input('referral_code');
        $userdata->save();

        return response()->json([
            'status'=>200,
            'message'=>'Successful'
        ]);
    }

    public function edit($id){
        $userdata = Userdata::find($id);
        return response()->json([
            'status'=>200,
            'message'=>'Successful',
            'accountpreferences'=>$userdata,
        ]);
    }

    public function update(Request $request, $id){
        $userdata = Userdata::find($id);
        $userdata->title = $request->input('title');
        $userdata->first_name = $request->input('first_name');
        $userdata->middle_name = $request->input('middle_name');
        $userdata->last_name = $request->input('last_name');
        $userdata->email_address = $request->input('email_address');
        $userdata->phone_number = $request->input('phone_number');
        $userdata->user_role = $request->input('user_role');
        $userdata->id_number = $request->input('id_number');
        $userdata->date_of_birth = $request->input('date_of_birth');
        $userdata->gender = $request->input('gender');
        $userdata->password = $request->input('password');
        $userdata->app_version = $request->input('app_version');
        $userdata->referral_code = $request->input('referral_code');
        $userdata->update();

        return response()->json([
            'status'=>200,
            'message'=>'Successful'
        ]);
    }
}
