<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentTypeModel extends Model
{
    use HasFactory;
    protected $table ='payment_type';
    protected $guarded =['id','created_at','updated_at'];
}
