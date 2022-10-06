<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Notifications;
use Illuminate\Http\Request;

class NotificationsController extends Controller
{
    public function index(){
        $notifications = Notifications::all();
        return response()->json([
            'status'=>200,
            'message'=>'Successful',
            'notifications'=>$notifications,
        ]);
    }

    public function store(Request $request){
        $notifications = new Notifications;
        $notifications->title = $request->input('title');
        $notifications->message = $request->input('message');
        $notifications->user_id = $request->input('user_id');
        $notifications->active = $request->input('active');
        $notifications->save();

        return response()->json([
            'status'=>200,
            'message'=>'Successful'
        ]);
    }

    public function edit($id){
        $notifications = Notifications::find($id);
        return response()->json([
            'status'=>200,
            'message'=>'Successful',
            'notifications'=>$notifications,
        ]);
    }

    public function update(Request $request, $id){
        $notifications = Notifications::find($id);
        $notifications->title = $request->input('title');
        $notifications->message = $request->input('message');
        $notifications->user_id = $request->input('user_id');
        $notifications->active = $request->input('active');
        $notifications->update();

        return response()->json([
            'status'=>200,
            'message'=>'Successful'
        ]);
    }
}
