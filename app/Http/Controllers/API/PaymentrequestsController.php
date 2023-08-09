<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Paymentrequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\API\Functions\AbstractedFunctions;
use Illuminate\Support\Facades\Http;

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
        $receiverNumber = $abstracted_functions->standardize_phonenumber($request->input('receiverNumber'));
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
            $paymentrequests->request_title = "Request Payment";
            $paymentrequests->request_description = "Request payment from your contacts.";
            $paymentrequests->save();

            return response()->json([
                'status' => 200,
                'error' => false,
                'error_msg' => 'Request Successful.',
                'transaction_msg' => $transaction_code . '. Success, you have requested a payment request of KES.' . $amountTransacted . ' to ' . $receiverId[0]->first_name . ', ' . $receiverNumber . '. on ' .  $paymentrequests->created_at,
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

    public function reject_or_approve_request(Request $request)
    {
        $abstracted_functions = new AbstractedFunctions();

        $name = $request->input('name');
        $phone = $request->input('phone');
        $id = $request->input('id');
        $amount = $request->input('amount');
        $requestor_Number = $request->input('requestor_Number');
        $request_desc = $request->input('request_desc');
        $pinSend = $request->input('pinSend');

        $getSenderDetails = DB::select('SELECT * from userdata WHERE email_address  = ?', [$abstracted_functions->standardize_phonenumber($requestor_Number)]);
        $sender_details = $getSenderDetails[0];
        $getReceiverDetails = DB::select('SELECT * from userdata WHERE phone_number  = ?', [$abstracted_functions->standardize_phonenumber($phone)]);
        $receiver_details = $getReceiverDetails[0];
        $user_balance = $this->get_user_balance($sender_details->phone_number);


        if ($request_desc === "approve") {
            $pinCheck = DB::select('SELECT * from userdata WHERE email_address  = ? AND password = ?', [$requestor_Number, $abstracted_functions->decrypt_password($pinSend)]);
            if (count($pinCheck) > 0) {
                // Pin was correct
                $user = $pinCheck[0];
                if ($user_balance >= $amount) {
                    $pinCheck = DB::select('UPDATE paymentrequests SET request_status = ? WHERE id  = ?', [$request_desc, $id]);
                    $response = Http::post('https://api.chapke.com/public/api/send_money', [
                        'pinSend' => $pinSend,
                        'senderNumber' =>  $user->phone_number,
                        'receiverNumber' => $phone,
                        'amountTransacted' => $amount,
                    ]);
                    if ($response->successful()) {
                        // The request was successful, handle the response
                        $responseData = $response->json();
                        if ($responseData['status'] == 200) {
                            // Transaction successful
                            $transactionMsg = $responseData['transaction_msg'];
                            $error_msg = $responseData['error_msg'];
                            // Handle the success message
                            return response()->json([
                                'status' => 200,
                                'error' => false,
                                'error_msg' => $error_msg,
                                'transaction_msg' => $transactionMsg,
                            ]);
                        } else {
                            // Transaction failed, handle the error message
                            $errorMsg = $responseData['error_msg'];
                            return response()->json([
                                'status' => 500,
                                'error' => true,
                                'error_msg' => $errorMsg,
                                'transaction_msg' => $errorMsg,
                            ]);
                        }
                    } else {
                        // The request was not successful, handle the failure scenario
                        return response()->json([
                            'status' => 500,
                            'error' => true,
                            'error_msg' => 'Request was not successful. Please retry.',
                            'transaction_msg' => 'Request was not successful. Please retry.',
                        ]);
                    }
                } else {
                    return response()->json([
                        'status' => 500,
                        'error' => true,
                        'error_msg' => 'Your balance is ' . $user_balance . ' which is not enough to aprove a request of ' . $amount . '.',
                        'transaction_msg' => 'Your balance is ' . $user_balance . ' which is not enough to aprove a request of ' . $amount . '.',
                    ]);
                }
            } else {
                return response()->json([
                    'status' => 500,
                    'error' => true,
                    'error_msg' => 'The PIN you entered was incorrect, try again.',
                    'transaction_msg' => 'The PIN you entered was incorrect, try again.',
                ]);
            }
        } else if ($request_desc === "reject") {
            DB::select('UPDATE paymentrequests SET request_status = ? WHERE id  = ?', [$request_desc, $id]);

            $receiver_title = 'Receiving';
            $receiver_message = 'Your payment request of ' . $amount . ' has been rejected by ' . $sender_details->first_name . '';
            $receiver_userid = $receiver_details->id;
            $receiver_active = 1;

            $sender_title = 'Sending';
            $sender_message = 'You have successfully cancelled the payment request of ' . $amount . ' initiated by ' . $name . '';
            $sender_userid = $sender_details->id;
            $sender_active = 1;

            $this->send_notification($receiver_title, $receiver_message, $receiver_userid, $receiver_active);
            $this->send_notification($sender_title, $sender_message, $sender_userid, $sender_active);
            return response()->json([
                'status' => 200,
                'error' => false,
                'error_msg' => 'You have successfully cancelled the payment request of ' . $amount . ' initiated by ' . $name . '',
                'transaction_msg' => 'You have successfully cancelled the payment request of ' . $amount . ' initiated by ' . $name . '',
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
                    pr.request_status = "pending"
                AND
                    pr.benefactor_id = ?', [$get_sender_id[0]->id]);

            return response()->json($fetch_requests);
        }
    }

    public function get_user_balance($phone_number_param)
    {
        $abstracted_functions = new AbstractedFunctions();

        $phone_number = $abstracted_functions->standardize_phonenumber($phone_number_param);
        $get_user_id = DB::select('SELECT id from userdata WHERE phone_number  = ?', [$phone_number]);
        $user_id = $get_user_id[0]->id;

        $get_debits = DB::select('SELECT transaction_dr from transactions WHERE user_id = ?', [$user_id]);
        $get_credits = DB::select('SELECT transaction_cr from transactions WHERE user_id = ?', [$user_id]);

        $dr_total = 0;
        $cr_total = 0;

        foreach ($get_debits as $get_debit) {
            $dr_total += $get_debit->transaction_dr;
        }

        foreach ($get_credits as $get_credit) {
            $cr_total += $get_credit->transaction_cr;
        }

        $user_balance = $cr_total - $dr_total;
        return strval($user_balance);
    }

    public function send_notification($title, $message, $user_id, $active)
    {
        Http::post('https://api.chapke.com/public/api/add-notifications', [
            'title' => $title,
            'message' =>  $message,
            'user_id' => $user_id,
            'active' => $active,
        ]);
    }
}
