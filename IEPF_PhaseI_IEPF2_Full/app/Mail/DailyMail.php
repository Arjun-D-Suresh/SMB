<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DailyMail extends Mailable
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
        $processedfile_count = $this->maildata['processedfile_count'];
        $total = $this->maildata['total'];
        $unprocessedfile_count = $this->maildata['unprocessedfile_count'];
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $this->markdown('emails.dailymail')
            ->with([
                'unprocessedfile_count' => $this->maildata['unprocessedfile_count'],
                'processedfile_count' => $this->maildata['processedfile_count'],
                'total' => $this->maildata['total'],

            ]);
    }
}
