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

    protected $fillable = ['id','value_string','value_percentil','indicador_nombre','indicador_id','categoria_id','evaluacion_id','created_at','updated_at'];

    public static function create($value_string, $value_percentil, $indicador_nombre, $indicador_id, $categoria_id, $evaluacion_id)   {
        $new = new Respuesta();

        $new->value_string      = $value_string;
        $new->value_percentil   = $value_percentil;
        $new->indicador_nombre  = $indicador_nombre;
        $new->indicador_id      = $indicador_id;
        $new->categoria_id      = $categoria_id;
        $new->evaluacion_id     = $evaluacion_id;
        $new->created_at        = \Carbon\Carbon::now();
        $new->updated_at        = \Carbon\Carbon::now();

        $new->save();

    }

    public function evaluacion(){
        return $this->belongsTo('App\Evaluacion','evaluacion_id','id');
    }
    public function indicador(){
        return $this->belongsTo('App\Indicador','indicador_id','id');
    }
    public function categoria(){
        return $this->belongsTo('App\Categoria','categoria_id','id');
    }

    public function getID(){
        return $this->id;
    }

    public function get_value_string(){
        return $this->value_string;
    }

    public function get_value_percentil(){
        return $this->value_percentil;
    }

    public function get_indicador_nombre(){
        return $this->indicador_nombre;
    }

}
