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
        $this->seed('CustomRolesSisgeva');
        $this->seed('CursosParticipantesTableSeeder');
        $this->seed('InstrumentosPredeterminados');
        $this->seed('EstatusInvitacionTableSeeder');
        $this->seed('TipoInvitacionTableSeeder');
    }
}
