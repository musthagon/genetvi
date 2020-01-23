<?php

namespace App;

class Role extends \TCG\Voyager\Models\Role
{
    public function permisos(){
        return $this->belongsToMany('App\Permission');
    }
}
