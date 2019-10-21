<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EstatusInvitacion extends Model
{
    protected $table = 'estatus_invitaciones';
    protected $fillable = ['id', 'nombre', 'nombre_corto', 'descripcion'];

}
