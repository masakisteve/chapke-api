<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Paymentrequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\API\Functions\AbstractedFunctions;

class PaymentrequestsController extends Controller
{
    public function index()
    {
        $paymentrequests = Paymentrequests::all();
        return response()->json([
            'status' => 200,
            'message' => 'Successful',
            'paymentrequests' => $paymentrequests,
        ]);
    }

    public function store(Request $request)
    {
        $paymentrequests = new Paymentrequests;
        $paymentrequests->requestor_id = $request->input('requestor_id');
        $paymentrequests->benefactor_id = $request->input('benefactor_id');
        $paymentrequests->amount = $request->input('amount');
        $paymentrequests->request_title = $request->input('request_title');
        $paymentrequests->request_description = $request->input('request_description');
        $paymentrequests->save();

        return response()->json([
            'status' => 200,
            'message' => 'Successful'
        ]);
    }

    public function edit($id)
    {
        $paymentrequests = Paymentrequests::find($id);
        return response()->json([
            'status' => 200,
            'message' => 'Successful',
            'paymentrequests' => $paymentrequests,
        ]);
    }

    public function update(Request $request, $id)
    {
        $paymentrequests = Paymentrequests::find($id);
        $paymentrequests->requestor_id = $request->input('requestor_id');
        $paymentrequests->benefactor_id = $request->input('benefactor_id');
        $paymentrequests->amount = $request->input('amount');
        $paymentrequests->request_title = $request->input('request_title');
        $paymentrequests->request_description = $request->input('request_description');
        $paymentrequests->update();

        return response()->json([
            'status' => 200,
            'message' => 'Successful'
        ]);
    }

    public function initiate_request(Request $request)
    {
        $abstracted_functions = new AbstractedFunctions();

        $pinSend = $request->input('pinSend');
        $senderNumber = $request->input('senderNumber');
        $receiverNumber = $request->input('receiverNumber');
        $amountTransacted = $request->input('amountTransacted');

        $senderId = DB::select('SELECT * from userdata WHERE email_address  = ?', [$senderNumber]);
        $receiverId = DB::select('SELECT * from userdata WHERE phone_number  = ?', [$receiverNumber]);

        $pinCheck = DB::select('SELECT * from userdata WHERE email_address  = ? AND password = ?', [$senderNumber, $abstracted_functions->decrypt_password($pinSend)]);
        if ($pinCheck) {
            $transaction_code = $abstracted_functions->generate_RefCode(8);
            $paymentrequests = new Paymentrequests;
            $paymentrequests->requestor_id = $senderId[0]->id;
            $paymentrequests->benefactor_id = $receiverId[0]->id;
            $paymentrequests->amount = $amountTransacted;
            $paymentrequests->request_title = "Payment Request";
            $paymentrequests->request_description = "Request payment from your contacts.";
            $paymentrequests->save();

            return response()->json([
                'status' => 200,
                'error' => false,
                'error_msg' => 'Request Successful.',
                'transaction_msg' => $transaction_code . '. Success, you have made a payment request of KES.' . $amountTransacted . ' to ' . $receiverId[0]->first_name . ', ' . $receiverNumber . '. on ' .  $paymentrequests->created_at,
            ]);
        } else {
            return response()->json([
                'status' => 500,
                'error' => true,
                'error_msg' => 'The PIN you entered was incorrect, try again.',
                'transaction_msg' => 'The PIN you entered was incorrect, try again.',
            ]);
        }
    }

    public function get_payment_request(Request $request)
    {
        $requestor = $request->input('senderNumber');

        $get_sender_id = DB::select('SELECT * from userdata WHERE email_address  = ?', [$requestor]);

        if (!empty($get_sender_id)) {
            $fetch_requests = DB::select('SELECT 
         pr.id,  
         pr.amount,
         ud.first_name as requestorName,
         ud.phone_number
     FROM 
         paymentrequests pr 
     INNER JOIN 
         userdata ud 
     ON 
         pr.requestor_id = ud.id 
     WHERE 
         pr.benefactor_id = ?', [$get_sender_id[0]->id]);

            return response()->json($fetch_requests);
        }
    }
}
