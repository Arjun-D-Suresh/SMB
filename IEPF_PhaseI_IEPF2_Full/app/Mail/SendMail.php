<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;


use app\Http\Controllers\HomeController;

class SendMail extends Mailable
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
        // print_r($maildata);
        $this->maildata = $maildata;
        $message = $this->maildata['message'];
        $file = $this->maildata['file'];
        $filename = $this->maildata['name'];
        // $file = $reader->load(storage_path('app/public/uploads/processed/IEPF2') . '/' . $name);
        // print_r($message + '55555555' + $filename);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $this->markdown('emails.welcome')
            // ->attach($this->maildata['file']);
            ->attachData($this->maildata['file'], $this->maildata['name'], [
                'mime' => 'xls',
                'Subject' => 'File uploaded',
            ])
            ->with([
                'filename' => $this->maildata['name'],
                'message' => $this->maildata['message'],
            ]);
    }
}
