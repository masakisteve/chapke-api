<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Transactions;
use Illuminate\Http\Request;

class TransactionsController extends Controller
{
    public function index(){
        $transactions = Transactions::all();
        return response()->json([
            'status'=>200,
            'message'=>'Successful',
            'transactions'=>$transactions,
        ]);
    }
 
    public function store(Request $request){
        $transactions = new Transactions;
        $transactions->user_id = $request->input('user_id');
        $transactions->transaction_type = $request->input('transaction_type');
        $transactions->transaction_code = $request->input('transaction_code');
        $transactions->notes = $request->input('notes');
        $transactions->state = $request->input('state');
        $transactions->transaction_dr = $request->input('transaction_dr');
        $transactions->transaction_cr = $request->input('transaction_cr');
        $transactions->save();

        return response()->json([
            'status'=>200,
            'message'=>'Successful'
        ]);
    }

    public function edit($id){
        $transactions = Transactions::find($id);
        return response()->json([
            'status'=>200,
            'message'=>'Successful',
            'transactions'=>$transactions,
        ]);
    }

    public function update(Request $request, $id){
        $transactions = Transactions::find($id);
        $transactions->user_id = $request->input('user_id');
        $transactions->transaction_type = $request->input('transaction_type');
        $transactions->transaction_code = $request->input('transaction_code');
        $transactions->notes = $request->input('notes');
        $transactions->state = $request->input('state');
        $transactions->transaction_dr = $request->input('transaction_dr');
        $transactions->transaction_cr = $request->input('transaction_cr');
        $transactions->update();

        return response()->json([
            'status'=>200,
            'message'=>'Successful'
        ]);
    }
}
