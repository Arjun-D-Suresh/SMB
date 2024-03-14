<?php

namespace App\Http\Controllers;

use App\Mail\welcomemail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Console\Scheduling\Schedule;



class MailController extends Controller
{

    /**
     * Undocumented function
     * @param request $filename
     * @param request $message
     * @return response
     */
    public function sendmail($filename, $message)
    {

        // mail::to('renuka@navilsoftwares.com')->send(new welcomemail());


        // Mail::send('emails.reminder', ['user' => $user], function ($m) use ($user) {
        //     $m->from('hello@app.com', 'Your Application');
        //     return view('welcome');

        //     $m->to($user->email, $user->name)->subject('Your Reminder!');
        // });
    }
    public function eod_mail()
    {
        return DB::select(DB::raw("CALL iepf_eod();"));
    }
    protected function schedule(Schedule $schedule)
    {
        $schedule->call(function () {
            DB::table('recent_users')->delete();
        })->daily();
    }
}
