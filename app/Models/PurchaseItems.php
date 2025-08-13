<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseItems extends Model
{
    use HasFactory;

    protected $table = 'purchase_items'; // table ka naam explicitly set karo
    protected $guarded = ['id', 'created_at', 'updated_at']; // mass assignment protection
}
