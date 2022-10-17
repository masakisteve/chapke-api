<?php

namespace App\Http\Controllers\API\Functions;

class AbstractedFunctions
{
    public static function generate_RefCode($length)
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

    public static function decrypt_password($password)
    {
        $key = env('PASSWORD_ENCRYPTION_KEY');
        $passwordd = hash_hmac('sha256', $password, $key);
        return $passwordd;
    }

    public static function standardize_phonenumber($login_id)
    {
        if (strlen($login_id) == 10) {

            $login_id1 = substr($login_id, 1);
            $login_id2 = "254" . $login_id1;
        } else if (strlen($login_id) == 9) {

            $login_id2 = "254" . $login_id;
        } else if (strlen($login_id) == 13) {

            $login_id2 = substr($login_id, 1);
        } else {
            $login_id2 = $login_id;
        }
        return $login_id2;
    }
}
