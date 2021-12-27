<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Testmail extends Mailable
{
    use Queueable, SerializesModels;


    public $mailbody;



    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($mailbody)
    {
        $this->mailbody = $mailbody;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Verify Your Email')->view('testmail');
    }
}
