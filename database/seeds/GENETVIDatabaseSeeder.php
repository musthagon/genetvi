<?php

use Illuminate\Database\Seeder;
use TCG\Voyager\Traits\Seedable;

class GENETVIDatabaseSeeder extends Seeder
{
    use Seedable;

    protected $seedersPath = __DIR__.'/';

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $this->seed('GenetviDataTypesTableSeeder');
        $this->seed('GenetviMenuItemsTableSeeder');
        $this->seed('GenetviRolesTableSeeder');
        $this->seed('GenetviSettingsTableSeeder');

        $this->seed('GenetviCursosParticipantesTableSeeder');
        $this->seed('GenetviInstrumentosPredeterminados');
        $this->seed('GenetviEstatusInvitacionTableSeeder');
        $this->seed('GenetviTipoInvitacionTableSeeder');
        $this->seed('GenetviMomentosEvaluacionTableSeeder');

        
    }
}
