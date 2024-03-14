<?php
  
namespace App\Exports;
  
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
  
class UsersExport implements FromCollection, WithHeadings
{
    protected $data;
  
    /**
     * Write code on Method
     *
     * @return response()
     */
    public function __construct($data)
    {
        $this->data = $data;
    }
  
    /**
     * Write code on Method
     *
     * @return response()
     */
    public function collection()
    {
        return collect($this->data);
    }
  
    /**
     * Write code on Method
     *
     * @return response()
     */
    public function headings() :array
    {
        return [
            'Investor First Name',
            'Investor Middle Name',
            'Investor Last Name',
            'Father/Husband First Name',
            'Father/Husband Middle Name',
            'Father/Husband Last Name',
            "Address",
            "Country",
            "State",
            "District",
            "Pin Code",
            "FOLIO NUMBER",
            "DP Id-Client Id-Account Number",
            "Investment Type",
            "Amount transferred",
            "Proposed Date of transfer to IEPF(YYYY-MM-DD)",
            "CIN"
        ];
    }
}