<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

//PhpSpreadsheet 
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Xls;

class eodmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dailymail:eod';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This is for daily EOD update mail';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $mailvalue = DB::select(DB::raw("CALL iepf_eod();"));
        $excelName[] = [];
        // $empty = '<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">' .
        //     '<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>' .
        //     '<style>.table-head {background: #695800;color: #fff;font-family: ' . "'Raleway'" . ', sans-serif;}' .
        //     'table th:first-child {border-radius: 10px 0 0 0;}' .
        //     'table th:last-child {border-radius: 0 10px 0 0;}</style>' . '<h2>EOD Report :</h2>' .
        //     '<table class="table table-striped">' .
        //     '<thead class="table-head" style="font-family: ' . "'Raleway'" . '><tr><th scope="col"></th><th scope="col">Excel name</th><th scope="col">File type</th><th scope="col">Uploaded at</th><th scope="col">Procssed data</th><th scope="col">Status</th></tr></thead>' .
        //     '<tbody style="font-family: ' . "'Raleway'" . ',sans-serif;">';

        // foreach ($mailvalue as $m) {

        //     $empty = $empty . '<tr><td><center>' . $m->excel_name . '</center></td><td><center>' . $m->type . '</center></td><td><center>' . $m->uploadedat .  '</center></td></td><td><center>' . $m->dataprocessed .  '</center></td></td><td><center>' . $m->file_type .  '</center></td></tr>';
        //     array_push($excelName, $m->excel_name);
        // }
        //$empty = $empty . '</tbody></table>';
        $count = count($mailvalue);
        $empty =  '<h2>EOD Report </h2>';
        $maildata = [
            "message" => $empty
        ];


        if ($count >0){
            $empty = $empty.'<p>File count : '. count($mailvalue).'</p> <p style="color:green;">(*attachment added below)</p>' ;

            $name = date("Y-m-d")."_eod.xlsx";
            echo "$name";
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setCellValue('A1', '#');
            $sheet->setCellValue('B1', 'Excel');
            $sheet->setCellValue('C1', 'Type');
            $sheet->setCellValue('D1', 'Uploaded At');
            $sheet->setCellValue('E1', 'Uploaded By');
            $sheet->setCellValue('F1', 'Rows Processed');
            $sheet->setCellValue('G1', 'Message');

            $i=2;
            foreach($mailvalue as $m){
                $sheet->setCellValue('A'.$i, $i-1);
                $sheet->setCellValue('B'.$i, $m->excel_name);
                $sheet->setCellValue('C'.$i, $m->type);
                $sheet->setCellValue('D'.$i, $m->uploadedat);
                $sheet->setCellValue('E'.$i, $m->usertype);
                $sheet->setCellValue('F'.$i, $m->dataprocessed);
                $sheet->setCellValue('G'.$i, $m->file_type);
                $i++;
            }

            $writer = new Xlsx($spreadsheet);
            $writer->save(storage_path()."/excel/"."$name");

            $maildata = [
                "message" => $empty,
                "file"  => file_get_contents(storage_path()."/excel/"."$name"),
                "name" => $name,
            ];
        }else{
            $empty = $empty.'<p style="color:red;">No file uploaded </p>';
            $maildata = [
                "message" => $empty
            ];
        }

       

        // $maildata = [
        //     "message" => $message,
        //     "file"  => Storage::get('public/uploads/processed/IEPF2/' . $name),
        //     "name" => $name,
        // ];
        
        

        $to = "iepftesting@gmail.com";

        Mail::to($to)->send(new \App\Mail\Testing($maildata));

        echo "EOD Mail done :) ";
    }
}
