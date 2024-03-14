<?php

namespace App\Imports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithMappedCells;
use Illuminate\Support\Facades\DB;
use app\Http\Controllers\MailController;


class UserImport implements WithMultipleSheets
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function sheets(): array
    {
        return [
            'Investor Details' => new SecondSheetImport(),
        ];
    }
}

class FirstSheetImport implements ToCollection
{

    public function collection(Collection $rows)
    {
        //
    }
}

class SecondSheetImport implements ToCollection
{

    public function collection(Collection $rows)
    {

        $Sheet1 = [];
        $i = 0;

        foreach ($rows as $row) {

            if ($i == 1) {
                $cin = $row[1];
                $company = $row[6];
            }
            if ($i >= 14) {

                // $d = new DateTime($row[15]);
                $time = strtotime($row[15]);
                $Sheet1[] = [
                    "Investor First Name" => str_replace("'", "", $row[0]),
                    "Investor Middle Name" => str_replace("'", "", $row[1]),
                    "Investor Last Name" => str_replace("'", "", $row[2]),
                    "Father\/Husband First Name" => str_replace("'", "", $row[3]),
                    "Father\/Husband Middle Name" => str_replace("'", "", $row[4]),
                    "Father\/Husband Last Name" => $row[5],
                    "Address" => str_replace("'", "", $row[6]),
                    "Country" => str_replace("'", "", $row[7]),
                    "State" => str_replace("'", "", $row[8]),
                    "District" => str_replace("'", "", $row[9]),
                    "Pin Code" => str_replace("'", "", $row[10]),
                    "FOLIO NUMBER" => '11111', //$row[11] == '' ? '11111' : str_replace("'", "", $row[11]),
                    "DP Id-Client Id-Account Number" => str_replace("'", "", $row[12]),
                    "Investment Type" => str_replace("'", "", $row[13]),
                    "Amount transferred" => str_replace("'", "", $row[14]),
                    "Proposed Date of transfer to IEPF(DD-MON-YYYY)" => $row[15], //date('d-m-Y',$time),
                    "Column17" => null,
                    "Column18" => $cin,
                    "IEPF-1 check" => $cin,
                    "CIN" => $cin,
                    "Check" => true
                ];
            }
            $i++;
        }
        ini_set('memory_limit', '1024M');

        // $data = [
        //     "Sheet1"=>$Sheet1
        // ];
        $data = (object) array('Sheet1' => $Sheet1);
        // print_r('CALL STORE_JSON(\''.json_encode($data).'\', "excel_1")');
        $db = DB::select(DB::raw('CALL STORE_JSON(\'' . json_encode($data) . '\', "excel_1")'));

        $response = MailController::sendmail($data);

        // $db1 = DB::select(DB::raw('CALL iepf_upload()'));
        // print_r($db);
        // print_r($db1);



    }
}
