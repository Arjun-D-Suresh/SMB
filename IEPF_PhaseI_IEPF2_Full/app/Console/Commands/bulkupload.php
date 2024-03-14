<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\UserImport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use app\Http\Controllers\MailController;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use app\Mail\SendMail;
use Carbon\Carbon;
use \Exception;

class bulkupload extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'call:bulkupload';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Call bulk upload through command.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function logMessage(String $msg)
    {
        echo $msg ."\n";
        Log::info($msg);
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {   
        ini_set('memory_limit', '1024M');
        $this->logMessage("------------------------------------- CALL:BULKUPLOAD ---------------------------------------");
        $contentList = Storage::disk('ftp')->files('IEPF2');

        $this->logMessage("All Files => " . implode(" | ", $contentList));
        
        $flag = 0;
        foreach($contentList as $contentListItem) {

            try{
                $name = basename($contentListItem);

                $this->logMessage($name);
                    
                $contents = Storage::disk('ftp')->get('IEPF2/' . $name);
    
                $response = Http::attach('file', $contents, $name)
                                    ->post(env('FLASK_API', '') . 'api/uploader', [
                                        'fileType' => 'IEPF2',
                                    ]);
    
                $is_deleted = Storage::disk('ftp')->delete('IEPF2/' . $name);
    
                $responseBody = json_decode($response->body());
                if($responseBody->message != "success"){
                    $flag = 1;
                    $this->logMessage($name." => ERROR! while uploading file");
                }else{
                    if($responseBody->data[0]->status != "File uploaded successfully"){
                        $flag = 1;
                    }
                    
                    $this->logMessage($name . " => " . $responseBody->data[0]->status);
                }
            } catch(Exception $e) {
                $this->logMessage($e);
            }
            
        }  
        if ($flag == 1) {
            $this->logMessage("Some Files Already Uploaded");
        } else {
            $this->logMessage("All Files uploaded Succesfully");
        }
        $this->logMessage("---------------------------------------------------------------------------------------------");
    }
}