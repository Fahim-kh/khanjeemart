<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleDetail extends Model
{
    use HasFactory;
    protected $table = 'sale_details'; // table ka naam explicitly set karo
    protected $guarded = ['id', 'created_at', 'updated_at']; // mass assignment protection
}
