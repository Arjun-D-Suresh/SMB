<?php
  
namespace App\Http\Controllers;
  
use Illuminate\Http\Request;
use App\Exports\UsersExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;

//PhpSpreadsheet 
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Xls;

class DownloadexcelController extends Controller
{
  
    /**
    * @return \Illuminate\Support\Collection

    */
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index()
    {
        $data = DB::select(DB::raw("SELECT e.*,(@cnt := @cnt + 1) AS row_num FROM excel_log AS e CROSS JOIN (SELECT @cnt := 0) AS dummy WHERE e.type = 'IEPF2';"));

        return view('uploaded', ["data" => $data]);
    }
    public function export(Request $request) 
    {
        $id = $request->id;
        $name = $request->excel;

        $data= DB::select("SELECT firstname,middlename,lastname,father_firstname,father_middlename,father_lastname,address,country,state,district,pincode,folionumber,accountnumber,investmenttype,amounttransfered,proposeddateoftransfer,cin FROM `iepf2_excel_data` WHERE log_id =$id;");

        // print_r($data);
        // die;
        return Excel::download(new UsersExport($data), $name);
    }

    public function excelCreate($id, $name){

        ini_set('memory_limit', '1024M');
        ini_set('max_execution_time', 300);
        
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $data= DB::select("SELECT firstname,middlename,lastname,father_firstname,father_middlename,father_lastname,address,country,state,district,pincode,folionumber,accountnumber,investmenttype,amounttransfered,proposeddateoftransfer,cin,company_name,no_of_shares FROM `iepf2_excel_data` WHERE log_id =$id;");

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Company Name');
        $sheet->setCellValue('B1', 'No Of Shares');
        $sheet->setCellValue('C1', 'Investor First Name');
        $sheet->setCellValue('D1', 'Investor Middle Name');
        $sheet->setCellValue('E1', 'Investor Last Name');
        $sheet->setCellValue('F1', 'Father/Husband First Name');
        $sheet->setCellValue('G1', 'Father/Husband Middle Name');
        $sheet->setCellValue('H1', 'Father/Husband Last Name');
        $sheet->setCellValue('I1', 'Address');
        $sheet->setCellValue('J1', 'Country');
        $sheet->setCellValue('K1', 'State');
        $sheet->setCellValue('L1', 'District');
        $sheet->setCellValue('M1', 'Pin Code');
        $sheet->setCellValue('N1', 'FOLIO NUMBER');
        $sheet->setCellValue('O1', 'DP Id-Client Id-Account Number');
        $sheet->setCellValue('P1', 'Investment Type');
        $sheet->setCellValue('Q1', 'Amount transferred');
        $sheet->setCellValue('R1', 'Proposed Date of transfer to IEPF(YYYY-MM-DD)');
        $sheet->setCellValue('S1', 'CIN');
        

        $i=2;
        foreach($data as $d){

            $sheet->setCellValue('A'.$i, $d->company_name);
            $sheet->setCellValue('B'.$i, $d->no_of_shares);
            $sheet->setCellValue('C'.$i, $d->firstname);
            $sheet->setCellValue('D'.$i, $d->middlename);
            $sheet->setCellValue('E'.$i, $d->lastname);
            $sheet->setCellValue('F'.$i, $d->father_firstname);
            $sheet->setCellValue('G'.$i, $d->father_middlename);
            $sheet->setCellValue('H'.$i, $d->father_lastname);
            $sheet->setCellValue('I'.$i, $d->address);
            $sheet->setCellValue('J'.$i, $d->country);
            $sheet->setCellValue('K'.$i, $d->state);
            $sheet->setCellValue('L'.$i, $d->district);
            $sheet->setCellValue('M'.$i, $d->pincode);
            $sheet->setCellValue('N'.$i, $d->folionumber);
            $sheet->setCellValue('O'.$i, $d->accountnumber);
            $sheet->setCellValue('P'.$i, $d->investmenttype);
            $sheet->setCellValue('Q'.$i, $d->amounttransfered);
            $sheet->setCellValue('R'.$i, $d->proposeddateoftransfer);
            $sheet->setCellValue('S'.$i, $d->cin);

            $i++;
        }

        

        $writer = new Xlsx($spreadsheet);
        $writer->save(storage_path()."/excel/"."$name");
        $result = array("flag"=>1, "message"=>"Success", "data"=> []);
        return json_encode($result);
    } 
}
