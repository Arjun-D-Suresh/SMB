<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Testing extends Mailable
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
        $message = $this->maildata;
        //$message = 'test';
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        if (array_key_exists("file",$this->maildata)){
            $this->markdown('emails.dailymail')
                ->attachData($this->maildata['file'], $this->maildata['name'], [
                    'mime' => 'xlsx',
                    'Subject' => 'EOD excel',
                ])
                ->with([
                    'message' => $this->maildata,
                    //'message' => 'test 1',
                ]);
        }else{
            $this->markdown('emails.dailymail')
            ->with([
                'message' => $this->maildata,
                //'message' => 'test 1',
            ]);
        }
    }
}
