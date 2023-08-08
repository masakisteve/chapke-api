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

    public static function get_transaction_fees($amount, $transaction_type) {
        $fee_table = [
            ['range' => [1, 54], 'sending' => 0, 'withdrawal' => 0],
            ['range' => [55, 105], 'sending' => 0, 'withdrawal' => 0],
            ['range' => [106, 550], 'sending' => 5, 'withdrawal' => 8],
            ['range' => [551, 1099], 'sending' => 11, 'withdrawal' => 25],
            ['range' => [1100, 1550], 'sending' => 20, 'withdrawal' => 25],
            ['range' => [1551, 2550], 'sending' => 28, 'withdrawal' => 25],
            ['range' => [2551, 3550], 'sending' => 45, 'withdrawal' => 40],
            ['range' => [3551, 5500], 'sending' => 50, 'withdrawal' => 60],
            ['range' => [5501, 7550], 'sending' => 70, 'withdrawal' => 75],
            ['range' => [7551, 10500], 'sending' => 80, 'withdrawal' => 100],
            ['range' => [10501, 15500], 'sending' => 85, 'withdrawal' => 130],
            ['range' => [15501, 20500], 'sending' => 90, 'withdrawal' => 150],
            ['range' => [20501, 35500], 'sending' => 100, 'withdrawal' => 180],
            ['range' => [35501, 50500], 'sending' => 100, 'withdrawal' => 200],
            ['range' => [50501, 150000], 'sending' => 100, 'withdrawal' => 250],
        ];
    
        foreach ($fee_table as $row) {
            if ($amount >= $row['range'][0] && $amount <= $row['range'][1]) {
                return ($transaction_type === 'sending') ? $row['sending'] : $row['withdrawal'];
            }
        }
    
        return 0; // Default fee if no matching range is found
    }
    
}
