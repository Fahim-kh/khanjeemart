<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use DB;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = ['id','created_at','updated_at']; 

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];


    public function role()
    {
        return $this->belongsTo(Role::class);
    }
   
    public function permission()
    {
        return $this->hasMany(RolePermission::class, 'role_id','role_id');
    }

    public function getmodules()
    {
        // Get the user's permitted module IDs
        $perms = $this->permission()
                    ->select(DB::raw("GROUP_CONCAT(module_id SEPARATOR ',') as `modules`"))
                    ->where('pview', '=', '1')
                    ->first();

        // If no permissions found, return empty collection
        if (!$perms || empty($perms->modules)) {
            return collect();
        }

        $moduleIds = explode(',', $perms->modules);

        // Get all modules (including potential parents and children)
        $allModules = Module::whereIn('id', $moduleIds)
                            ->orderBy('sorting')
                            ->get();

        // Organize modules hierarchically
        $modules = $allModules->where('parent_id', 0);
        
        foreach ($modules as $module) {
            $module->childs = $allModules->where('parent_id', $module->id)->sortBy('sorting');
        }

        return $modules;
    }
    public function hasPer($perm = null)
    {
        if(is_null($perm)) return false;
        $module = Module::where('name',$perm)->first();
        $perms = $this->permission()->select('pview','pedit','pcreate','pdelete')->where('module_id', '=', $module->id)->first();

        return ($perms) ? $perms->toArray() : [];
    }

    
   
}
