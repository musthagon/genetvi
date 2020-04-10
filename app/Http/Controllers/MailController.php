<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Instrumento;
use App\Curso;
use App\Evaluacion;
use App\Respuesta;
use App\Invitacion;
use App\TipoInvitacion;
use App\MomentosEvaluacion;
use App\Estatus;
use phpDocumentor\Reflection\Types\Boolean;

class MailController extends Controller
{

    public function index(Request $request){
        
        $evaluationLink    = $request->link;

        return view('mails.enlace_evaluacion', compact('evaluationLink'));
    }

}
