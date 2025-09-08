<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductModel extends Model
{
    use HasFactory;

    protected $table = 'products';
    protected $guarded =['id','created_at','updated_at'];

    public function purchaseItems() {
        return $this->hasMany(PurchaseItems::class, 'product_id');
    }
    public function category(){
       return $this->belongsTo(Category::class,'category_id');
    }
    public function brand(){
        return $this->belongsTo(Brand::class,'brand_id');
    }
    public function unit(){
        return $this->belongsTo(Unit::class,'unit_id');
    }
}
