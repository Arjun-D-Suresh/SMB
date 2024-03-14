<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function userPortal()
    {
        $data = [];
        return view('home_ram', ["data" => $data]);
    }
    public function userSearch()
    {
        $data = [];
        return view('usersearch', ["data" => $data]);
    }
    public function UserSearchResult(Request $request)
    {
        $investor_name = empty($request->input('investor_name')) ? "" : $request->input('investor_name');
        $fs_name = empty($request->input('fs_name')) ? "" : $request->input('fs_name');
        $address = empty($request->input('address')) ? "" : $request->input('address');
        $country = empty($request->input('country')) ? "" : $request->input('country');
        $state = empty($request->input('state')) ? "" : $request->input('state');
        $district = empty($request->input('district')) ? "" : $request->input('district');
        $pincode = empty($request->input('pincode')) ? "" : $request->input('pincode');
        $min_share_value = empty($request->input('min_share_value')) ? "" : $request->input('min_share_value');
        $max_share_value = empty($request->input('max_share_value')) ? "" : $request->input('max_share_value');
        $skip = $request->input('skip');
        $take = $request->input('take');

        $query = 'SELECT 
                    (@row_number := @row_number + 1) AS row_num, 
                    tbl.* 
                    FROM 
                        (
                        SELECT 
                        group_concat(urn) AS urn_ids, 
                        masked_name, 
                        masked_fs_name, 
                        masked_address, 
                        pincode, 
                        sum(dividend_amount) AS share_value 
                        FROM 
                            (
                            SELECT 
                                final.urn, 
                                final.c_investor_name, 
                                final.c_f_name, 
                                final.final_address, 
                                final.masked_address, 
                                final.masked_name, 
                                final.pincode, 
                                final.masked_fs_name 
                            FROM 
                            (
                                SELECT 
                                unique_investor.masked_name, 
                                unique_investor.masked_address, 
                                unique_investor.urn, 
                                unique_investor.first_urn, 
                                unique_investor.id, 
                                unique_investor.c_investor_name, 
                                unique_investor.c_f_name, 
                                unique_investor.final_address, 
                                unique_investor.pincode, 
                                unique_investor.masked_fs_name 
                                FROM 
                                (
                                SELECT 
                                    LOWER(REPLACE(fh.investor_name, " ", "")) AS c_investor_name, 
                                    LOWER(REPLACE(fh.fs_fname, " ", "")) AS c_f_name, 
                                    LOWER(REPLACE(fh.address, " ", "")) AS c_address, 
                                    fh.pincode AS pincode, 
                                    SUBSTRING_INDEX(GROUP_CONCAT(fh.urn ORDER BY fh.urn), ",", 1) AS first_urn, 
                                    GROUP_CONCAT(fh.urn) AS urn, 
                                    GROUP_CONCAT(fh.id) AS id, 
                                    CONCAT(LEFT(fh.investor_name, 4), REPEAT("*", 7), RIGHT(fh.investor_name, 4)) AS masked_name, 
                                    CONCAT(LEFT(fh.fs_name, 4), REPEAT("*", 7), RIGHT(fh.fs_name, 4)) AS masked_fs_name, 
                                    CONCAT(LEFT(fh.address, 8), REPEAT("*", 7), RIGHT(fh.address, 6)) AS masked_address, 
                                    RIGHT(LOWER(REPLACE(fh.address, " ", "")), 6) AS last_address, 
                                    CASE WHEN RIGHT(LOWER(REPLACE(fh.address, " ", "")), 6) REGEXP "^[0-9]+$" THEN 1 ELSE 0 END AS is_number, 
                                    CASE WHEN RIGHT(LOWER(REPLACE(fh.address, " ", "")), 6) REGEXP "^[0-9]+$" THEN CONCAT(SUBSTRING(LOWER(REPLACE(fh.address, " ", "")), 1, LENGTH(LOWER(REPLACE(fh.address, " ", ""))) - 7), RIGHT(LOWER(REPLACE(fh.address, " ", "")), 6))
                                    ELSE CONCAT(LOWER(REPLACE(fh.address, " ", "")), IF(fh.pincode IS NOT NULL, fh.pincode, ""))
                                END AS final_address 
                                    FROM 
                                        folioheader fh 
                                WHERE 
                                    fh.investor_name IS NOT NULL 
                                    AND fh.fs_fname IS NOT NULL 
                                    AND fh.address IS NOT NULL ';

        if ($investor_name != '') {
            $query .= 'AND REPLACE(investor_name, " ", "") LIKE "%' . $investor_name . '%"';
        }
        if ($fs_name != '') {
            $query .= 'AND REPLACE(fs_name, " ", "") LIKE "%' . $fs_name . '%"';
        }
        if ($address != '') {
            $query .= 'AND REPLACE(address, " ", "") LIKE "%' . $address . '%"';
        }
        if ($country != '') {
            $query .= 'AND REPLACE(country, " ", "") LIKE "%' . $country . '%"';
        }
        if ($state != '') {
            $query .= 'AND REPLACE(state, " ", "") LIKE "%' . $state . '%"';
        }
        if ($district != '') {
            $query .= 'AND REPLACE(district, " ", "") LIKE "%' . $district . '%"';
        }
        if ($pincode != '') {
            $query .= 'AND REPLACE(pincode, " ", "") LIKE "%' . $pincode . '%"';
        }
        $query .= 'GROUP BY 
                    fh.investor_name, 
                    fh.fs_fname, 
                    fh.address, 
                    fh.pincode,
                    fh.fs_name
                            ) AS unique_investor
                        ) AS final 
                        JOIN folioheader AS fh on fh.id in (final.id)
                    ) AS folio_h 
                    JOIN folio_dividend AS fd on folio_h.urn = fd.urn_key 
                    group by 
                        c_investor_name, 
                        c_f_name, 
                        final_address,
                        masked_name,
                        masked_fs_name,
                        masked_address,
                        pincode ';

        if ($min_share_value != '' && $max_share_value != '') {
            $query .= 'HAVING (share_value >= ' . $min_share_value . ' AND share_value <= ' . $max_share_value . ') ';
        }
        $query .= ' LIMIT ' . $skip . ',' . $take . ' ) as tbl, (SELECT @row_number:=0) as r;';
        // $only_groupby_mode = 'SET sql_mode=(SELECT REPLACE(@@sql_mode,"ONLY_FULL_GROUP_BY",""));';
        // $off_groupby_mode = DB::select(DB::raw($only_groupby_mode));
        $data = DB::select(DB::raw($query));
        $data = array("data" => $data);
        echo json_encode($data);
    }
    public function getUniqueDividentDetails(Request $request)
    {
        $urn_ids = $request->urn;
        $urnsArray = explode(",", $urn_ids);
        $urns = "'" . implode("','", $urnsArray) . "'";
        $sql = 'SELECT (@row_number:=@row_number + 1) AS row_num, tbl.* FROM(
        SELECT fh.investor_name AS investor_name,fh.fs_name AS fs_name,fh.address AS address,fh.country AS country,fh.district AS distruct,fh.nof_shares AS share_value,fh.folio_number AS folio_number,fh.pincode AS pincode,fh.state AS state,fh.urn AS urn,sum(fd.dividend_amount) AS total,group_concat(fd.id) as divident_ids FROM folioheader fh
                JOIN folio_dividend fd on fd.urn_key = fh.urn where fd.urn_key in (' . $urns . ')
                group by fh.investor_name,fh.fs_name,fh.address,fh.country,fh.district,fh.nof_shares,fh.folio_number,fh.pincode,fh.state,fh.urn
                ) as tbl, (SELECT @row_number:=0) as r;';
        $data = DB::select(DB::raw($sql));
        $data = array("data" => $data);
        echo json_encode($data);
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
}
