<?php

namespace App;

class Role extends \TCG\Voyager\Models\Role
{
    protected $fillable = ['id', 'name', 'display_name','created_at','updated_at'];

    public function permisos(){
        return $this->belongsToMany('App\Permission');
    }

    public static function getAllRoles(){
        return Role::all();
    }

    public function getID(){
        return $this->id;
    }
    public function getName(){
        return $this->name;
    }
    public function getDisplayName(){
        return $this->display_name;
    }
}
