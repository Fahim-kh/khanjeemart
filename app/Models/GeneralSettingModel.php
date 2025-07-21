<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GeneralSettingModel extends Model
{
    use HasFactory;
    protected $table= 'general_settings';
    protected $guarded=['id','created_at','updated_at'];
}
