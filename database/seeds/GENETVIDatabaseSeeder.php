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
        /*
        $this->seed('DataTypesTableSeeder');
        $this->seed('DataRowsTableSeeder');
        $this->seed('MenusTableSeeder');
        $this->seed('MenuItemsTableSeeder');

        $this->seed('RolesTableSeeder');
        $this->seed('PermissionsTableSeeder');
        $this->seed('PermissionRoleTableSeeder');
        $this->seed('SettingsTableSeeder');
        */


        $this->seed('CustomRolesGenetvi');

        $this->seed('GenetviSettingsTableSeeder');

        $this->seed('CursosParticipantesTableSeeder');
        $this->seed('InstrumentosPredeterminados');
        $this->seed('EstatusInvitacionTableSeeder');
        $this->seed('TipoInvitacionTableSeeder');
        $this->seed('MomentosEvaluacionTableSeeder');

        
    }
}
