<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transactions extends Model
{
    use HasFactory;
    protected $table = 'transactions';
    protected $fillable =[
        'user_id',
        'transaction_type',
        'transaction_code',
        'notes',
        'state',
        'transaction_dr',
        'transaction_cr',
    ];
}
