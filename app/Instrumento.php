<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Instrumento extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'instrumentos';

    /*public function categorias(){
        return $this->hasMany('App\CategoriaDeInstrumento','instrumento_id','id');
    }

    public function evaluaciones(){
        return $this->hasMany('App\Evaluacion','instrumento_id','id');
    }*/

    public function campos(){
        return $this->hasMany('App\Campo','instrumento_id','id');
    }

    public function campos_padre(){
        return $this->hasMany('App\Campo','instrumento_id','id')
            ->whereNull('padre_id');;
    }

}
