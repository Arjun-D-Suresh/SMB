<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class processfile extends Mailable
{
    use Queueable, SerializesModels;
    public $maildata;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($maildata)
    {
        $this->maildata = $maildata;
        $companyname = $this->maildata['companyname'];
        // $file = $this->maildata['file'];
        $dividendamount = $this->maildata['dividendamount'];
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $this->markdown('emails.process')
            ->with([
                'comapanyname' => $this->maildata['companyname'],
                'dividendamount' => $this->maildata['dividendamount']
            ]);
        // ->attach($this->maildata['file']);
        // ->attachData($this->maildata['file'], $this->maildata['name'], [
        //     'mime' => 'xls',
        //     'Subject' => 'File uploaded',
        // ]);
    }
}
