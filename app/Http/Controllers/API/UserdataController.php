<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Userdata;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\API\Functions\AbstractedFunctions;

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
        $abstracted_functions = new AbstractedFunctions();

        $login_id = $request->input('login_id');
        $password = $request->input('password');

        $needle   = "@";
        if (strpos($login_id, $needle) !== false) {
            // echo $email." is an email address";
            $column = "email_address";
            $login_id2 = $login_id;
        } else {
            // echo $email." is a phone number";
            $column = "phone_number";
            $login_id2 = $abstracted_functions->standardize_phonenumber($login_id);
        }

        $logins = DB::select('SELECT * from userdata WHERE ' . $column . '  = ? AND password = ?', [$login_id2, $abstracted_functions->decrypt_password($password)]);

        if ($logins) {
            return response()->json([
                'status' => 200,
                'error' => false,
                'uid' => $logins[0]->id,
                'error_msg' => 'Login Successful',
                'user' => [
                    'uid' => $logins[0]->id,
                    'name' =>  $logins[0]->first_name,
                    'created_at' =>  $logins[0]->created_at,
                    'email2' =>  $logins[0]->phone_number,
                    'email' =>  $logins[0]->email_address,
                ]
            ]);
        } else {
            return response()->json([
                'status' => 500,
                'error' => true,
                'error_msg' => 'Login not successful',
            ]);
          
        }
    }

    public function register(Request $request)
    {
        $abstracted_functions = new AbstractedFunctions();

        $name = $request->input('name');
        $phone_number1 = $request->input('email');
        $email_address = $request->input('email2');
        $password = $request->input('password');
        $user_role = $request->input('user_type');
        // $id_number = $request->input('id_number');

        $phone_number = $abstracted_functions->standardize_phonenumber($phone_number1);

        do {
            $ref_code = $abstracted_functions->generate_RefCode(6);
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
            $userdata->password = $abstracted_functions->decrypt_password($password);
            $userdata->app_version = "NULL";
            $userdata->referral_code = $ref_code;
            $userdata->firebase_reg_id = "NULL";
            $userdata->save();

            $return_user_data = DB::select('SELECT * from userdata WHERE phone_number  = ?', [$phone_number]);

            return response()->json([
                'status' => 200,
                'error' => false,
                'error_msg' => 'Successful',
                'uid' => $return_user_data[0]->id,
                'user' => [
                    'created_at' => $return_user_data[0]->created_at,
                    'email2' => $return_user_data[0]->phone_number,
                    'email' => $return_user_data[0]->email_address,
                    'name' => $return_user_data[0]->first_name,
                ]
            ]);
        }
    }

    public function get_contacts(Request $request)
    {

        $abstracted_functions = new AbstractedFunctions();
        $result2 = DB::select('SELECT phone_number, first_name from userdata');
        $data3 = json_encode($result2);
        $data22 = json_decode($data3, true);
        $d = array();

        $response_data = $request->getContent();
        $obj = json_decode($response_data, TRUE);
        foreach ($obj as $key => $value) {
            foreach ($data22 as $obj) {
                if ($abstracted_functions->standardize_phonenumber($value) == $obj['phone_number']) {
                    $d[] = array('name' => $obj['first_name'], 'phone' => $obj['phone_number'], 'image' => 'image.jpg');
                }
            }
        }
        $json = json_encode($d);
        $json2 = json_decode($json);


        return response()->json([
            'status' => 200,
            'error' => false,
            'error_msg' => 'Successful',
            'contacts_array' => $json2,
        ]);
    }

    public function pin_change(Request $request)
    {
        $phoneNumber = $request->input('phoneNumber');
        $pinNewChange = $request->input('pinNewChange');
        $pinOldChange = $request->input('pinOldChange');

        $abstracted_functions = new AbstractedFunctions();

        $pin_check = DB::select('SELECT * from userdata WHERE phone_number  = ? AND password = ?', [$abstracted_functions->standardize_phonenumber($phoneNumber), $abstracted_functions->decrypt_password($pinOldChange)]);

        if ($pin_check and sizeof($pin_check) == 1) {

            try {
                DB::select('UPDATE userdata SET password = ?  WHERE phone_number  = ?', [$abstracted_functions->decrypt_password($pinNewChange), $abstracted_functions->standardize_phonenumber($phoneNumber)]);
                return response()->json([
                    'status' => 200,
                    'error' => false,
                    'error_msg' => 'PIN Change successful.',
                    'transaction_msg' => 'PIN Change successful.',
                ]);
            } catch (\Illuminate\Database\QueryException $e) {
                return response()->json([
                    'status' => 500,
                    'error' => true,
                    'error_msg' => 'PIN Change unsuccessful.',
                    'transaction_msg' => 'PIN Change unsuccessful.',
                ]);
            }
        } else {
            return response()->json([
                'status' => 500,
                'error' => true,
                'error_msg' => 'The PIN you entered is incorrect.',
                'transaction_msg' => 'The PIN you entered is incorrect.',
            ]);
        }
    }
}
