<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    use HasFactory;
     protected $table ='module';
     protected $guarded=['id','created_at','updated_at'];

     
      public function childs()
    {
        return $this->hasMany(Module::class, 'parent_id', 'id');
    }
}
