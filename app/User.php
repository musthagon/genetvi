<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Hash;

class User extends \TCG\Voyager\Models\User
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','cvucv_username','cvucv_firstname','cvucv_lastname','cvucv_suspended','cvucv_id','avatar'
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

        if( !isset($data['name']) || !isset($data['email']) || !isset($data['password']) ){
            return null;
        }
        
        $user = new User;

        $user->name = $data['name'];
        $user->email = $data['email'];
        //$user->password = bcrypt($data['password']);
        $user->password = Hash::make($data['password']);

        if( isset($data['avatar']) && isset($data['cvucv_username']) && isset($data['cvucv_id']) &&
            isset($data['cvucv_lastname']) && isset($data['cvucv_firstname']) && isset($data['cvucv_suspended']) ){

            $user->avatar = $data['avatar'];
            $user->cvucv_username = $data['cvucv_username'];
            $user->cvucv_id = $data['cvucv_id'];
            $user->cvucv_lastname = $data['cvucv_lastname'];
            $user->cvucv_firstname = $data['cvucv_firstname'];
            $user->cvucv_suspended = $data['cvucv_suspended'];
            $user->name = $data['name'];
            $user->email = $data['email'];

        }

        $user->save();

        return $user;

        

        
    }
}
