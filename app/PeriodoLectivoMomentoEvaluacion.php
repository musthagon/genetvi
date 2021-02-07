<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\Pivot;

class PeriodoLectivoMomentoEvaluacion extends Pivot
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'periodos_lectivos_momentos_evaluacion';
    protected $fillable = [
        'id',
        'periodo_lectivo_id',
        'momento_evaluacion_id',
        'fecha_inicio',
        'fecha_fin',
        'opciones',
        'created_at',
        'updated_at'];

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;

    public static function get_fecha_inicio_field(){
        return 'fecha_inicio';
    }
    public static function get_fecha_fin_field(){
        return 'fecha_fin';
    }
    public static function get_opciones_field(){
        return 'opciones';
    }
    public static function get_created_at_field(){
        return 'created_at';
    }
    public static function get_updated_at_field(){
        return 'updated_at';
    }
    public function get_fecha_inicio(){
        return $this->fecha_inicio;
    }
    public function get_fecha_fin(){
        return $this->fecha_fin;
    }
    public function get_opciones(){
        return $this->opciones;
    }
    public function get_created_at(){
        return $this->created_at;
    }
    public function get_updated_at(){
        return $this->updated_at;
    }
}
