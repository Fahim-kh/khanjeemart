<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;
    protected $table ='role';
    protected $guarded=['id','created_at','updated_at'];

    public function permission()
    {
        return $this->hasMany(RolePermission::class);
    }

    public function hasPer($perm = null)
    {
        if(is_null($perm)) return false;
        $perms = $this->permission()->select('pview','pedit','pcreate','pdelete')->where('module_id', '=', $perm)->get();
        return $perms->toArray();
    }
}
