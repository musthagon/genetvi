<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TipoInvitacion extends Model
{
    protected $table = 'tipo_invitaciones';
    protected $fillable = ['id', 'nombre', 'nombre_corto', 'descripcion'];

}
