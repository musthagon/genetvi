<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\PeriodoLectivo;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule){

        //echo date("m-d-Y H:i:s", strtotime( \Carbon\Carbon::now()));
        //echo " ";

        $schedule->call(function () {
            $this->actualizar_periodos_lectivos();
        })->everyMinute();

    }

    //Actualizamos el momento de evaluacion activo de todos los periodos lectivos
    public function actualizar_periodos_lectivos(){

        $periodos = PeriodoLectivo::all();

        foreach ($periodos as $periodo) {
            
            $momento_evaluacion1 = $periodo->getMomento_evaluacion_activo_id();
            $momento_evaluacion2 = $periodo->actualizarMomentoEvaluacion();
            if($periodo->cambioMomentoEvaluacion($momento_evaluacion1, $momento_evaluacion2)){
                echo "Periodo: ".$periodo->id." invitacion masiva ";
                //Deshabilitamos el momento evaluacion anterior
                //Realizamos las invitaciones al proximo periodo
                //********************************** */
            }
        }

    }
    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
