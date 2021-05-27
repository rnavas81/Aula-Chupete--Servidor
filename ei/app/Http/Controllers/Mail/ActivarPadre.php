<?php

namespace App\Http\Controllers\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ActivarPadre extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    public $name;
    public $url;
    public $password;

    public function __construct($name, $lastname, $url, $password=null)
    {
        $this->name = $name . ' ' . $lastname;
        $this->url = $url;
        $this->password = $password;

    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('activateParent');
    }
}
