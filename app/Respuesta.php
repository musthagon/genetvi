<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Respuesta extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'respuestas';

    protected $fillable = ['id','value_string','value_request','value_percentil','indicador_nombre','indicador_id','categoria_id','evaluacion_id','created_at','updated_at'];

    public function evaluacion(){
        return $this->belongsTo('App\Evaluacion','evaluacion_id','id');
    }
    public function categoria(){
        return $this->belongsTo('App\Categoria','categoria_id','id');
    }

}
