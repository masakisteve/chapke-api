<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Userdata extends Model
{
    use HasFactory;
    protected $table = 'userdata';
    protected $fillable =[
        'title',
        'first_name',
        'middle_name',
        'last_name',
        'email_address',
        'phone_number',
        'user_role',
        'id_number',
        'date_of_birth',
        'gender',
        'password',
        'app_version',
        'referral_code',
        'firebase_reg_id',
    ];
}
