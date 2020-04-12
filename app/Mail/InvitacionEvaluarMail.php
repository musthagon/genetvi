<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Invitacion;

class InvitacionEvaluarMail extends Mailable
/*class InvitacionEvaluarMail extends Mailable implements ShouldQueue*/
{
    use Queueable, SerializesModels;

    protected $invitacion;
    protected $user_profile;
    protected $shortTemplate;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Invitacion $datosInvitacion, $user_profile, $shortTemplate = false){
        
        $this->invitacion    = $datosInvitacion;
        $this->user_profile  = $user_profile;
        $this->shortTemplate = $shortTemplate;

    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $tipoTemplate = $this->shortTemplate ? 'Short' : '';
        return $this->subject($this->user_profile['fullname'].', has sido Invitado a Evaluar el Curso '.$this->invitacion->curso->getNombre())
                    ->view('mails.enlace_evaluacion'.$tipoTemplate)
                    ->with([
                        'nombre_curso'       => $this->invitacion->curso->getNombre(),
                        'nombre_usuario'     => $this->user_profile['fullname'],
                        'token_invitacion'   => $this->invitacion->getToken(),
                        'nombre_instrumento' => $this->invitacion->instrumento->getNombre(),
                    ]);
    }
}
