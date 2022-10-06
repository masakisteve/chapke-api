<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Userdata;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserdataController extends Controller
{
    public function index()
    {
        $userdata = Userdata::all();
        return response()->json([
            'status' => 200,
            'message' => 'Successful',
            'accountpreferences' => $userdata,
        ]);
    }

    public function store(Request $request)
    {
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
        $userdata->firebase_reg_id = $request->input('firebase_reg_id');
        $userdata->save();

        return response()->json([
            'status' => 200,
            'message' => 'Successful'
        ]);
    }

    public function edit($id)
    {
        $userdata = Userdata::find($id);
        return response()->json([
            'status' => 200,
            'message' => 'Successful',
            'accountpreferences' => $userdata,
        ]);
    }

    public function update(Request $request, $id)
    {
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
            'status' => 200,
            'message' => 'Successful'
        ]);
    }

    public function login(Request $request)
    {

        $email = $request->input('email');
        $password = $request->input('password');

        $key = env('PASSWORD_ENCRYPTION_KEY');
        $passwordd = hash_hmac('sha256', $password, $key);
        // $password2 = md5($password);

        $needle   = "@";
        if (strpos($email, $needle) !== false) {
            // echo $email." is an email address";
            $column = "email_address";
            $email2 = $email;
            $loginMethod = "Email Address";
        } else {
            // echo $email." is a phone number";
            $column = "phone_number";
            $loginMethod = "Phone Number";

            if (strlen($email) == 10) {

                $email1 = substr($email, 1);
                $email2 = "254" . $email1;
            } else if (strlen($email) == 9) {

                $email2 = "254" . $email;
            } else if (strlen($email) == 13) {

                $email2 = substr($email, 1);
            } else {
                $email2 = $email;
            }
        }

        $logins = DB::select('SELECT * from userdata WHERE ' . $column . '  = ? AND password = ?', [$email2, $passwordd]);

        if ($logins) {
            return response()->json([
                'status' => 200,
                'error' => false,
                'message' => 'Login Successful',
                'uid' => $logins[0]->id,
                'name' =>  $logins[0]->first_name,
                'created_at' =>  $logins[0]->created_at,
                'email2' =>  $logins[0]->phone_number,
                'email' =>  $logins[0]->email_address,
            ]);
        } else {
            return response()->json([
                'status' => 500,
                'message' => 'Login not successful',
            ]);
        }
    }

    public function register(Request $request)
    {
        function generate_RefCode($length)
        {

            $alphabets = range('A', 'Z');
            $numbers = range('0', '9');
            $final_array = array_merge($numbers, $alphabets);

            $transaction = '';

            while ($length--) {
                $key = array_rand($final_array);
                $transaction .= $final_array[$key];
            }

            return $transaction;
        }
        $name = $request->input('name');
        $phone_number = $request->input('email');
        $email_address = $request->input('email2');
        $password = $request->input('password');
        $user_role = $request->input('user_type');
        // $id_number = $request->input('id_number');

        if (strlen($phone_number) == 10) {

            $phone_number1 = substr($phone_number, 1);
            $phone_number = "254" . $phone_number1;
        } else if (strlen($phone_number) == 9) {

            $phone_number = "254" . $phone_number;
        } else if (strlen($phone_number) == 13) {

            $phone_number = substr($phone_number, 1);
        } else {
            $phone_number = $phone_number;
        }

        $key = env('PASSWORD_ENCRYPTION_KEY');
        $passwordd = hash_hmac('sha256', $password, $key);

        do {
            $ref_code = generate_RefCode(6);
            $resultref_code = DB::select('SELECT * from userdata WHERE referral_code  = ?', [$ref_code]);
        } while (sizeof($resultref_code) >= 1);

        $result2 = DB::select('SELECT phone_number from userdata WHERE phone_number  = ?', [$phone_number]);
        // $result3 = DB::select('SELECT id_number from userdata WHERE id_number  = ?', [$id_number]);
        $result4 = DB::select('SELECT email_address from userdata WHERE email_address  = ?', [$email_address]);


        if (sizeof($result2) >= 1) {

            $response["error"] = TRUE;
            $response["error_msg"] = "An account with the same phone number already exists !";
            echo json_encode($response);
        }
        //  else if (sizeof($result3) >= 1) {

        //     $response["error"] = TRUE;
        //     $response["error_msg"] = "An account with the same id number already exists !";
        //     echo json_encode($response);
        // } 
        else if (sizeof($result4) >= 1) {

            $response["error"] = TRUE;
            $response["error_msg"] = "An account with the same email already exists !";
            echo json_encode($response);
        } else if (sizeof($result4) == 0) {

            $userdata = new Userdata();
            $userdata->title = "NULL";
            $userdata->first_name = $name;
            $userdata->middle_name = "NULL";
            $userdata->last_name = "NULL";
            $userdata->email_address = $email_address;
            $userdata->phone_number = $phone_number;
            $userdata->user_role = $user_role;
            $userdata->id_number = "NULL";
            $userdata->date_of_birth = "NULL";
            $userdata->gender = "NULL";
            $userdata->password = $passwordd;
            $userdata->app_version = "NULL";
            $userdata->referral_code = $ref_code;
            $userdata->firebase_reg_id = "NULL";
            $userdata->save();

            $return_user_data = DB::select('SELECT * from userdata WHERE phone_number  = ?', [$phone_number]);


            return response()->json([
                'status' => 200,
                'message' => 'Successful',
                'name' => $return_user_data[0]->first_name,
            ]);
        }
    }
}
