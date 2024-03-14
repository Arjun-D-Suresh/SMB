<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;


class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->call(function () {
            $mailvalue = DB::select(DB::raw("CALL iepf_eod();"));
            // print_r($mailvalue);
            $maildata = [
                "message" => "hello testing ",

            ];
            // $maildata = [
            //     "message" => $message,
            //     "file"  => Storage::get('public/uploads/processed/IEPF2/' . $name),
            //     "name" => $name,
            // ];
            $to = "dhinakaran@navilsoftwares.com";
            Mail::to($to)->send(new \App\Mail\SendMail($maildata));
        })->everyMinute();
    }
}
// ->dailyAt('18:00')