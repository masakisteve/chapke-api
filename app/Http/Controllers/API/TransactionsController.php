<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Transactions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\API\Functions\AbstractedFunctions;

class TransactionsController extends Controller
{
    public function index()
    {
        $transactions = Transactions::all();
        return response()->json([
            'status' => 200,
            'message' => 'Successful',
            'transactions' => $transactions,
        ]);
    }

    public function store(Request $request)
    {
        $transactions = new Transactions;
        $transactions->user_id = $request->input('user_id');
        $transactions->transaction_type = $request->input('transaction_type');
        $transactions->transaction_code = $request->input('transaction_code');
        $transactions->notes = $request->input('notes');
        $transactions->state = $request->input('state');
        $transactions->transaction_dr = $request->input('transaction_dr');
        $transactions->transaction_cr = $request->input('transaction_cr');
        $transactions->senderNUmber = $request->input('senderNUmber');
        $transactions->receiverNumber = $request->input('receiverNumber');
        $transactions->save();

        return response()->json([
            'status' => 200,
            'message' => 'Successful'
        ]);
    }

    public function edit($id)
    {
        $transactions = Transactions::find($id);
        return response()->json([
            'status' => 200,
            'message' => 'Successful',
            'transactions' => $transactions,
        ]);
    }

    public function update(Request $request, $id)
    {
        $transactions = Transactions::find($id);
        $transactions->user_id = $request->input('user_id');
        $transactions->transaction_type = $request->input('transaction_type');
        $transactions->transaction_code = $request->input('transaction_code');
        $transactions->notes = $request->input('notes');
        $transactions->state = $request->input('state');
        $transactions->transaction_dr = $request->input('transaction_dr');
        $transactions->transaction_cr = $request->input('transaction_cr');
        $transactions->senderNUmber = $request->input('senderNUmber');
        $transactions->receiverNumber = $request->input('receiverNumber');
        $transactions->update();

        return response()->json([
            'status' => 200,
            'message' => 'Successful'
        ]);
    }

    public function withdraw_money(Request $request)
    {

        $abstracted_functions = new AbstractedFunctions();

        $amountTransacted = $request->input('amountTransacted');
        $receiverNumber = $request->input('receiverNumber');
        $senderNumber = $request->input('senderNumber');
        $pinWithdraw = $request->input('pinWithdraw');

        $pinCheck = DB::select('SELECT * from userdata WHERE phone_number  = ? AND password = ?', [$abstracted_functions->standardize_phonenumber($senderNumber), $abstracted_functions->decrypt_password($pinWithdraw)]);
        $getAgentDetails = DB::select('SELECT * from userdata WHERE phone_number  = ?', [$abstracted_functions->standardize_phonenumber($receiverNumber)]);

        $user_balance = $this->get_user_balance($senderNumber);

        if ($user_balance >= $amountTransacted) {
            if ($getAgentDetails and $getAgentDetails[0]->user_role == "agent") {
                if ($pinCheck) {

                    $transaction_code = $abstracted_functions->generate_RefCode(8);
                    $sender_number = $abstracted_functions->standardize_phonenumber($senderNumber);
                    $receiver_number = $abstracted_functions->standardize_phonenumber($receiverNumber);

                    $transactions = new Transactions;
                    $transactions->user_id = $pinCheck[0]->id;
                    $transactions->transaction_type = 'WithdrawMoney';
                    $transactions->transaction_code = $transaction_code;
                    $transactions->notes = 'withdraw';
                    $transactions->state = 'successful';
                    $transactions->transaction_dr = $amountTransacted;
                    $transactions->transaction_cr = '0';
                    $transactions->senderNUmber = $sender_number;
                    $transactions->receiverNumber = $receiver_number;
                    $transactions->save();

                    $transactions = new Transactions;
                    $transactions->user_id = $getAgentDetails[0]->id;
                    $transactions->transaction_type = 'WithdrawMoney';
                    $transactions->transaction_code = $transaction_code;
                    $transactions->notes = 'withdraw';
                    $transactions->state = 'successful';
                    $transactions->transaction_dr = '0';
                    $transactions->transaction_cr = $amountTransacted;
                    $transactions->senderNUmber = $senderNumber;
                    $transactions->receiverNumber = $receiverNumber;
                    $transactions->save();


                    return response()->json([
                        'status' => 200,
                        'error' => false,
                        'error_msg' => 'Transaction Successful.',
                        'transaction_msg' => $transaction_code . '. Success, you have withdrawn KES.' . $amountTransacted . ' from agent number ' . $receiverNumber . '. on ' .  $transactions->created_at,
                    ]);
                } else {
                    return response()->json([
                        'status' => 500,
                        'error' => true,
                        'error_msg' => 'The PIN you entered was incorrect, try again.',
                        'transaction_msg' => 'The PIN you entered was incorrect, try again.',
                    ]);
                }
            } else {
                return response()->json([
                    'status' => 500,
                    'error' => true,
                    'error_msg' => 'The agent number you entered is not a registered agent.',
                    'transaction_msg' => 'The agent number you entered is not a registered agent.',
                ]);
            }
        } else {
            return response()->json([
                'status' => 500,
                'error' => true,
                'error_msg' => 'You do not have enough balance to transact KES.' . $amountTransacted . '. You need to top  up atleast KES.' . $amountTransacted - $user_balance . '.',
                'transaction_msg' => 'You do not have enough balance to transact KES.' . $amountTransacted . '. You need to top  up atleast KES.' . $amountTransacted - $user_balance . '.',
            ]);
        }
    }

    public function send_money(Request $request)
    {

        $abstracted_functions = new AbstractedFunctions();

        $amountTransacted = $request->input('amountTransacted');
        $receiverNumber = $request->input('receiverNumber');
        $senderNumber = $request->input('senderNumber');
        $pinWithdraw = $request->input('pinSend');

        $pinCheck = DB::select('SELECT * from userdata WHERE phone_number  = ? AND password = ?', [$abstracted_functions->standardize_phonenumber($senderNumber), $abstracted_functions->decrypt_password($pinWithdraw)]);
        $getReceiverDetails = DB::select('SELECT * from userdata WHERE phone_number  = ?', [$abstracted_functions->standardize_phonenumber($receiverNumber)]);

        $user_balance = $this->get_user_balance($senderNumber);

        if ($user_balance >= $amountTransacted) {
            if ($getReceiverDetails) {
                if ($pinCheck) {

                    $transaction_code = $abstracted_functions->generate_RefCode(8);
                    $sender_number = $abstracted_functions->standardize_phonenumber($senderNumber);
                    $receiver_number = $abstracted_functions->standardize_phonenumber($receiverNumber);

                    $transactions = new Transactions;
                    $transactions->user_id = $pinCheck[0]->id;
                    $transactions->transaction_type = 'SendMoney';
                    $transactions->transaction_code = $transaction_code;
                    $transactions->notes = 'send';
                    $transactions->state = 'successful';
                    $transactions->transaction_dr = $amountTransacted;
                    $transactions->transaction_cr = '0';
                    $transactions->senderNUmber = $sender_number;
                    $transactions->receiverNumber = $receiver_number;
                    $transactions->save();

                    $transactions = new Transactions;
                    $transactions->user_id = $getReceiverDetails[0]->id;
                    $transactions->transaction_type = 'SendMoney';
                    $transactions->transaction_code = $transaction_code;
                    $transactions->notes = 'send';
                    $transactions->state = 'successful';
                    $transactions->transaction_dr = '0';
                    $transactions->transaction_cr = $amountTransacted;
                    $transactions->senderNUmber = $senderNumber;
                    $transactions->receiverNumber = $receiverNumber;
                    $transactions->save();

                    foreach ($getReceiverDetails as $receiver) {
                        $receiver_name = $receiver->first_name;
                        
                        }

                    return response()->json([
                        'status' => 200,
                        'error' => false,
                        'error_msg' => "Transaction Successful",
                        'transaction_msg' => $transaction_code . '. Success, you have sent KES.' . $amountTransacted . ' to '.$receiver_name.', ' . $receiverNumber . '. on ' .  $transactions->created_at,
                    ]);
                } else {
                    return response()->json([
                        'status' => 500,
                        'error' => true,
                        'error_msg' => "The PIN you entered was incorrect, try again.",
                        'transaction_msg' => 'The PIN you entered was incorrect, try again.',
                    ]);
                }
            } else {
                return response()->json([
                    'status' => 500,
                    'error' => true,
                    'error_msg' => "The receiver number you entered is not registered on Chap Money.",
                    'transaction_msg' => 'The receiver number you entered is not registered on Chap Money.',
                ]);
            }
        } else {
            return response()->json([
                'status' => 500,
                'error' => true,
                'error_msg' => 'You do not have enough balance to transact KES.' . $amountTransacted . '. You need to top  up atleast KES.' . $amountTransacted - $user_balance . '.',
                'transaction_msg' => 'You do not have enough balance to transact KES.' . $amountTransacted . '. You need to top  up atleast KES.' . $amountTransacted - $user_balance . '.',
            ]);
        }
    }

    public function user_balance(Request $request)
    {
        return response()->json([
            'status' => 200,
            'error' => false,
            'error_msg' => 'Successful',
            'balance' => $this->get_user_balance($request->input('phoneNumber')),
            'transaction_msg' => "Your Balance is: KES." . $this->get_user_balance($request->input('phoneNumber')),
        ]);
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

    public function account_statements($id)
    {
        $abstracted_functions = new AbstractedFunctions();

        $phone_number = $abstracted_functions->standardize_phonenumber($id);
        $get_user_id = DB::select('SELECT id from userdata WHERE phone_number  = ?', [$phone_number]);
        $user_id = $get_user_id[0]->id;

        $get_statements = DB::select('SELECT * from transactions WHERE user_id = ?', [$user_id]);

        if ($get_statements and sizeof($get_statements) > 0) {
            $encoded = json_encode($get_statements, true);
            $decoded = json_decode($encoded, true);
            $d = array();
            foreach ($decoded as $obj) {
                $amount_transacted = $obj['transaction_dr'] + $obj['transaction_cr'];
                $d[] = array(
                    'transactionId' => $obj['transaction_code'],
                    'receiverNumber' => $obj['receiverNumber'],
                    'senderNumber' => $obj['senderNumber'],
                    'amountTransacted' => $amount_transacted,
                    'date' => $obj['created_at'],
                    'transactionType' => $obj['transaction_type'],
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
        }else {
            return response()->json(
                
            );
        }
    }
}