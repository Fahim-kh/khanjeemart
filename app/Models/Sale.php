<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;
    protected $table = 'sale_summary';
    protected $guarded = ['id', 'created_at', 'updated_at'];

    // Optional: relation with items
    public function items()
    {
        //return $this->hasMany(SaleDetail::class, 'sale_summary_id')->orderBy('id', 'asc');
        return $this->hasMany(SaleDetail::class, 'sale_summary_id');
    }
}