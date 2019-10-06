<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use TCG\Voyager\Facades\Voyager;

class Role extends \TCG\Voyager\Models\Role
{
    public function permisos(){
        return $this->belongsToMany('App\Permission');
    }
}
