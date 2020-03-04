<?php

namespace App\Widgets;

use Illuminate\Support\Str;
use TCG\Voyager\Facades\Voyager;

use App\Invitacion;

class EstatusEvaluacionesCursosDimmer extends BaseDimmer
{
    /**
     * The configuration array.
     *
     * @var array
     */
    protected $config = [];

    /**
     * Treat this method as a controller action.
     * Return view() or other content to display.
     */
    public function run()
    {
        Invitacion::EstatusEvaluacionesCursos($estatus, $estatus_count);
        $string = 'Estatus de Evaluaciones';
        $string2 = '';
        foreach($estatus as $estatusIndex => $estatusActual){
            $string2 = $string2.$estatus_count[$estatusIndex].' evaluaciones '.$estatusActual.'s<br>';
        }

        return view('voyager::dimmer', array_merge($this->config, [
            /*'icon'   => 'voyager-bar-chart',*/
            'title'  => "{$string}",
            'text'   => "{$string2}Estado de Todas las Evaluaciones hasta el momento",
            'button' => [
                'text' => __('Ver listado de cursos'),
                'link' => route('gestion.evaluaciones'),
            ],
            'image' => asset('img/widgets/cursos2.png'),
        ]));
    }

    /**
     * Determine if the widget should be displayed.
     *
     * @return bool
     */
    public function shouldBeDisplayed()
    {
        return auth()->user()->hasRole('admin');
    }
}
