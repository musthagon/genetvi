<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends \TCG\Voyager\Models\User
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','cvucv_username','cvucv_lastname','cvucv_suspended','cvucv_token','cvucv_id','avatar'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token'
    ];

    public function evaluaciones(){
        return $this->hasMany('App\Evaluacion','usuario_id','id');
    }
    
    public function roles(){
        return $this->belongsTo('App\Role','role_id','id');
    }

    public function getCVUCV_USER_ID(){
        return $this->cvucv_id;
    }

    public static function username(){
        return 'cvucv_username';
    }
    public static function password(){
        return 'password';
    }

    public static function create(array $data)
    {
        return User::create([
            'name'              => $data['name'],
            'email'             => $data['email'],
            'password'          => bcrypt($data['password']),
            'avatar'            => $data['avatar'],
            'cvucv_username'    => $data['cvucv_username'],
            'cvucv_id'          => $data['cvucv_id'],
            'cvucv_lastname'    => $data['cvucv_lastname'],
            'cvucv_suspended'   => $data['cvucv_suspended'],
            'cvucv_token'       => $data['cvucv_token'],
        ]);
    }
}
