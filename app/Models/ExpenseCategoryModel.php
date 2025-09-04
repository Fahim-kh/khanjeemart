<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpenseCategoryModel extends Model
{
    use HasFactory;
    protected $table ='expense_categories';
    protected $guarded =['id','created_at','updated_at'];

    public function expenses(){
        return $this->hasMany(ExpenseModel::class,'id','expense_category_id');
    }
}
