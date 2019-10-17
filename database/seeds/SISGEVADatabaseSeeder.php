<?php

use Illuminate\Database\Seeder;
use TCG\Voyager\Traits\Seedable;

class SISGEVADatabaseSeeder extends Seeder
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
    }
}
