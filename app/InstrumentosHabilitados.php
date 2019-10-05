<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\Pivot;

class InstrumentosHabilitados extends Pivot
{
    protected $table = 'instrumentos_habilitados';

    protected $fillable = ['id','categoria_id','instrumento_id'];

}