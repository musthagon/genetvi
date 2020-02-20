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
        'name', 'email', 'password','cvucv_username','cvucv_firstname','cvucv_lastname','cvucv_suspended','cvucv_id','avatar','created_at','updated_at'
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
    public function rolesMany(){
        return $this->belongsTo('App\Role','user_roles','user_id','role_id');
    }

    public function getCVUCV_USER_ID(){
        return $this->cvucv_id;
    }

    public static function username(){
        return 'cvucv_username';
    }
    protected static function password(){
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

    public function updateData($request){
        //dd($request);
        $c = 0;
        if(!isset($request)){
            return;
        }
        
        if(isset($request->name)){
            $this->name = $request->name;
            $c++;
        }
        if(isset($request->email)){
            $this->email = $request->email;
            $c++;
        }
        if(isset($request->password)){
            $this->password = Hash::make($request->password);
            $c++;
        }
        if(isset($request->cvucv_username)){
            $this->cvucv_username = $request->cvucv_username;
            $c++;
        }
        if(isset($request->cvucv_firstname)){
            $this->cvucv_firstname = $request->cvucv_firstname;
            $c++;
        }
        if(isset($request->cvucv_lastname)){
            $this->cvucv_lastname = $request->cvucv_lastname;
            $c++;
        }
        if(isset($request->cvucv_suspended)){
            $this->cvucv_suspended = $request->cvucv_suspended;
            $c++;
        }
        if(isset($request->cvucv_id)){
            $this->cvucv_id = $request->cvucv_id;
            $c++;
        }
        if(isset($request->avatar)){
            $this->avatar = $request->avatar;
            $c++;
        }
        if(isset($request->user_belongsto_role_relationship)){
            $this->role_id = $request->user_belongsto_role_relationship;
            $c++;
        }
        /*if(isset($request->user_belongstomany_role_relationship)){
            foreach($request->user_belongstomany_role_relationship as $role){
                $rol = Role::find($role);
                if(!empty($rol)){
                    $this->rolesMany()->attach($rol);
                }
            }
        }*/

        if($c == 0){
            return;
        }
        $this->save();
    }
}
