<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpenseModel extends Model
{
    use HasFactory;

    protected $table ='expense';
    protected $guarded =['id','created_at','updated_at'];

    public function expenseCategory(){
        return $this->belongsTo(ExpenseCategoryModel::class,'expense_category_id');
    }

}
