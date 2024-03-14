@extends('layouts.app')

@section('content')
<div class="container">
    <div class="input-group mb-3" style="width: 20%;">

        <input id="folioSearch" type="text" class="form-control" value="">
        <span id="folioSearchSpn" class="input-group-text dropbtn">Search</span>
    </div>

    <div>
        <div>
            <div class="table-container">
                <div class="table-responsive">
                    <table class="table" id="folioHeaderTable">
                        <thead class="thead-dark table-head">
                            <tr>
                                <th class="w-5" scope=" col">#</th>
                                <th class="w-10" scope="col">Folio/DP id</th>
                                <th class="w-15" scope="col">CIN</th>
                                <th class="w-10" scope="col">Name</th>
                                <th class="w-10" scope="col">Father Name</th>
                                <th class="w-10" scope="col">Address</th>
                                <th class="w-10" scope="col">Country</th>
                                <th class="w-10" scope="col">State</th>
                                <th class="w-10" scope="col">District</th>
                                <th class="w-10" scope="col">Pincode</th>
                                <th class="w-10" scope="col">No of Shares</th>
                                <th class="w-10" scope="col">Foilo Status</th>
                                <th class="w-10" scope="col">PAN</th>
                                <th class="w-10" scope="col">ADHAAR</th>
                                <th class="w-10" scope="col">DOB</th>
                                <th class="w-10" scope="col">IEPF Claimed</th>
                                <th class="w-10" scope="col">UCD Claimed</th>
                                <th class="w-10" scope="col">Joint Holder</th>
                            </tr>
                        </thead>
                        <tbody class="folioHeaderTable" id="folioHeaderTableBody">

                        </tbody>
                    </table>
                    <div id="noData">

                    </div>
                </div>
            </div>



            <!-- <div id="folioHeaderTableDiv" class="table-responsive fixTableHead" style="overflow-y: auto;height:250px;width:100%;">
                <table class="table" id="folioHeaderTable">
                    <thead class="thead-dark table-head">
                        <tr>
                            <th scope=" col">#</th>
                            <th scope="col">Folio/DP id</th>
                            <th scope="col">CIN</th>
                            <th scope="col">Name</th>
                            <th scope="col">Father Name</th>
                            <th scope="col">Address</th>
                            <th scope="col">Country</th>
                            <th scope="col">State</th>
                            <th scope="col">District</th>
                            <th scope="col">Pincode</th>
                            <th scope="col">No of Shares</th>
                            <th scope="col">Foilo Status</th>
                            <th scope="col">PAN</th>
                            <th scope="col">ADHAAR</th>
                            <th scope="col">DOB</th>
                            <th scope="col">IEPF Claimed</th>
                            <th scope="col">UCD Claimed</th>
                            <th scope="col">Joint Holder</th>
                        </tr>
                    </thead>
                    <tbody class="dividendtable" id="folioHeaderTableBody"></tbody>
                </table>
                <div id="noData">

                </div>
            </div> -->
        </div>
        <br>
        <div>
            <div id="folioDividendTableDiv" class="table-responsive fixTableHead" style="overflow-y: auto;height:250px;width:100%;">
                <table class="table" id="folioDividendTable">
                    <thead class="thead-dark table-head">
                        <tr>
                            <th scope=" col">#</th>
                            <th scope="col">Folio/DP id</th>
                            <th scope="col">CIN</th>
                            <th scope="col">Dividend Amount</th>
                            <th scope="col">Proposed date of xfer</th>
                            <th scope="col">Purpose</th>
                        </tr>
                    </thead>
                    <tbody class="dividendtable" id="folioDividendTableBody"></tbody>
                </table>
                <div id="noDividendData">

                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .w-15 {
        width: 15% !important;
    }

    .w-10 {
        width: 10% !important;
    }

    .w-5 {
        width: 5% !important;
    }

    .w-25 {
        width: 25% !important;
    }

    #folioSearch,
    span {
        font-size: 15px;
    }

    .table-head {
        background: #695800;
        color: #fff;
        font-family: 'Raleway', sans-serif;
    }

    table th:first-child {
        border-radius: 10px 0 0 0;
    }

    table th:last-child {
        border-radius: 0 10px 0 0;
    }

    .table-container {
        width: 100%;
        overflow-x: auto;
    }

    .table-responsive {
        width: 150%;
        height: 350px;
        /* Adjust the height as needed */
        overflow-y: auto;
        overflow-x: auto;
    }

    .table {
        width: 100%;
        table-layout: fixed;
    }

    thead,
    tbody {
        display: block;
    }


    thead,
    tbody tr {
        display: table;
        width: 100%;
        table-layout: fixed;
    }

    .spinner-border-folio {
        width: 4rem;
        height: 4rem;
    }

    tbody tr td {
        word-wrap: break-word;
    }

    .folioHeaderTable {
        font-size: 9px;
    }
</style>

<script>
    var base_url = window.location.origin + '/';

    $(document).ready(function() {

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('#noData').html('<div style="padding: 70px 0;text-align: center;"><p>No data available</p></div>');
        $('#noDividendData').html('<div style="padding: 70px 0;text-align: center;"><p>No data available</p></div>');
        let timeout = null;
        $("#folioSearch").keyup(function() {
            clearTimeout(timeout);

            timeout = setTimeout(function() {
                searchFunction();
            }, 1000);
        });

        let searchFunction = () => {
            let folioSearch = $("#folioSearch").val();
            let html = '';
            $('#noData').html('');
            $('#folioHeaderTableBody').html(html);
            $('#folioSearchSpn').html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');
            sessionStorage.setItem('folio_skip', 0);
            sessionStorage.setItem('total_value', 0);
            let data = {
                search: folioSearch,
                take: 10,
                skip: 0
            }
            var saveData = $.ajax({
                type: 'POST',
                url: "{{url('/folio-header-data')}}",
                data: JSON.stringify(data),
                contentType: 'application/json; charset=utf-8',
                dataType: "json",
                success: function(resultData) {
                    console.log(resultData);
                    let data = resultData['data'];
                    sessionStorage.setItem('folio_skip', resultData['skip']);
                    sessionStorage.setItem('total_value', resultData['total_data'][0]['total']);

                    for (let i = 0; i < data.length; i++) {
                        html = html + '<tr>' +
                            '<td class="w-5"><input type="radio" value="' + data[i].urn + '" name="urnSelect" class="urnSelect"/></td>' +
                            '<td class="w-10">' + data[i].urn + '</td>' +
                            '<td class="w-15">' + data[i].cin + '</td>' +
                            '<td class="w-10">' + data[i].investor_name + '</td>' +
                            '<td class="w-10">' + data[i].fs_name + '</td>' +
                            '<td class="w-10">' + data[i].address + '</td>' +
                            '<td class="w-10">' + data[i].country + '</td>' +
                            '<td class="w-10">' + data[i].state + '</td>' +
                            '<td class="w-10">' + data[i].district + '</td>' +
                            '<td class="w-10">' + data[i].pincode + '</td>' +
                            '<td class="w-10">' + data[i].nof_shares + '</td>' +
                            '<td class="w-10">' + data[i].folio_status + '</td>' +
                            '<td class="w-10">' + (data[i].pan == null ? '' : data[i].pan) + '</td>' +
                            '<td class="w-10">' + (data[i].aadhaar == null ? '' : data[i].aadhaar) + '</td>' +
                            '<td class="w-10">' + (data[i].dob == null ? '' : data[i].dob) + '</td>' +
                            '<td class="w-10">' + (data[i].iepf_claimed == 0 ? 'No' : 'Yes') + '</td>' +
                            '<td class="w-10">' + (data[i].ucd_claimed == 0 ? 'No' : 'Yes') + '</td>' +
                            '<td class="w-10">' + (data[i].joint_holder_name == null ? '' : data[i].joint_holder_name) + ' </td>' +
                            '</tr>';
                    }
                    $('#folioHeaderTableBody').html(html);

                    $('.urnSelect').click(function() {
                        let folio = {
                            urn: $(this).val()
                        };
                        var saveData = $.ajax({
                            type: 'POST',
                            url: "{{url('/folio-dividend-data')}}",
                            data: folio,
                            dataType: "text",
                            success: function(resultDividendData) {
                                let dividendHTML = '';
                                $('#noDividendData').html('');
                                $('#folioDividendTableBody').html(dividendHTML);
                                let dividendData = JSON.parse(resultDividendData);
                                for (let i = 0; i < dividendData.length; i++) {
                                    dividendHTML = dividendHTML + '<tr>' +
                                        '<td>' + (i + 1) + '</td>' +
                                        '<td>' + dividendData[i].urn_key + '</td>' +
                                        '<td>' + dividendData[i].cin + '</td>' +
                                        '<td>' + dividendData[i].dividend_amount + '</td>' +
                                        '<td>' + dividendData[i].pd_of_xfer + '</td>' +
                                        '<td>' + dividendData[i].purpose + '</td>' +
                                        '</tr>';
                                }
                                $('#folioDividendTableBody').html(dividendHTML);
                                if (dividendData.length < 1) {
                                    $('#noDividendData').html('<div style="padding: 70px 0;text-align: center;"><p>No data available</p></div>');
                                }
                            }
                        });
                    });

                    if (data.length < 1) {
                        $('#noData').html('<div style="padding: 70px 0;text-align: center;"><p>No data available</p></div>');
                    }
                    $('#folioSearchSpn').html('Search');
                }
            });
            saveData.error(function() {
                alert("Something went wrong 1");
                $('#noData').html('<div style="padding: 70px 0;text-align: center;"><p>No data available</p></div>');
                $('#folioSearchSpn').html('Search');
            });
        }
        var $tbody = $(".table-responsive");
        var debounceTimer;
        var isLoadingData = false;
        $tbody.on("scroll resize", function() {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(function() {
                var isAtBottom = ($tbody.scrollTop() + $tbody.innerHeight() + 10) >= $tbody[0].scrollHeight;
                console.log('isLoadingData', isLoadingData);
                if (isAtBottom && !isLoadingData) {
                    console.log('--Scroll Bottom--');
                    loadMoreData();
                }
            }, 200);
        });

        const loadMoreData = () => {
            debugger;
            isLoadingData = true;

            let folioSearch = $("#folioSearch").val();
            let html = '';
            let skip_value = sessionStorage.getItem('folio_skip');
            let total_value = sessionStorage.getItem('total_value');
            let data = {
                search: folioSearch,
                take: 10,
                skip: Number(skip_value) + 10
            }
            console.log('total_value', total_value);
            console.log('skip_value', skip_value);
            console.log(Number(total_value) - (Number(skip_value) + 10));
            if (Number(total_value) - (Number(skip_value) + 10) > 0) {
                // Loader
                var $tbody = $("#folioHeaderTableBody");
                $tbody.find(".removable-loader").remove();
                $tbody.append(`
                    <tr class="removable-loader"><td colspan="9">
                    <div class="text-center">
                    <span class="spinner-border spinner-border-folio spinner-border-sm-load" role="status" aria-hidden="true"></span>
                    </div>
                    </td>
                    </tr>`);
                var saveData = $.ajax({
                    type: 'POST',
                    url: "{{url('/folio-header-data')}}",
                    data: JSON.stringify(data),
                    contentType: 'application/json; charset=utf-8',
                    dataType: "json",
                    success: function(resultData) {
                        isLoadingData = false;
                        sessionStorage.setItem('folio_skip', resultData['skip']);
                        let data = resultData['data'];
                        for (let i = 0; i < data.length; i++) {
                            html = html + '<tr>' +
                                '<td class="w-5"><input type="radio" value="' + data[i].urn + '" name="urnSelect" class="urnSelect"/></td>' +
                                '<td class="w-10">' + data[i].urn + '</td>' +
                                '<td class="w-15">' + data[i].cin + '</td>' +
                                '<td class="w-10">' + data[i].investor_name + '</td>' +
                                '<td class="w-10">' + data[i].fs_name + '</td>' +
                                '<td class="w-10">' + data[i].address + '</td>' +
                                '<td class="w-10">' + data[i].country + '</td>' +
                                '<td class="w-10">' + data[i].state + '</td>' +
                                '<td class="w-10">' + data[i].district + '</td>' +
                                '<td class="w-10">' + data[i].pincode + '</td>' +
                                '<td class="w-10">' + data[i].nof_shares + '</td>' +
                                '<td class="w-10">' + data[i].folio_status + '</td>' +
                                '<td class="w-10">' + (data[i].pan == null ? '' : data[i].pan) + '</td>' +
                                '<td class="w-10">' + (data[i].aadhaar == null ? '' : data[i].aadhaar) + '</td>' +
                                '<td class="w-10">' + (data[i].dob == null ? '' : data[i].dob) + '</td>' +
                                '<td class="w-10">' + (data[i].iepf_claimed == 0 ? 'No' : 'Yes') + '</td>' +
                                '<td class="w-10">' + (data[i].ucd_claimed == 0 ? 'No' : 'Yes') + '</td>' +
                                '<td class="w-10">' + (data[i].joint_holder_name == null ? '' : data[i].joint_holder_name) + '</td>' +
                                '</tr>';
                        }
                        $tbody.find(".removable-loader").remove();
                        $('#folioHeaderTableBody').append(html);
                        $('.urnSelect').click(function() {
                            let folio = {
                                urn: $(this).val()
                            };
                            var saveData = $.ajax({
                                type: 'POST',
                                url: "{{url('/folio-dividend-data')}}",
                                data: folio,
                                dataType: "text",
                                success: function(resultDividendData) {

                                    let dividendHTML = '';
                                    $('#noDividendData').html('');
                                    $('#folioDividendTableBody').html(dividendHTML);
                                    let dividendData = JSON.parse(resultDividendData);
                                    for (let i = 0; i < dividendData.length; i++) {
                                        dividendHTML = dividendHTML + '<tr>' +
                                            '<td>' + (i + 1) + '</td>' +
                                            '<td>' + dividendData[i].urn_key + '</td>' +
                                            '<td>' + dividendData[i].cin + '</td>' +
                                            '<td>' + dividendData[i].dividend_amount + '</td>' +
                                            '<td>' + dividendData[i].pd_of_xfer + '</td>' +
                                            '<td>' + dividendData[i].purpose + '</td>' +
                                            '</tr>';
                                    }
                                    $('#folioDividendTableBody').html(dividendHTML);
                                    if (dividendData.length < 1) {
                                        $('#noDividendData').html('<div style="padding: 70px 0;text-align: center;"><p>No data available</p></div>');
                                    }
                                }
                            });
                        });
                        if (data.length < 1) {
                            $('#noData').html('<div style="padding: 70px 0;text-align: center;"><p>No data available</p></div>');
                        }
                        $('#folioSearchSpn').html('Search');
                    }
                });
                saveData.error(function() {
                    alert("Something went wrong");
                    $('#noData').html('<div style="padding: 70px 0;text-align: center;"><p>No data available</p></div>');
                    $('#folioSearchSpn').html('Search');
                });
            }

        }

    });
</script>

@endsection