<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Work extends Model
{
    protected $fillable = ['user_id','building_site_id','truck_no','work_description','date','time','created_by','updated_at'];
}
