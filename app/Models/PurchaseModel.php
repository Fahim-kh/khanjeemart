<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseModel extends Model
{
    use HasFactory;

    protected $table = 'purchases';
    protected $guarded = ['id', 'created_at', 'updated_at'];

    // Optional: relation with items
    public function items()
    {
        return $this->hasMany(PurchaseItems::class, 'purchase_id');
    }
}