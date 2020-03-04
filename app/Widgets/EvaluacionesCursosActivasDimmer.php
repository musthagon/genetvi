<?php

namespace App\Widgets;

use Illuminate\Support\Str;
use TCG\Voyager\Facades\Voyager;

use App\Curso;

class EvaluacionesCursosActivasDimmer extends BaseDimmer
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
        $count = Curso::CursosEvaluacionesActivas();
        $string = 'Cursos en Proceso de Evaluación';

        return view('voyager::dimmer', array_merge($this->config, [
            'icon'   => 'voyager-pen',
            'title'  => "{$count} {$string}",
            'text'   => 'Total de cursos en proceso de evaluación actualmente',
            'button' => [
                'text' => __('Ver listado de cursos'),
                'link' => route('gestion.evaluaciones'),
            ],
            'image' => asset('img/widgets/cursos.png'),
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
