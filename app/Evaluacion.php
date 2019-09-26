<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Evaluacion extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'evaluaciones';

    public function instrumento()    {
        return $this->belongsTo('App\Instrumento','categorias_instrumento_id','id');
    }

    public function curso()    {
        return $this->belongsTo('App\Curso','categorias_instrumento_id','id');
    }

    public function usuario()    {
        return $this->belongsTo('App\User','categorias_instrumento_id','id');
    }
}
