<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FolioDetailsController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function folioHeaderDeatails()
    {
        return view('folioDetails');
    }

    public function folioHeaderData(Request $request)
    {
        $search = $request->search;
        $skip = $request->skip;
        $take = $request->take;
        $data['data'] = DB::select(DB::raw('select fh.* from folioheader fh where fh.urn like "%' . $search . '%" limit ' . $skip . ',' . $take . ';'));
        $data['total_data'] = DB::select(DB::raw('select count(*) as total from folioheader fh where fh.urn like "%' . $search . '%" limit ' . $skip . ',' . $take . ';'));
        $data['skip'] = $skip;
        return json_encode($data);
    }

    public function folioDividenData(Request $request)
    {
        $urn = $request->urn;
        $data = DB::select(DB::raw('select fd.*, dm.purpose from folio_dividend fd join dividend_master dm on fd.dm_id = dm.id where fd.urn_key = "' . $urn . '";'));
        return json_encode($data);
    }
}
