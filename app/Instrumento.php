<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Instrumento extends Model
{
    protected $fillable = [
        'nombre'
    ];
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'instrumentos';

    public function categorias(){
        return $this->belongsToMany('App\Categoria','categorias_instrumentos','instrumento_id','categoria_id')->using('App\CategoriaInstrumento');
    }

    public function evaluaciones(){
        return $this->hasMany('App\Evaluacion','instrumento_id','id');
    }

}
