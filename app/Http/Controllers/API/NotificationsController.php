<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Models\Notifications;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Controllers\API\Functions\AbstractedFunctions;

class NotificationsController extends Controller
{
    public function index()
    {
        $notifications = Notifications::all();
        return response()->json([
            'status' => 200,
            'message' => 'Successful',
            'notifications' => $notifications,
        ]);
    }

    public function store(Request $request)
    {
        $notifications = new Notifications;
        $notifications->title = $request->input('title');
        $notifications->message = $request->input('message');
        $notifications->user_id = $request->input('user_id');
        $notifications->active = $request->input('active');
        $notifications->save();

        return response()->json([
            'status' => 200,
            'message' => 'Successful'
        ]);
    }

    public function edit($id)
    {
        $notifications = Notifications::find($id);
        return response()->json([
            'status' => 200,
            'message' => 'Successful',
            'notifications' => $notifications,
        ]);
    }

    public function update(Request $request, $id)
    {
        $notifications = Notifications::find($id);
        $notifications->title = $request->input('title');
        $notifications->message = $request->input('message');
        $notifications->user_id = $request->input('user_id');
        $notifications->active = $request->input('active');
        $notifications->update();

        return response()->json([
            'status' => 200,
            'message' => 'Successful'
        ]);
    }

    public function get_notification_count(Request $request)
    {
        return response()->json([
            'status' => 200,
            'error' => false,
            'unread' => '1',
            'message' => 'Get notifications successful.',
        ]);
    }

    public function get_notifications($id)
    {
        $abstracted_functions = new AbstractedFunctions();

        $phone_number = $abstracted_functions->standardize_phonenumber($id);
        $get_user_id = DB::select('SELECT id from userdata WHERE phone_number  = ?', [$phone_number]);
        $user_id = $get_user_id[0]->id;

        $get_statements = DB::select('SELECT * from notifications WHERE user_id = ?', [$user_id]);

        if ($get_statements and sizeof($get_statements) > 0) {
            $encoded = json_encode($get_statements, true);
            $decoded = json_decode($encoded, true);
            $d = array();
            foreach ($decoded as $obj) {
                // $amount_transacted = $obj['transaction_dr'] + $obj['transaction_cr'];
                $d[] = array(
                    'id' => $obj['id'],
                    'user_id' => $obj['user_id'],
                    'ndate' => strtotime($obj['created_at']),
                    'message' => $obj['message'],
                    'title' => $obj['title'],
                    'active' => $obj['active'],
                );
            }
            $json = json_encode($d);
            $json2 = json_decode($json);

            return response()->json(
                // 'status' => 200,
                $json2
                // 'error' => false,
                // 'error_msg' => 'Successful',
                // 'balance' => $this->get_user_balance($request->input('phoneNumber')),
                // 'transaction_msg' => "Your Balance is: KES." . $this->get_user_balance($request->input('phoneNumber')),
            );
        } else {
            return response()->json();
        }
    }
}
