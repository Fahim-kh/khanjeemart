<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RolePermission extends Model
{
    use HasFactory;
     protected $table ='role_permission';
     protected $guarded=['id','created_at','updated_at'];

	public function role()
	{
	    return $this->belongsTo(Role::class);
	}


	public function module()
	{
	    return $this->belongsTo(Module::class);
	}
}