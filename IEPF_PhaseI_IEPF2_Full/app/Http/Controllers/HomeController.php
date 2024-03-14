<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\UserImport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use app\Http\Controllers\MailController;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Artisan;
use app\Mail\SendMail;
use Carbon\Carbon;
// use Mail;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */

    public function __construct()
    {
        $this->middleware('auth');
        $this->INBOX_PATH = "C:/IEPF_FTP/Inbox/IEPF2";
    }
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    public function search()
    {
        return view('search');
    }

    public function investorSearch(Request $request)
    {
        $investor_name = empty($request->input('investor_name')) ? "" : $request->input('investor_name');
        $fs_name = empty($request->input('fs_name')) ? "" : $request->input('fs_name');
        $address = empty($request->input('address')) ? "" : $request->input('address');
        $country = empty($request->input('country')) ? "" : $request->input('country');
        $state = empty($request->input('state')) ? "" : $request->input('state');
        $district = empty($request->input('district')) ? "" : $request->input('district');
        $pincode = empty($request->input('pincode')) ? "" : $request->input('pincode');
        // $dob = empty($request->input('dob')) ? "" : $request->input('dob');
        $year = empty($request->input('year')) ? "" : $request->input('year');
        // $cin = empty($request->input('cin')) ? "" : $request->input('cin');
        // $pan = empty($request->input('pan')) ? "" : $request->input('pan');
        // $aadhaar = empty($request->input('aadhaar')) ? "" : $request->input('aadhaar');
        $min_share_value = empty($request->input('min_share_value')) ? "" : $request->input('min_share_value');
        $max_share_value = empty($request->input('max_share_value')) ? "" : $request->input('max_share_value');
        $skip = $request->input('skip');
        $take = $request->input('take');

        $query = 'SELECT (@row_number:=@row_number + 1) AS row_num, tbl.* FROM(
                    SELECT distinct
                        fh.folio_number,
                        fh.urn,
                        fh.investor_name,
                        fh.fs_name,
                        fh.address,
                        fh.country,
                        fh.state,
                        fh.district,
                        fh.pincode,
                        DATE(fh.dob) as dob,
                        YEAR(fh.createdat) as year,
                        fh.cin,
                        (select c_fullname from company_master cm where cm.cin=fh.cin limit 1) as CompanyName,
                        fh.pan,
                        fh.aadhaar,
                        fd.dividend_amount as share_value
                    FROM folioheader fh
                    JOIN folio_dividend fd ON fh.urn = fd.urn_key 
                ) as tbl, (SELECT @row_number:=0) as r WHERE';

                if ($investor_name != '') {
                    // $query .= ' REPLACE(investor_name, " ", "") LIKE "%' . $investor_name . '%" AND';
                    $query .= ' investor_name LIKE "%' . $investor_name . '%" AND';
                }
                if ($fs_name != '') {
                    // $query .= ' REPLACE(fs_name, " ", "") LIKE "%' . $fs_name . '%" AND';
                    $query .= ' fs_name LIKE "%' . $fs_name . '%" AND';
                }
                if ($address != '') {
                    // $query .= ' REPLACE(address, " ", "") LIKE "%' . $address . '%" AND';
                    $query .= ' address LIKE "%' . $address . '%" AND';
                }
                if ($country != '') {
                    // $query .= ' REPLACE(country, " ", "") LIKE "%' . $country . '%" AND';
                    $query .= ' country LIKE "%' . $country . '%" AND';
                }
                if ($state != '') {
                    // $query .= ' REPLACE(state, " ", "") LIKE "%' . $state . '%" AND';
                    $query .= ' state LIKE "%' . $state . '%" AND';
                }
                if ($district != '') {
                    // $query .= ' REPLACE(district, " ", "") LIKE "%' . $district . '%" AND';
                    $query .= ' district LIKE "%' . $district . '%" AND';
                }
                if ($pincode != '') {
                    // $query .= ' REPLACE(pincode, " ", "") LIKE "%' . $pincode . '%" AND';
                    $query .= ' pincode LIKE "%' . $pincode . '%" AND';
                }
        // if ($dob != '') {
        //     $query .= ' dob = "' . $dob . '" AND';
        // }
        if ($year != '') {
            $query .= ' year = "' . $year . '" AND';
        }
        // if ($cin != '') {
        //     $query .= ' REPLACE(cin, " ", "") LIKE "%' . $cin . '%" AND';
        // }
        // if ($pan != '') {
        //     $query .= ' REPLACE(pan, " ", "") LIKE "%' . $pan . '%" AND';
        // }
        // if ($aadhaar != '') {
        //     $query .= ' REPLACE(aadhaar, " ", "") LIKE "%' . $aadhaar . '%" AND';
        // }
        if ($min_share_value != '' && $max_share_value != '') {
            $query .= ' (share_value >= ' . $min_share_value . ' AND share_value <= ' . $max_share_value . ') AND';
        }


        $query .= ' true LIMIT ' . $skip . ',' . $take . ';';

        // print_r($query);
        // die;
        // dd($query);

        $data = DB::select(DB::raw($query));
        $data = array("data" => $data);


        echo json_encode($data);
    }
    
    public function investorSearch_group_dividendAmount(Request $request)
    {
        $investor_name = empty($request->input('investor_name')) ? "" : $request->input('investor_name');
        $fs_name = empty($request->input('fs_name')) ? "" : $request->input('fs_name');
        $address = empty($request->input('address')) ? "" : $request->input('address');
        $country = empty($request->input('country')) ? "" : $request->input('country');
        $state = empty($request->input('state')) ? "" : $request->input('state');
        $district = empty($request->input('district')) ? "" : $request->input('district');
        $pincode = empty($request->input('pincode')) ? "" : $request->input('pincode');
        // $dob = empty($request->input('dob')) ? "" : $request->input('dob');
        $year = empty($request->input('year')) ? "" : $request->input('year');
        // $cin = empty($request->input('cin')) ? "" : $request->input('cin');
        // $pan = empty($request->input('pan')) ? "" : $request->input('pan');
        // $aadhaar = empty($request->input('aadhaar')) ? "" : $request->input('aadhaar');
        $min_share_value = empty($request->input('min_share_value')) ? "" : $request->input('min_share_value');
        $max_share_value = empty($request->input('max_share_value')) ? "" : $request->input('max_share_value');
        $skip = $request->input('skip');
        $take = $request->input('take');

        $query = 'SELECT (@row_number:=@row_number + 1) AS row_num, tbl.* FROM(
                    SELECT
                        fh.folio_number,
                        fh.urn,
                        fh.investor_name,
                        fh.fs_name,
                        fh.address,
                        fh.country,
                        fh.state,
                        fh.district,
                        fh.pincode,
                        DATE(fh.dob) as dob,
                        YEAR(fh.createdat) as year,
                        fh.cin,
                        fh.pan,
                        fh.aadhaar,
                        fd.share_value
                    FROM folioheader fh
                    JOIN (
                            SELECT 
                            fd.urn_key, 
                            SUM(fd.dividend_amount) as share_value 
                            FROM folio_dividend fd 
                            GROUP BY fd.urn_key
                        ) fd ON fh.urn = fd.urn_key
                ) as tbl, (SELECT @row_number:=0) as r WHERE';

        if ($investor_name != '') {
            $query .= ' REPLACE(investor_name, " ", "") LIKE "%' . $investor_name . '%" AND';
        }
        if ($fs_name != '') {
            $query .= ' REPLACE(fs_name, " ", "") LIKE "%' . $fs_name . '%" AND';
        }
        if ($address != '') {
            $query .= ' REPLACE(address, " ", "") LIKE "%' . $address . '%" AND';
        }
        if ($country != '') {
            $query .= ' REPLACE(country, " ", "") LIKE "%' . $country . '%" AND';
        }
        if ($state != '') {
            $query .= ' REPLACE(state, " ", "") LIKE "%' . $state . '%" AND';
        }
        if ($district != '') {
            $query .= ' REPLACE(district, " ", "") LIKE "%' . $district . '%" AND';
        }
        if ($pincode != '') {
            $query .= ' REPLACE(pincode, " ", "") LIKE "%' . $pincode . '%" AND';
        }
        // if ($dob != '') {
        //     $query .= ' dob = "' . $dob . '" AND';
        // }
        if ($year != '') {
            $query .= ' year = "' . $year . '" AND';
        }
        // if ($cin != '') {
        //     $query .= ' REPLACE(cin, " ", "") LIKE "%' . $cin . '%" AND';
        // }
        // if ($pan != '') {
        //     $query .= ' REPLACE(pan, " ", "") LIKE "%' . $pan . '%" AND';
        // }
        // if ($aadhaar != '') {
        //     $query .= ' REPLACE(aadhaar, " ", "") LIKE "%' . $aadhaar . '%" AND';
        // }
        if ($min_share_value != '' && $max_share_value != '') {
            $query .= ' (share_value >= ' . $min_share_value . ' AND share_value <= ' . $max_share_value . ') AND';
        }


        $query .= ' true LIMIT ' . $skip . ',' . $take . ';';

        // print_r($query);
        // die;
        // dd($query);

        $data = DB::select(DB::raw($query));
        $data = array("data" => $data);


        echo json_encode($data);
    }

    public function import()
    {
        ini_set('memory_limit', '1024M');
        set_time_limit(300);
        $newfile = request()->file('file');
        // $fileType = request()->input('fileType');
        if ($newfile == null) {
            $message = "Please select file first!";
            return redirect()->back()->with('errmessage', $message);
        }
        $name = $newfile->getClientOriginalName();
        $size = request()->file->getsize();



        if ($size > 0) { //3000000
            $size = number_format($size / 1048576, 2);
            $arr_file = explode('.', $name);
            $extension = end($arr_file);
            $selected = $_POST['fileType'];
           
            $manual = auth()->user()->name;

            if ('csv' == $extension) {
            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
        } else if ('xls' == $extension) {
            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xls();
        } else if ('xlsx' == $extension) {
            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        } 

            if ($selected == 'IEPF2') {
                dd('IEPF2');

                if (Storage::exists('app/public/uploads/Inbox/IEPF2/' . $name)) {
                    $message = "File already uploaded.";
                    return redirect()->back()->with('errmessage', $message);
                } else {

                    $contents = fopen(request()->file('file'), 'r');

                    $response = Http::attach('file', $contents, $name)
                        ->post(env('FLASK_API', '') . 'api/uploader', [
                            'fileType' => $_POST['fileType'],
                        ]);
                    $responseBody = json_decode($response->body());

                    if ($responseBody->message == "success") {
                        $message = $responseBody->data[0]->status;
                        return redirect()->back()->with('message', $message);
                    } else {
                        $message = "Uploaded file is large. Please, Check mail for update.";
                        request()->file('file')->move(storage_path('app/public/uploads/Inbox/IEPF2/'), $name);
                        return redirect()->back()->with('message', $message);
                    }

                    // if($responseBody->message == "success"){
                    //     $message = $responseBody->data[0]->status;
                    //     if($message == "File contains multiple dividend rows" || $message == "File contains only single dividend rows"){
                    //         return redirect()->back()->with('message', $message);
                    //     }else{ 
                    //         return redirect()->back()->with('errmessage', $message);
                    //     }
                    // }else{
                    //     $message = "Uploaded file is large. Please, Check mail for update.";
                    //     request()->file('file')->move(storage_path('app/public/uploads/Inbox/IEPF2/'), $name);
                    //     return redirect()->back()->with('message', $message);
                    // }


                }
            } else if ($selected == 'BONUS' || $selected == 'STOCK' || $selected == 'DIVIDEND') {
                // dd('DIVIDEND');
                $selected = $selected;
                $db = DB::select(DB::raw('CALL Store_excellog("' . $name . '","' . $selected . '","' . $manual . '")'));
                $process = 0;

                if ($db[0]->result == 'Excel log uploaded') {
                    $spreadsheet = $reader->load(request()->file('file'));
                    $worksheet = $spreadsheet->getActiveSheet();
                    $rows = $worksheet->toArray();

                    $Sheet1 = [];
                    $i = 0;
                    foreach ($rows as $row) {
                        // if ($i == 1) {
                        //     // $cin = $row[1];
                        //     // $company = $row[6];
                        // }
                        if ($process == 1) {
                            // if ($i >= 14) {
                            // $time = strtotime($row[15]);
                            $Sheet1[] = [

                                "Security Code" => str_replace("'", "", trim(preg_replace('/\s\s+/', '', $row[0]))),
                                "Security Name" => str_replace("'", "", trim(preg_replace('/\s\s+/', '', $row[1]))),
                                "Company Name" => str_replace("'", "", trim(preg_replace('/\s\s+/', '', $row[2]))),
                                "Ex Date" => str_replace(" ", "-", str_replace("'", "", trim(preg_replace('/\s\s+/', '', $row[3])))),
                                "Purpose" => str_replace("'", "", trim(preg_replace('/\s\s+/', '', $row[4]))),
                                "Record Date" => str_replace(" ", "-", str_replace("'", "", trim(preg_replace('/\s\s+/', '', $row[5])))),
                                "BC Start Date" => str_replace(" ", "-", str_replace("'", "", trim(preg_replace('/\s\s+/', '', $row[6])))),
                                "BC End Date" => str_replace(" ", "-", str_replace("'", "", trim(preg_replace('/\s\s+/', '', $row[7])))),
                                "ND Start Date" => str_replace(" ", "-", str_replace("'", "", trim(preg_replace('/\s\s+/', '', $row[8])))),
                                "ND End Date" => str_replace(" ", "-", str_replace("'", "", trim(preg_replace('/\s\s+/', '', $row[9])))),
                                "Actual Payment Date" => str_replace(" ", "-", str_replace("'", "", trim(preg_replace('/\s\s+/', '', $row[10])))),
                                "Dividend" => $row[11] == '' ? 0 : str_replace("'", "", $row[11]),
                                "Cin No" => $row[12] == '' ? 0 : preg_replace("/[^a-z_\-0-9]/i", '', str_replace("'", "", $row[12])),
                                // "Amount transferred" => str_replace(",", "", str_replace("'", "", $row[14])),
                                // "Proposed Date of transfer to IEPF(DD-MON-YYYY)" => $row[15], //date('d-m-Y',$time),
                                // "Column17" => null,
                                // "Column18" => $cin,
                                // "IEPF-1 check" => $cin,
                                // "CIN" => $cin,
                                "Check" => true
                            ];
                        }

                        if ($row[0] == "Security Code" && $row[5] = "Purpose") {
                            $process = 1;
                            echo "header";
                        }

                        $i++;
                    }
                    ini_set('memory_limit', '1024M');
                    $data = (object) array('Sheet1' => $Sheet1);

                    dd(json_encode($data));
                    

                    $db2 = DB::select(DB::raw('CALL Corporate_Actions(\'' . json_encode($data) . '\', "' . $name . '","' . $selected . '")'));

                    // print_r('CALL Corporate_Actions(\'' . json_encode($data) . '\', "' . $name . '","' . $selected . '")');
                    // die;
                    $message = $db2[0]->result;
                    // print_r($message);

                    if ($message == 'Data inserted sucessfully') {
                        request()->file('file')->move(storage_path('app/public/uploads/processed/CORPORATE'), $name);
                        $maildata = [
                            "message" => $message,
                            "file"  => Storage::get('public/uploads/processed/CORPORATE/' . $name),
                            "name" => $name,
                        ];
                        $to = "iepftesting@gmail.com";

                        // Send Mail
                        // Mail::to($to)->send(new \App\Mail\SendMail($maildata));

                        return redirect()->back()->with('message', $message);
                    } else {
                        return redirect()->back()->with('errmessage', $message);
                    }
                } else {
                    $message = "File already processed";
                    request()->file('file')->move(storage_path('app/public/uploads/ErrorFiles/CORPORATE'), $name);
                    $maildata = [
                        "message" => $message,
                        "file"  => Storage::get('public/uploads/ErrorFiles/CORPORATE/' . $name),
                        "name" => $name,
                    ];
                    $to = "iepftesting@gmail.com";

                    // Send Mail
                    // Mail::to($to)->send(new \App\Mail\SendMail($maildata));

                    return redirect()->back()->with('errmessage', $message);
                }
            } else {
                dd('Wrong');

                $message = "Wrong file format!";
                return redirect()->back()->with('errmessage', $message);
            }
        }



        // if (((stripos($name,"IEPF 2") === false) && (stripos($name,"IEPF-2") === false) && (stripos($name,"IEPF2") === false)) || strval($selected) !== "IEPF2"){
        //     if(strval($selected) !== "IEPF2"){
        //         $message = "Only IEPF2 fearures are availabe. ";
        //         return redirect()->back()->with('errmessage', $message);
        //     }
        //     $message = "Selected file mismatch with uploaded file. ";
        //     return redirect()->back()->with('errmessage', $message);
        // }

        

        // if ($selected == 'IEPF2' || $selected == 'IEPF1') {
        //     $db = DB::select(DB::raw('CALL Store_excellog("' . $name . '","' . $selected . '","' . $manual . '")'));
        //     $process = 0;

        //     if ($db[0]->result == 'Excel log uploaded') {
        //         // $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        //         // $reader->setLoadSheetsOnly(["Sheet 2", "Investor Details"]);
        //         $spreadsheet = $reader->load(request()->file('file'));
        //         $worksheet = $spreadsheet->getActiveSheet();
        //         $rows = $worksheet->toArray();
        //         // $rows = array_filter($worksheet->toArray(),function ($var){return count(array_unique($var)) > 4;});

        //         $Sheet1 = [];
        //         $i = 0;
        //         foreach ($rows as $row) {

        //             if ($i == 1) {
        //                 $cin = $row[1];
        //                 $company = $row[6];
        //             }
        //             if ($process == 1) {
        //                 $time = strtotime($row[15]);
        //                 $Sheet1[] = [
        //                     "Investor First Name" => str_replace("'", "", str_replace("\\", "\\/", $row[0])),
        //                     "Investor Middle Name" => str_replace("'", "", str_replace("\\", "\\/", $row[1])),
        //                     "Investor Last Name" => str_replace("'", "", str_replace("\\", "\\/", $row[2])),
        //                     "Father\/Husband First Name" => str_replace("'", "", str_replace("\\", "\\/", $row[3])),
        //                     "Father\/Husband Middle Name" => str_replace("'", "", str_replace("\\", "\\/", $row[4])),
        //                     "Father\/Husband Last Name" => str_replace("\\", "\\/", $row[5]),
        //                     "Address" => str_replace("'", "", str_replace("\\", "\\/", str_replace('"', '', $row[6]))),
        //                     "Country" => str_replace("'", "", str_replace("\\", "\\/", $row[7])),
        //                     "State" => str_replace("'", "", str_replace("\\", "\\/", $row[8])),
        //                     "District" => str_replace("'", "", str_replace("\\", "\\/", $row[9])),
        //                     "Pin Code" => str_replace("'", "", str_replace("\\", "\\/", $row[10])),
        //                     "FOLIO NUMBER" => $row[11] == '' ? '' : str_replace("'", "", $row[11]),
        //                     "DP Id-Client Id-Account Number" => str_replace("'", "", $row[12]),
        //                     "Investment Type" => str_replace("'", "", str_replace("\\", "\\/", $row[13])),
        //                     "Amount transferred" => str_replace(",", "", str_replace("'", "", $row[14])),
        //                     "Proposed Date of transfer to IEPF(DD-MON-YYYY)" => str_replace("\\", "\\/", $row[15]), //date('d-m-Y',$time),
        //                     "CIN" => $cin,
        //                     "PAN" => (count($row) <= 17) ? null : str_replace("'", "", $row[16]),
        //                     "Date of Birth" => (count($row) <= 18) ? null : str_replace("'", "", $row[17]),
        //                     "Aadhar Number" => (count($row) <= 19) ? null : str_replace("'", "", $row[18]),
        //                     "Nominee Name   " => (count($row) <= 20) ? null : str_replace("'", "", $row[19]),
        //                     "Joint Holder Name" => (count($row) <= 21) ? null : str_replace("'", "", $row[20]),
        //                     // "Remarks" => str_replace("'", "", $row[21]),
        //                     // "Is the Investment(amount \/ shares)under any litigation" => str_replace("'", "", $row[22]),
        //                     // "Is the shares transfer from unpaid suspence amount(Yes\/No)" => str_replace("'", "", $row[22]),
        //                     "Financial Year" => (count($row) <= 25) ? null : str_replace("'", "", $row[24]),
        //                     // "Column17" => null,
        //                     // "Column18" => $cin,
        //                     // "IEPF-1 check" => $cin,
        //                     "Check" => true
        //                 ];
        //             }
        //             if ($row[0] == "Investor First Name" && $row[11] = "Folio Number") {
        //                 $process = 1;
        //             }

        //             $i++;
        //         }
        //         ini_set('memory_limit', '1024M');
        //         $data = (object) array('Sheet1' => $Sheet1);

        //         $db2 = DB::select(DB::raw('CALL STORE_JSON(\'' . json_encode($data) . '\', "' . $name . '",1)'));
        //         $message = $db2[0]->result;
        //         if ($message == 'No multiple dividend data available') {
        //             request()->file('file')->move(storage_path('app/public/uploads/processed/IEPF2'), $name);
        //             $maildata = [
        //                 "message" => $message,
        //                 "file"  => Storage::get('public/uploads/processed/IEPF2/' . $name),
        //                 "name" => $name,
        //             ];
        //             $to = "iepftesting@gmail.com";

        //             // Send Mail
        //             // Mail::to($to)->send(new \App\Mail\SendMail($maildata));
        //             return redirect()->back()->with('errmessage', $message);
        //         } else {
        //             request()->file('file')->move(storage_path('app/public/uploads/processed/IEPF2'), $name);
        //             $maildata = [
        //                 "message" => $message,
        //                 "file"  => Storage::get('public/uploads/processed/IEPF2/' . $name),
        //                 "name" => $name,
        //             ];
        //             $to = "iepftesting@gmail.com";

        //             // Send Mail
        //             // Mail::to($to)->send(new \App\Mail\SendMail($maildata));
        //             return redirect()->back()->with('message', $message);
        //         }
        //     } else {
        //         $message = "File already uploaded";
        //         request()->file('file')->move(storage_path('app/public/uploads/ErrorFiles/IEPF2'), $name);
        //         $maildata = [
        //             "message" => $message,
        //             "file"  => Storage::get('public/uploads/ErrorFiles/IEPF2/' . $name),
        //             "name" => $name,
        //         ];
        //         $to = "iepftesting@gmail.com";

        //         // Send Mail
        //         // Mail::to($to)->send(new \App\Mail\SendMail($maildata));

        //         return redirect()->back()->with('errmessage', $message);
        //     }
        // }
        //  else if ($selected == 'DIVIDEND') {
        //     // print_r('cominggg DIVIDEND');
        //     $db = DB::select(DB::raw('CALL Store_excellog("' . $name . '","' . $selected . '","' . $manual . '")'));
        //     $process = 0;
        //     // print_r($db[0]->result);


        //     if ($db[0]->result == 'Excel log uploaded') {
        //         $spreadsheet = $reader->load(request()->file('file'));
        //         $worksheet = $spreadsheet->getActiveSheet();
        //         $rows = $worksheet->toArray();
        //         // print_r($rows);
        //         // print_r("npoooo");

        //         $Sheet1 = [];
        //         $i = 0;
        //         foreach ($rows as $row) {
        //             // if ($i == 1) {
        //             //     // $cin = $row[1];
        //             //     // $company = $row[6];
        //             // }
        //             if ($process == 1) {
        //                 // if ($i >= 14) {
        //                 // $time = strtotime($row[15]);
        //                 $Sheet1[] = [
        //                     "Cin No" => str_replace("'", "", str_replace("\\","\\/",$row[0])),
        //                     "Year" => str_replace("'", "", str_replace("\\","\\/",$row[1])),
        //                     "Security Code" => str_replace("'", "", str_replace("\\","\\/",$row[2])),
        //                     "Security Name" => str_replace("'", "", str_replace("\\","\\/",$row[3])),
        //                     "Company Name" => str_replace("'", "", str_replace("\\","\\/",$row[4])),
        //                     "Ex Date" => str_replace("\\","\\/",$row[5]),
        //                     "Purpose" => str_replace("'", "", str_replace("\\","\\/",$row[6])),
        //                     "Dividend" => $row[7] == '' ? 0 : str_replace("'", "", str_replace("\\","\\/",$row[7])),
        //                     "Record Date" => str_replace("'", "", str_replace("\\","\\/",$row[8])),
        //                     "BC Start Date" => str_replace("'", "", str_replace("\\","\\/",$row[9])),
        //                     "BC End Date" => str_replace("'", "", str_replace("\\","\\/",$row[10])),
        //                     "ND Start Date" => str_replace("'", "", str_replace("\\","\\/",$row[11])),
        //                     "ND End Date" => str_replace("'", "", str_replace("\\","\\/",$row[12])),
        //                     "Actual Payment Date" => str_replace("'", "", str_replace("\\","\\/",$row[13])),
        //                     // "Amount transferred" => str_replace(",", "", str_replace("'", "", $row[14])),
        //                     // "Proposed Date of transfer to IEPF(DD-MON-YYYY)" => $row[15], //date('d-m-Y',$time),
        //                     // "Column17" => null,
        //                     // "Column18" => $cin,
        //                     // "IEPF-1 check" => $cin,
        //                     // "CIN" => $cin,
        //                     "Check" => true
        //                 ];
        //             }
        //             if ($row[0] == "Cin No" && $row[6] = "Purpose") {
        //                 $process = 1;
        //                 echo "header";
        //             }

        //             $i++;
        //         }
        //         ini_set('memory_limit', '1024M');
        //         $data = (object) array('Sheet1' => $Sheet1);
        //         // print_r(json_encode($data));

        //         $db2 = DB::select(DB::raw('CALL store_dividend(\'' . json_encode($data) . '\', "' . $name . '")'));
        //         $message = $db2[0]->result;
        //         // print_r($message);
        //         if ($message == 'Data inserted sucessfully') {
        //             request()->file('file')->move(storage_path('app/public/uploads/processed/DIVIDEND'), $name);
        //             $maildata = [
        //                 "message" => $message,
        //                 "file"  => Storage::get('public/uploads/processed/DIVIDEND/' . $name),
        //                 "name" => $name,
        //             ];
        //             $to = "iepftesting@gmail.com";


        //             Mail::to($to)->send(new \App\Mail\SendMail($maildata));


        //             return redirect()->back()->with('message', $message);
        //         } else {
        //             return redirect()->back()->with('errmessage', $message);
        //         }
        //     } else {
        //         $message = "File already uploaded";
        //         request()->file('file')->move(storage_path('app/public/uploads/ErrorFiles/DIVIDEND'), $name);
        //         $maildata = [
        //             "message" => $message,
        //             "file"  => Storage::get('public/uploads/ErrorFiles/DIVIDEND/' . $name),
        //             "name" => $name,
        //         ];
        //         $to = "iepftesting@gmail.com";

        //         Mail::to($to)->send(new \App\Mail\SendMail($maildata));

        //         return redirect()->back()->with('errmessage', $message);
        //     }
        // }
        // else if ($selected == 'BONUS' || $selected == 'STOCK' || $selected == 'DIVIDEND') {

        //     $selected = $selected;
        //     $db = DB::select(DB::raw('CALL Store_excellog("' . $name . '","' . $selected . '","' . $manual . '")'));
        //     $process = 0;

        //     if ($db[0]->result == 'Excel log uploaded') {
        //         $spreadsheet = $reader->load(request()->file('file'));
        //         $worksheet = $spreadsheet->getActiveSheet();
        //         $rows = $worksheet->toArray();

        //         $Sheet1 = [];
        //         $i = 0;
        //         foreach ($rows as $row) {
        //             // if ($i == 1) {
        //             //     // $cin = $row[1];
        //             //     // $company = $row[6];
        //             // }
        //             if ($process == 1) {
        //                 // if ($i >= 14) {
        //                 // $time = strtotime($row[15]);
        //                 $Sheet1[] = [

        //                     "Security Code" => str_replace("'", "", trim(preg_replace('/\s\s+/', '', $row[0]))),
        //                     "Security Name" => str_replace("'", "", trim(preg_replace('/\s\s+/', '', $row[1]))),
        //                     "Company Name" => str_replace("'", "", trim(preg_replace('/\s\s+/', '', $row[2]))),
        //                     "Ex Date" => str_replace(" ", "-", str_replace("'", "", trim(preg_replace('/\s\s+/', '', $row[3])))),
        //                     "Purpose" => str_replace("'", "", trim(preg_replace('/\s\s+/', '', $row[4]))),
        //                     "Record Date" => str_replace(" ", "-", str_replace("'", "", trim(preg_replace('/\s\s+/', '', $row[5])))),
        //                     "BC Start Date" => str_replace(" ", "-", str_replace("'", "", trim(preg_replace('/\s\s+/', '', $row[6])))),
        //                     "BC End Date" => str_replace(" ", "-", str_replace("'", "", trim(preg_replace('/\s\s+/', '', $row[7])))),
        //                     "ND Start Date" => str_replace(" ", "-", str_replace("'", "", trim(preg_replace('/\s\s+/', '', $row[8])))),
        //                     "ND End Date" => str_replace(" ", "-", str_replace("'", "", trim(preg_replace('/\s\s+/', '', $row[9])))),
        //                     "Actual Payment Date" => str_replace(" ", "-", str_replace("'", "", trim(preg_replace('/\s\s+/', '', $row[10])))),
        //                     "Dividend" => $row[11] == '' ? 0 : str_replace("'", "", $row[11]),
        //                     "Cin No" => $row[12] == '' ? 0 : preg_replace("/[^a-z_\-0-9]/i", '', str_replace("'", "", $row[12])),
        //                     // "Amount transferred" => str_replace(",", "", str_replace("'", "", $row[14])),
        //                     // "Proposed Date of transfer to IEPF(DD-MON-YYYY)" => $row[15], //date('d-m-Y',$time),
        //                     // "Column17" => null,
        //                     // "Column18" => $cin,
        //                     // "IEPF-1 check" => $cin,
        //                     // "CIN" => $cin,
        //                     "Check" => true
        //                 ];
        //             }
        //             if ($row[0] == "Security Code" && $row[5] = "Purpose") {
        //                 $process = 1;
        //                 echo "header";
        //             }

        //             $i++;
        //         }
        //         ini_set('memory_limit', '1024M');
        //         $data = (object) array('Sheet1' => $Sheet1);

        //         // print_r(json_encode($data));
        //         // die;

        //         $db2 = DB::select(DB::raw('CALL Corporate_Actions(\'' . json_encode($data) . '\', "' . $name . '","' . $selected . '")'));

        //         // print_r('CALL Corporate_Actions(\'' . json_encode($data) . '\', "' . $name . '","' . $selected . '")');
        //         // die;
        //         $message = $db2[0]->result;
        //         // print_r($message);

        //         if ($message == 'Data inserted sucessfully') {
        //             request()->file('file')->move(storage_path('app/public/uploads/processed/CORPORATE'), $name);
        //             $maildata = [
        //                 "message" => $message,
        //                 "file"  => Storage::get('public/uploads/processed/CORPORATE/' . $name),
        //                 "name" => $name,
        //             ];
        //             $to = "iepftesting@gmail.com";

        //             // Send Mail
        //             // Mail::to($to)->send(new \App\Mail\SendMail($maildata));

        //             return redirect()->back()->with('message', $message);
        //         } else {
        //             return redirect()->back()->with('errmessage', $message);
        //         }
        //     } else {
        //         $message = "File already processed";
        //         request()->file('file')->move(storage_path('app/public/uploads/ErrorFiles/CORPORATE'), $name);
        //         $maildata = [
        //             "message" => $message,
        //             "file"  => Storage::get('public/uploads/ErrorFiles/CORPORATE/' . $name),
        //             "name" => $name,
        //         ];
        //         $to = "iepftesting@gmail.com";

        //         // Send Mail
        //         // Mail::to($to)->send(new \App\Mail\SendMail($maildata));

        //         return redirect()->back()->with('errmessage', $message);
        //     }
        // } else if ($selected == 'COMPANY') {
        //     $selected = $selected;
        //     $db = DB::select(DB::raw('CALL Store_excellog("' . $name . '","' . $selected . '","' . $manual . '")'));
        //     $process = 0;

        //     if ($db[0]->result == 'Excel log uploaded') {
        //         $spreadsheet = $reader->load(request()->file('file'));
        //         $worksheet = $spreadsheet->getActiveSheet();
        //         $rows = $worksheet->toArray();

        //         $Sheet1 = [];
        //         $i = 0;
        //         foreach ($rows as $row) {
        //             if ($process == 1) {
        //                 $Sheet1[] = [

        //                     "BSE Security Code" => str_replace("'", "", trim(preg_replace('/\s\s+/', '', $row[0]))),
        //                     "Cin No" => str_replace("'", "", trim(preg_replace('/\s\s+/', '', $row[1]))),
        //                     "NSE Security Id" => str_replace("'", "", trim(preg_replace('/\s\s+/', '', $row[2]))),
        //                     "Security Name" => str_replace("'", "", trim(preg_replace('/\s\s+/', '', $row[3]))),
        //                     "Status" => str_replace("'", "", trim(preg_replace('/\s\s+/', '', $row[4]))),
        //                     "Face Value" => str_replace("'", "", trim(preg_replace('/\s\s+/', '', $row[5]))),
        //                     "ISIN No" => str_replace("'", "", trim(preg_replace('/\s\s+/', '', $row[6]))),
        //                 ];
        //             }
        //             if ($row[0] == "BSE Security Code" && $row[1] = "Cin No") {
        //                 $process = 1;
        //             }

        //             $i++;
        //         }
        //         ini_set('memory_limit', '1024M');
        //         $data = (object) array('Sheet1' => $Sheet1);

        //         $db2 = DB::select(DB::raw('CALL store_company(\'' . json_encode($data) . '\', "' . $name . '")'));

        //         $message = $db2[0]->result;

        //         if ($message == 'Data inserted sucessfully') {
        //             request()->file('file')->move(storage_path('app/public/uploads/processed/CORPORATE'), $name);
        //             $maildata = [
        //                 "message" => $message,
        //                 "file"  => Storage::get('public/uploads/processed/CORPORATE/' . $name),
        //                 "name" => $name,
        //             ];
        //             $to = "iepftesting@gmail.com";

        //             // Send Mail
        //             // Mail::to($to)->send(new \App\Mail\SendMail($maildata));

        //             return redirect()->back()->with('message', $message);
        //         } else {
        //             return redirect()->back()->with('errmessage', $message);
        //         }
        //     } else {
        //         $message = "File already processed";
        //         request()->file('file')->move(storage_path('app/public/uploads/ErrorFiles/CORPORATE'), $name);
        //         $maildata = [
        //             "message" => $message,
        //             "file"  => Storage::get('public/uploads/ErrorFiles/CORPORATE/' . $name),
        //             "name" => $name,
        //         ];
        //         $to = "iepftesting@gmail.com";


        //         // Send Mail
        //         // Mail::to($to)->send(new \App\Mail\SendMail($maildata));

        //         return redirect()->back()->with('errmessage', $message);
        //     }
        // }
        // else if($selected == 'IEPF7'){


        // }
        // else{

        // }
    }

    public function storedata()
    {
        set_time_limit(300);
        $DM_id = $_POST['data1'];
        $EM_id = $_POST['data2'];
        $companyname = $_POST['comapanyname'];
        $dividendamount = $_POST['dividendamount'];
        $id = explode("/", $DM_id)[0];

        $response = Http::post(env('FLASK_API', '') . 'api/multiple-dividend', [
            'dividend_id' => $id,
            'iepf_row_ids' => $EM_id,
        ]);

        return json_decode($response->body());

        // dd($id, $EM_id);

        // $db = DB::select(DB::raw("CALL update_selected($id,'$EM_id');"));

        // return json_encode($db);
        // $message = $db[0]->result;
        // $maildata = [
        //     "dividentamount" => $dividendamount,
        //     // "file"  => Storage::get('public/uploads/processed/IEPF2/' . $name),
        //     "companyname" => $companyname,
        // ];
        // $to = "iepftesting@gmail.com";

        // Mail::to($to)->send(new \App\Mail\processfile($maildata));
    }

    public function processFTPfiles()
    {
        $exitCode = Artisan::call('call:bulkupload', []);
        return $exitCode;
    }

    public function allfiles()
    {
        // // $files = Storage::disk('localFTP')->files($this->INBOX_PATH); // '/public/uploads/Inbox/IEPF2'
        // // $files = Storage::disk('ftp')->files();
        // // $files = Storage::allDirectories('C:\IEPF_FTP\Inbox');

        // $ftp_server =   '202.0.103.63';//Replace with your IP
        // $conn_id    =    ftp_connect($ftp_server);

        // # login with username and password
        // $user   =   'IepfUser'; //Replace with your FTP User name
        // $passwd =   'IEPF@123'; //Replace with your FTP Password
        // $login_result = ftp_login($conn_id, $user, $passwd);

        // # check connection
        // if (!$conn_id)
        // {
        //     // print_r("FTP connection has failed!");
        //     // print_r("Attempted to connect to $ftp_server for user $user");
        //     // die;
        //     $custommessage = "FTP connection has failed!";
        //     return redirect('/home')->with('errmessage', $custommessage);
        // }
        // else{
        //     $cwd            =   ftp_pwd($conn_id);
        //     $contentList    =   ftp_nlist($conn_id, 'Inbox/IEPF2'); // This denotes the current path, you can replace this with your actual path

        //     $custommessage = "No files exist ";

        //     if (count($contentList) < 1) {
        //         return redirect('/home')->with('errmessage', $custommessage);
        //     }

        //     // $files = preg_replace("/public/i", "", $files);

        //     $flag = 0;

        //     foreach($contentList as $contentListItem)
        //     {
        //         if (ftp_size($conn_id, $contentListItem) == -1)
        //         {
        //             #Its a direcotry
        //             // print_r("Directory : ".$contentListItem."<br>");
        //         }
        //         else
        //         {
        //             #Its a file
        //             // print_r("File : ".$contentListItem."<br>");
        //             $process = 0;
        //             $name = basename($contentListItem);

        //             $selected = 'IEPF2';
        //             $manual = 'manual';
        //             $db = DB::select(DB::raw('CALL Store_excellog("' . $name . '","' . $selected . '","' . $manual . '")'));
        // // print_r("*****************");
        // // print_r($db[0]->result);

        //             if ($db[0]->result == 'Excel log uploaded') {
        // // print_r("+++++++++++");

        //                 $arr_file = explode('.', $name);
        //                 $extension = end($arr_file);

        //                 if ('csv' == $extension) {
        //                     $reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
        //                 } else if ('xls' == $extension) {
        //                     $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xls();
        //                 } else
        //                     $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();

        //                     // print_r("--");
        //                     // print_r('ftp://' . $ftp_server . '/' . $name);
        //                     // print_r("--");
        //                 // $reader->setLoadSheetsOnly(["Sheet 2", "Investor Details"]);
        //                 $spreadsheet = $reader->load('ftp://' . $ftp_server . '/inbox/IEPF2/' . $name);
        //                 $worksheet = $spreadsheet->getActiveSheet();
        //                 $rows = $worksheet->toArray();
        //                 // print_r(json_encode($rows));

        //                 $Sheet1 = [];
        //                 $i = 0;
        //                 foreach ($rows as $row) {
        //                     if ($i == 1) {
        //                         $cin = $row[1];
        //                         $company = $row[6];
        //                     }
        //                     // if ($i >= 14) {



        //                     if ($process == 1) {
        //                         // if ($i >= 14) {
        //                         $time = strtotime($row[15]);
        //                         $Sheet1[] = [
        //                             "Investor First Name" => str_replace("'", "", str_replace("\\","\\/",$row[0])),
        //                             "Investor Middle Name" => str_replace("'", "", str_replace("\\","\\/",$row[1])),
        //                             "Investor Last Name" => str_replace("'", "", str_replace("\\","\\/",$row[2])),
        //                             "Father\/Husband First Name" => str_replace("'", "", str_replace("\\","\\/",$row[3])),
        //                             "Father\/Husband Middle Name" => str_replace("'", "", str_replace("\\","\\/",$row[4])),
        //                             "Father\/Husband Last Name" => str_replace("\\","\\/",$row[5]),
        //                             "Address" => str_replace("'", "", str_replace("\\","\\/",str_replace("\\","\\/",str_replace('"','',$row[6])))),
        //                             "Country" => str_replace("'", "", str_replace("\\","\\/",$row[7])),
        //                             "State" => str_replace("'", "", str_replace("\\","\\/",$row[8])),
        //                             "District" => str_replace("'", "", str_replace("\\","\\/",$row[9])),
        //                             "Pin Code" => str_replace("'", "", str_replace("\\","\\/",$row[10])),
        //                             "FOLIO NUMBER" => $row[11] == '' ? '-' : str_replace("'", "", str_replace("\\","\\/",$row[11])),
        //                             "DP Id-Client Id-Account Number" => str_replace("'", "", str_replace("\\","\\/",$row[12])),
        //                             "Investment Type" => str_replace("'", "", str_replace("\\","\\/",$row[13])),
        //                             "Amount transferred" => str_replace(",", "", str_replace("'", "", str_replace("\\","\\/",$row[14]))),
        //                             "Proposed Date of transfer to IEPF(DD-MON-YYYY)" => str_replace("\\","\\/",$row[15]), //date('d-m-Y',$time),
        //                             "Column17" => null,
        //                             "Column18" => $cin,
        //                             "IEPF-1 check" => $cin,
        //                             "CIN" => $cin,
        //                             "Check" => true
        //                         ];
        //                         // }
        //                     }
        //                     if ($row[0] == "Investor First Name" && $row[11] = "Folio Number") {
        //                         $process = 1;
        //                         echo "header";
        //                     }
        //                     $i++;
        //                 }
        //                 ini_set('memory_limit', '1024M');
        //                 $data = (object) array('Sheet1' => $Sheet1);
        //                 // echo (json_encode($data));

        //                 $db2 = DB::select(DB::raw('CALL STORE_JSON(\'' . json_encode($data) . '\', "' . $name . '")'));
        //                 $message = $db2[0]->result;
        //                 if ($message == 'File already processed' || $message == 'First, Upload excel via Store_excel_log') {
        //                     // if (Storage::exists('public/uploads/ErrorFiles/IEPF2/' . $name)) {
        //                     if(File::exists('public/uploads/ErrorFiles/IEPF2/' . $name)){
        //                         // print_r($name);
        //                         $withoutExt = preg_replace('/\\.[^.\\s]{3,4}$/', '', $name);
        //                         // $mytime = Carbon::now();
        //                         // $mynewtime = $mytime->toDate();
        //                         $date111 = date('Y-m-d');
        //                         $newname = $withoutExt . $date111;
        //                         // Storage::delete('public/uploads/processed/IEPF2/' . $name);
        //                         // Storage::move($this->INBOX_PATH.'/' . $name, 'public/uploads/ErrorFiles/IEPF2/' . $newname . ".xls");
        //                         File::put('public/uploads/ErrorFiles/IEPF2/' . $newname . ".xls", Storage::disk('c_path')->get($name));
        //                         ftp_delete($conn_id, 'Inbox/IEPF2/'. $name);
        //                     } else {
        //                         // Storage::move($this->INBOX_PATH.'/' . $name, 'public/uploads/ErrorFiles/IEPF2/' . $name);
        //                         File::put('public/uploads/ErrorFiles/IEPF2/' . $name, Storage::disk('c_path')->get($name));
        //                         ftp_delete($conn_id, 'Inbox/IEPF2/'. $name);
        //                     }

        //                     $flag = 1;
        //                 } else if ($message == 'SUCCESS, multiple dividend data available' || $message == 'SUCCESS, No multiple dividend data available') {
        //                     // if (Storage::exists('public/uploads/processed/IEPF2/' . $name)) {
        //                     if(File::exists('public/uploads/processed/IEPF2/' . $name)){
        //                         $withoutExt = preg_replace('/\\.[^.\\s]{3,4}$/', '', $name);
        //                         // $mytime = Carbon::now();
        //                         // $mynewtime = $mytime->toDate();
        //                         $date111 = date('Y-m-d');
        //                         $newname = $withoutExt . $date111;
        //                         // Storage::move('ftp://' . $ftp_server . '/' . $name, 'public/uploads/processed/IEPF2/' . $newname . ".xls");
        //                         File::put('public/uploads/processed/IEPF2/' . $newname . ".xls", Storage::disk('c_path')->get($name));
        //                         ftp_delete($conn_id, 'Inbox/IEPF2/'. $name);
        //                     } else {
        //                         // Storage::move('ftp://' . $ftp_server . '/' . $name, 'public/uploads/processed/IEPF2/' . $name);
        //                         File::put('public/uploads/processed/IEPF2/' . $name, Storage::disk('c_path')->get($name));
        //                         ftp_delete($conn_id, 'Inbox/IEPF2/'. $name);
        //                     }
        //                     // return redirect('/home')->with('message', $message);
        //                 }
        //             } else {
        //                 if(File::exists('public/uploads/ErrorFiles/IEPF2/' . $name)){
        //                     $withoutExt = preg_replace('/\\.[^.\\s]{3,4}$/', '', $name);
        //                     $date111 = date('Y-m-d');
        //                     $newname = $withoutExt . $date111;
        //                     File::put('public/uploads/ErrorFiles/IEPF2/' . $newname . ".xls", Storage::disk('c_path')->get($name));
        //                     ftp_delete($conn_id, 'Inbox/IEPF2/'. $name);
        //                 } else {
        //                     File::put('public/uploads/ErrorFiles/IEPF2/' . $name, Storage::disk('c_path')->get($name));
        //                     ftp_delete($conn_id, 'Inbox/IEPF2/'. $name);
        //                 }
        //             }
        //         }
        //     }
        //     // print_r("Success");
        //     // die;
        // }

        // if ($flag == 0) {
        //     $message = "Some Files Uploaded Already";
        //     return redirect('/multidiv')->with('errmessage', $message);
        // } else {
        //     $message = "All Files uploaded Succesfully";
        //     return redirect('/home')->with('message', $message);
        // }


        // // $custommessage = "No files exist ";

        // // print_r($this->INBOX_PATH);
        // // print_r("---");
        // // print_r($files);
        // // die;


        // // if (count($files) < 1) {
        // //     return redirect('/home')->with('errmessage', $custommessage);
        // // }

        // // $files = preg_replace("/public/i", "", $files);

        // // $flag = 0;

        // // foreach ($files as $row2) {
        // //     $process = 0;
        // //     $name = basename($row2);

        // //     $selected = 'IEPF2';
        // //     $manual = 'manual';
        // //     $db = DB::select(DB::raw('CALL Store_excellog("' . $name . '","' . $selected . '","' . $manual . '")'));


        // //     if ($db[0]->result == 'Excel log uploaded') {


        // //         $arr_file = explode('.', $name);
        // //         $extension = end($arr_file);

        // //         if ('csv' == $extension) {
        // //             $reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
        // //         } else if ('xls' == $extension) {
        // //             $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xls();
        // //         } else
        // //             $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();

        // //         // $reader->setLoadSheetsOnly(["Sheet 2", "Investor Details"]);
        // //         $spreadsheet = $reader->load(storage_path($this->INBOX_PATH) . '/' . $name);
        // //         $worksheet = $spreadsheet->getActiveSheet();
        // //         $rows = $worksheet->toArray();
        // //         // print_r(json_encode($rows));

        // //         $Sheet1 = [];
        // //         $i = 0;
        // //         foreach ($rows as $row) {
        // //             if ($i == 1) {
        // //                 $cin = $row[1];
        // //                 $company = $row[6];
        // //             }
        // //             // if ($i >= 14) {



        // //             if ($process == 1) {
        // //                 // if ($i >= 14) {
        // //                 $time = strtotime($row[15]);
        // //                 $Sheet1[] = [
        // //                     "Investor First Name" => str_replace("'", "", $row[0]),
        // //                     "Investor Middle Name" => str_replace("'", "", $row[1]),
        // //                     "Investor Last Name" => str_replace("'", "", $row[2]),
        // //                     "Father\/Husband First Name" => str_replace("'", "", $row[3]),
        // //                     "Father\/Husband Middle Name" => str_replace("'", "", $row[4]),
        // //                     "Father\/Husband Last Name" => $row[5],
        // //                     "Address" => str_replace("'", "", $row[6]),
        // //                     "Country" => str_replace("'", "", $row[7]),
        // //                     "State" => str_replace("'", "", $row[8]),
        // //                     "District" => str_replace("'", "", $row[9]),
        // //                     "Pin Code" => str_replace("'", "", $row[10]),
        // //                     "FOLIO NUMBER" => '11111', //$row[11] == '' ? '11111' : str_replace("'", "", $row[11]),
        // //                     "DP Id-Client Id-Account Number" => str_replace("'", "", $row[12]),
        // //                     "Investment Type" => str_replace("'", "", $row[13]),
        // //                     "Amount transferred" => str_replace(",", "", str_replace("'", "", $row[14])),
        // //                     "Proposed Date of transfer to IEPF(DD-MON-YYYY)" => $row[15], //date('d-m-Y',$time),
        // //                     "Column17" => null,
        // //                     "Column18" => $cin,
        // //                     "IEPF-1 check" => $cin,
        // //                     "CIN" => $cin,
        // //                     "Check" => true
        // //                 ];
        // //                 // }
        // //             }
        // //             if ($row[0] == "Investor First Name" && $row[11] = "Folio Number") {
        // //                 $process = 1;
        // //                 echo "header";
        // //             }
        // //             $i++;
        // //         }
        // //         ini_set('memory_limit', '1024M');
        // //         $data = (object) array('Sheet1' => $Sheet1);
        // //         // echo (json_encode($data));

        // //         $db2 = DB::select(DB::raw('CALL STORE_JSON(\'' . json_encode($data) . '\', "' . $name . '")'));
        // //         $message = $db2[0]->result;
        // //         if ($message == 'File already processed' || $message == 'First, Upload excel via Store_excel_log') {
        // //             if (Storage::exists('public/uploads/ErrorFiles/IEPF2/' . $name)) {
        // //                 // print_r($name);
        // //                 $withoutExt = preg_replace('/\\.[^.\\s]{3,4}$/', '', $name);
        // //                 // $mytime = Carbon::now();
        // //                 // $mynewtime = $mytime->toDate();
        // //                 $date111 = date('Y-m-d');
        // //                 $newname = $withoutExt . $date111;
        // //                 // Storage::delete('public/uploads/processed/IEPF2/' . $name);
        // //                 Storage::move($this->INBOX_PATH.'/' . $name, 'public/uploads/ErrorFiles/IEPF2/' . $newname . ".xls");
        // //             } else {
        // //                 Storage::move($this->INBOX_PATH.'/' . $name, 'public/uploads/ErrorFiles/IEPF2/' . $name);
        // //             }

        // //             $flag = 1;
        // //         } else if ($message == 'SUCCESS, multiple dividend data available' || $message == 'SUCCESS, No multiple dividend data available') {
        // //             if (Storage::exists('public/uploads/processed/IEPF2/' . $name)) {
        // //                 // print_r($name);
        // //                 $withoutExt = preg_replace('/\\.[^.\\s]{3,4}$/', '', $name);
        // //                 // $mytime = Carbon::now();
        // //                 // $mynewtime = $mytime->toDate();
        // //                 $date111 = date('Y-m-d');
        // //                 $newname = $withoutExt . $date111;
        // //                 // Storage::delete('public/uploads/processed/IEPF2/' . $name);
        // //                 Storage::move($this->INBOX_PATH.'/' . $name, 'public/uploads/processed/IEPF2/' . $newname . ".xls");
        // //             } else {
        // //                 Storage::move($this->INBOX_PATH.'/' . $name, 'public/uploads/processed/IEPF2/' . $name);
        // //             }
        // //             // return redirect('/home')->with('message', $message);
        // //         }
        // //     } else {
        // //         if (Storage::exists('public/uploads/ErrorFiles/IEPF2/' . $name)) {
        // //             // print_r($name);
        // //             $withoutExt = preg_replace('/\\.[^.\\s]{3,4}$/', '', $name);
        // //             // $mytime = Carbon::now();
        // //             // $mynewtime = $mytime->toDate();
        // //             $date111 = date('Y-m-d');
        // //             $newname = $withoutExt . $date111;
        // //             // Storage::delete('public/uploads/processed/IEPF2/' . $name);
        // //             Storage::move($this->INBOX_PATH.'/' . $name, 'public/uploads/ErrorFiles/IEPF2/' . $newname . ".xls");
        // //         } else {
        // //             Storage::move($this->INBOX_PATH.'/' . $name, 'public/uploads/ErrorFiles/IEPF2/' . $name);
        // //         }
        // //     }
        // // }
        // // if ($flag == 1) {
        // //     $message = "Some Files Uploaded Already";
        // //     return redirect('/multidiv')->with('errmessage', $message);
        // // } else {
        // //     $message = "All Files uploaded Succesfully";
        // //     return redirect('/home')->with('message', $message);
        // // }
    }
}
