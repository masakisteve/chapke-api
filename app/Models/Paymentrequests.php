<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Paymentrequests extends Model
{
    use HasFactory;
    protected $table = 'paymentrequests';
    protected $fillable =[
        'requestor_id',
        'benefactor_id',
        'amount',
        'request_title',
        'request_description',
      
    ];
}
