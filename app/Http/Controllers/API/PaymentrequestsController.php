<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Paymentrequests;
use Illuminate\Http\Request;

class PaymentrequestsController extends Controller
{
    public function index(){
        $paymentrequests = Paymentrequests::all();
        return response()->json([
            'status'=>200,
            'message'=>'Successful',
            'paymentrequests'=>$paymentrequests,
        ]);
    }
 
    public function store(Request $request){
        $paymentrequests = new Paymentrequests;
        $paymentrequests->requestor_id = $request->input('requestor_id');
        $paymentrequests->benefactor_id = $request->input('benefactor_id');
        $paymentrequests->amount = $request->input('amount');
        $paymentrequests->request_title = $request->input('request_title');
        $paymentrequests->request_description = $request->input('request_description');
        $paymentrequests->save();

        return response()->json([
            'status'=>200,
            'message'=>'Successful'
        ]);
    }

    public function edit($id){
        $paymentrequests = Paymentrequests::find($id);
        return response()->json([
            'status'=>200,
            'message'=>'Successful',
            'paymentrequests'=>$paymentrequests,
        ]);
    }

    public function update(Request $request, $id){
        $paymentrequests = Paymentrequests::find($id);
        $paymentrequests->requestor_id = $request->input('requestor_id');
        $paymentrequests->benefactor_id = $request->input('benefactor_id');
        $paymentrequests->amount = $request->input('amount');
        $paymentrequests->request_title = $request->input('request_title');
        $paymentrequests->request_description = $request->input('request_description');
        $paymentrequests->update();

        return response()->json([
            'status'=>200,
            'message'=>'Successful'
        ]);
    }
}
