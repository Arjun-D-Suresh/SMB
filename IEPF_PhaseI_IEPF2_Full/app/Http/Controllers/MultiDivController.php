<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class MultiDivController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index()
    {
        $data = [];
        // $name = "";
        // //Get Muliple dividend data from excel 
        // $db = DB::select(DB::raw("CALL get_multiple_dividend('L25111TN1960PLC004306');"));
        // $data["multi"] =  json_decode(json_encode($db), true);
        // //All Dividend from Dividend Master 
        // $db1 = DB::select(DB::raw("CALL get_dividend('L25111TN1960PLC004306');"));
        // $data["divi"] = json_decode(json_encode($db1), true);

        return view('multidiv', ["data" => $data]);
    }
    // get company
    public function getcompany()
    {
        return DB::select(DB::raw("CALL get_md_company();"));
    }

    //get file list

    public function getMultipleDividendFile($cin)
    {
        return DB::select("CALL get_multiple_dividend_file('$cin');");
    }

    //get file list

    public function getMultipleDividendXfer($log_id)
    {
        return DB::select("CALL get_multiple_dividend_xfer($log_id);");
    }

    // Left Table
    public function getdividentlist(Request $request)
    {

        $cin_number = request()->input('cin_number');
        $security_code = request()->input('security_code');

        return DB::select("CALL get_dividend('$security_code','$cin_number');");
    }

    // Right Table
    public function getmultipledividend()
    {
        $cin = request()->input('cin');
        $log_id = request()->input('log_id');
        $xfer_date = request()->input('xfer_date');
        $D_No = request()->input('D_No');
        return DB::select("CALL get_multiple_dividend('$cin',$log_id,'$xfer_date',$D_No);");
    }


    public function deletemembersdata()
    {
        $cin = request()->input('cin');
        $log_id = request()->input('log_id');
        $xfer_date = request()->input('xfer_date');

        return DB::select("call purge_multiple_dividend('$cin',$log_id,'$xfer_date');");
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        echo "test";
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
    public function getMultipleDividendData(Request $request)
    {
        set_time_limit(3000);
        $cin = $request->cin;
        $security_code = $request->security_code;
        $log_id = $request->log_id;
        $xfer_date = $request->xfer_date;
        $division = $request->division;
        $skip = $request->skip;
        $take = $request->take;
        $response = Http::post(env('FLASK_API', '') . 'api/get-multidividend-data', [
            'cin' => $cin,
            'security_code' => $security_code,
            'log_id' => $log_id,
            'xfer_date' => $xfer_date,
            'division' => $division,
            'skip' => $skip,
            'take' => $take
        ]);
        return json_decode($response->body());
    }
    public function processMultiDividend(Request $request)
    {
        set_time_limit(3000);
        $cin = $request->cin;
        $log_id = $request->log_id;
        $xfer_date = $request->xfer_date;
        $division = $request->division;
        $dividend_id = $request->dividend_id;
        $response = Http::post(env('FLASK_API', '') . 'api/multiple-dividend', [
            'cin' => $cin,
            'log_id' => $log_id,
            'xfer_date' => $xfer_date,
            'division' => $division,
            'dividend_id' => $dividend_id,
        ]);
        return json_decode($response->body());
    }
}
