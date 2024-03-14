

<?php $__env->startSection('content'); ?>

<?php

use Illuminate\Support\Facades\DB;

?>
<div class="container">

    <div id="ermsg">
        <?php if(session()->has('errmessage')): ?>
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="alert alert-danger">
                    <?php echo e(session()->get('errmessage')); ?>

                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
    <div id="succmsg" style="display: none;">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="alert alert-success" id="msgcnt">

                </div>
            </div>
        </div>
    </div>

    <!-- <a class="btn btn-secondary dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            Dropdown link
        </a> -->
    <div id="tablemsgdiv" style="display: none;">
        <div class="row justify-content-center">
            <div class="col-md-8">

                <div class="alert alert-danger" id="tablemsg">

                </div>
            </div>
        </div>
    </div>
    <div class="col-md-12">
        <div class="col-md-3">
            <label class="" for="selecteddata">Company :</label>
            <div class="dropdown">
                <input class="w-100" id="companyNameselect" name="company_name" autocomplete="off" readonly>
                <!--  -->
                <input type="hidden" name="cin_number" id="cin_number" value="">
                <!--  -->
                <input type="hidden" id="security_code" value="">
                <!--  -->
                <div id="myDropdown" class="dropdown-content w-100">
                    <input type="text" placeholder="Search.." id="myInput" onkeyup="filterFunction()">
                    <?PHP
                    $data = DB::select(DB::raw("CALL get_md_company();"));
                    $d = json_decode(json_encode($data), true);
                    foreach ($d as $row) {
                        $cin = $row['cin'];
                        $c_fullname = $row['c_shortname'];
                        $security_code = $row['security_code'];
                    ?>
                        <a onclick="loadInput('<?php echo $c_fullname ?>','<?php echo $cin ?>','<?php echo $security_code ?>')" href="#<?php echo $c_fullname ?>"><?php echo $c_fullname ?></a>
                    <?php
                    }
                    ?>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <label for="">File : </label>
            <select class="w-100" name="file_select" id="file_select" onchange="onFileChange()">
                <option value="0" selected disabled>select</option>
            </select>
        </div>
        <div class="col-md-3">
            <label for="xfer_select">Xfer date : </label>
            <select class="w-100" name="xfer_select" id="xfer_select" onchange="document.getElementById('getMultiDiv').disabled = false;">
                <option value="0" selected disabled>select</option>
            </select>
        </div>
        <div class="col-md-3">
            <label for="xfer_select"></label>
            <button type="button" class="btn btn-success thm-btn default-font w-100" id="getMultiDiv" onclick="get_data()" disabled> Go </button>
        </div>
    </div>
    <div class="col-md-12 process-div">
        <div class="col-md-3">

        </div>
        <div class="col-md-3">

        </div>
        <div class="col-md-3">

        </div>
        <div class="col-md-3">
            <label for="xfer_select"></label>
            <button type="button" class="btn btn-success  default-font w-100" id="process" onclick="process_data()" disabled> Process </button>
        </div>
    </div>
    <div class="col-md-12 mt-5">
        <div class="col-md-6 " id="search-responce-table">
            <div class="container rounded bg-white p-md-5">
                <!-- <div class="h2 font-weight-bold">Meetings</div> -->
                <div class="table-responsive">
                    <table class="table" id="companyNameTable">
                        <thead class="search-table-head">
                            <tr>
                                <th class="w-5" scope=" col">#</th>
                                <th scope="col">Security code</th>
                                <th scope="col">Company Name</th>
                                <th scope="col">Ex Date</th>
                                <th scope="col">Year</th>
                                <th scope="col">Purpose</th>
                                <th scope="col">Dividend per share</th>
                            </tr>
                        </thead>
                        <tbody id="dividenttable">

                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6" id="search-responce-table">
            <div class="container rounded bg-white p-md-5">
                <!-- <div class="h2 font-weight-bold">Meetings</div> -->
                <div class="table-responsive">
                    <table class="table" id="the-table2">
                        <thead class="search-table-head">
                            <tr>
                                <th scope="col">Name</th>
                                <th scope="col">Folio Number</th>
                                <th scope="col">Dividend Amount</th>
                                <th scope="col">DateOf Transfer</th>
                                <th scope="col">No.Of shares</th>
                            </tr>
                        </thead>
                        <tbody id="multipledivident">

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>


</div>

<script>
    document.getElementById("companyNameselect").addEventListener("click", myFunction);
    $('.process-div').hide();


    function filterFunction() {
        var input, filter, ul, li, a, i;
        input = document.getElementById("myInput");
        filter = input.value.toUpperCase();
        div = document.getElementById("myDropdown");
        a = div.getElementsByTagName("a");
        for (i = 0; i < a.length; i++) {
            txtValue = a[i].textContent || a[i].innerText;
            if (txtValue.toUpperCase().indexOf(filter) > -1) {
                a[i].style.display = "";
            } else {
                a[i].style.display = "none";
            }
        }
    }

    function myFunction() {
        document.getElementById("myDropdown").classList.toggle("show");
    }

    function loadInput(a, h, s) {

        document.getElementById("companyNameselect").value = a;
        // console.log(document.getElementById("companyNameselect").value);
        document.getElementById("cin_number").value = h;
        document.getElementById("security_code").value = s;
        document.getElementById("myDropdown").classList.toggle("show");

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var cin_number = document.getElementById('cin_number').value;
        var fileData = $.ajax({
            type: 'GET',
            url: "/getfilelist/" + cin_number,
            data: '',
            dataType: "JSON",
            success: function(resultData) {
                // console.log(resultData);
                let element = '<option value="0" selected disabled>--select--</option>';
                var fileSelect = document.getElementById('file_select');
                for (let i = 0; i < resultData.length; i++) {
                    element = element + '<option value="' + resultData[i].id + '">' + resultData[i].excel_name + '</option>';
                    fileSelect.innerHTML = element;
                }
            }
        });
        fileData.error(function() {
            alert("Something went wrong");
        });

        document.getElementById("xfer_select").innerHTML = '<option value="0" selected disabled>--select--</option>';
        document.getElementById('getMultiDiv').disabled = true;
    }

    function onFileChange() {
        var log_id = document.getElementById('file_select').value;
        var xferData = $.ajax({
            type: 'GET',
            url: "/getxferdate/" + log_id,
            data: '',
            dataType: "JSON",
            success: function(resultData) {
                // console.log(resultData);
                let element = '<option value="0" selected disabled>--select--</option>';
                var xferSelect = document.getElementById("xfer_select")
                for (let i = 0; i < resultData.length; i++) {
                    var yyyymmdd = resultData[i].proposeddateoftransfer == null ? resultData[i].proposeddateoftransfer : resultData[i].proposeddateoftransfer.split('-');
                    var ddmmyyyy = yyyymmdd == null ? '--' : yyyymmdd[2] + '-' + yyyymmdd[1] + '-' + yyyymmdd[0];
                    element = element + '<option value="' + resultData[i].proposeddateoftransfer + "," + resultData[i].D_No + '">' + ddmmyyyy + ' (' + resultData[i].D_No + ')</option>';
                    xferSelect.innerHTML = element;
                }
            }
        });
        xferData.error(function() {
            alert("Something went wrong");
        });
        document.getElementById('getMultiDiv').disabled = true;
    }


    const get_data = () => {
        $('.process-div').slideUp();
        $("#process").prop("disabled", false);
        data = "";
        var cin = document.getElementById("cin_number");
        var log_id = document.getElementById("file_select");
        var xferElement = document.getElementById("xfer_select");
        var security_code = document.getElementById("security_code");
        data = {
            cin: cin.value,
            security_code: security_code.value,
            log_id: log_id.value,
            xfer_date: xferElement.value.split(",")[0],
            division: xferElement.value.split(",")[1],
            skip: 0,
            take: 10
        };
        $("#getMultiDiv").html(
            '<span class="spinner-border spinner-border-sm-go" role="status" aria-hidden="true"></span>'
        );
        $("#getMultiDiv").prop("disabled", true);

        var Data = $.ajax({
            type: 'POST',
            // url: "<?php echo e(env('FLASK_API', '')); ?>" + "api/get-multidividend-data",
            url: "<?php echo e(url('/getMultipleDividendData')); ?>",
            data: JSON.stringify(data),
            contentType: 'application/json; charset=utf-8',
            dataType: "json",
            success: function(resultData) {
                console.log(resultData);
                $(document).ready(function() {
                    // console.log(resultData);
                    sessionStorage.setItem("multidevident_meta", JSON.stringify(resultData['meta']));
                    $("#dividenttable").html("");
                    if (resultData['dividend_master'].length > 0) {
                        for (i = 0; i < resultData['dividend_master'].length; i++) {
                            var year =
                                resultData['dividend_master'][i]["ex_date"] == null ?
                                resultData['dividend_master'][i]["ex_date"] :
                                resultData['dividend_master'][i]["ex_date"].split("-");
                            var newDate =
                                year == null ?
                                "--" :
                                year[2] +
                                "-" +
                                year[1] +
                                "-" +
                                year[0];

                            var html =
                                "<tr><td>" +
                                "<input type='radio' class='selectdividend' name='selectdividend' value='" +
                                resultData['dividend_master'][i]["id"] +
                                "/" +
                                resultData['dividend_master'][i]["c_fullname"] +
                                "/" +
                                resultData['dividend_master'][i]["dividend_amount"] +
                                "'></input>" +
                                "</td><td>" +
                                resultData['dividend_master'][i]["security_code"] +
                                "</td><td>" +
                                resultData['dividend_master'][i]["c_fullname"] +
                                "</td><td>" +
                                resultData['dividend_master'][i]["proposed_date"] +
                                "</td><td>" +
                                resultData['dividend_master'][i]["year"] +
                                "</td><td>" +
                                resultData['dividend_master'][i]["purpose"] +
                                "</td><td>" +
                                resultData['dividend_master'][i]["dividend_amount"]; +
                            "</td></tr>";
                            $("#dividenttable").append(html);

                            $("td")
                                .filter(function() {
                                    return this.innerHTML.match(
                                        /^[0-9\s\.,]+$/
                                    );
                                })
                                .css("text-align", "left");
                        }

                        $(".selectdividend").click(function() {
                            $('.process-div').slideDown();

                            var radioValue = $(
                                "input[name='selectdividend']:checked"
                            ).val();
                            data1 = radioValue;
                            if (radioValue) {
                                var dividendamount = radioValue;
                                var selecteddividend = radioValue;
                                var arr = selecteddividend.split("/");
                            }
                        });
                    } else {
                        $("#tablemsgdiv").show("slow");
                        $("#tablemsg").html(
                            "<p>No dividend data available :(</p>"
                        );
                    }
                    // =============================================================================================
                    if (resultData['multiple_dividend'].length === 0) {
                        $("#tablemsgdiv").show("slow");
                        $("#tablemsg").html(
                            "<p>No dividend data available :(</p>"
                        );
                    }
                    $("#multipledivident").html("");
                    if (resultData['multiple_dividend'].length > 0) {
                        for (i = 0; i < resultData['multiple_dividend'].length; i++) {
                            var year =
                                resultData['multiple_dividend'][i]["proposeddateoftransfer"] ==
                                null ?
                                resultData['multiple_dividend'][i][
                                    "proposeddateoftransfer"
                                ] :
                                resultData['multiple_dividend'][i][
                                    "proposeddateoftransfer"
                                ].split("-");
                            var newDate =
                                year == null ?
                                "--" :
                                year[2] +
                                "-" +
                                year[1] +
                                "-" +
                                year[0];

                            data2 = data2 + resultData['multiple_dividend'][i]["id"] + ",";
                            var html =
                                "<tr><td>" +
                                resultData['multiple_dividend'][i]["firstname"] +
                                " " +
                                resultData['multiple_dividend'][i]["middlename"] +
                                "</td><td>" +
                                resultData['multiple_dividend'][i]["folionumber"] +
                                "</td><td>" +
                                resultData['multiple_dividend'][i]["amounttransfered"] +
                                "</td><td>" +
                                newDate +
                                "</td><td>" +
                                "-" +
                                "</td></tr>";
                            $("#multipledivident").append(html);

                            $("td")
                                .filter(function() {
                                    return this.innerHTML.match(
                                        /^[0-9\s\.,]+$/
                                    );
                                })
                                .css("text-align", "left");
                        }
                    }
                    $(".selectdividend").click(function() {
                        var radioValue = $(
                            "input[name='selectdividend']:checked"
                        ).val();
                        var DIV = 0;
                        if (radioValue) {
                            var selecteddividend = radioValue;
                            var arr = selecteddividend.split("/");
                            DIV = arr[2];
                            comapanyname =
                                $("#companyNameselect").val();
                            dividendamount = DIV;
                        }
                        var $tbody = $("#multipledivident");
                        var trElements = $tbody.find("tr");
                        // Iterate over each tr element
                        trElements.each(function() {
                            var thirdTd = $(this).find("td:eq(2)").text(); // Get the text content of the third td element
                            // Convert the thirdTd to a number
                            var thirdTdValue = parseFloat(thirdTd);
                            var noOfSharesHTML = "-";
                            if (DIV !== 0) {
                                var noOfShares = thirdTdValue / DIV;
                                if (noOfShares % 1 !== 0) {
                                    noOfSharesHTML =
                                        '<span style="color:red;font-weight:bold;">' +
                                        String(noOfShares).replace(/(\.\d\d\d).+/, "$1") +
                                        "</span>";
                                } else {
                                    noOfSharesHTML = "<span>" + noOfShares + "</span>";
                                }
                            }
                            // Find the 6th td element and append the noOfSharesHTML
                            $(this).find("td:eq(4)").html(noOfSharesHTML);
                        });


                    });
                });
                $("#getMultiDiv").html("Go");
                $("#getMultiDiv").prop("disabled", false);
            }
        });
        Data.error(function() {
            $("#tablemsgdiv").show("slow");
            $("#tablemsg").html("<p>Something went wrong </p>");
            $("#getMultiDiv").html("Go");
            $("#getMultiDiv").prop("disabled", false);
        });
    }
    var $tbody = $("#multipledivident");
    var debounceTimer;
    var isLoadingData = false;
    $tbody.on("scroll resize", function() {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(function() {
            var isAtBottom = ($tbody.scrollTop() + $tbody.innerHeight() + 1) >= $tbody[0].scrollHeight;
            if (isAtBottom && !isLoadingData) {
                loadMoreData();
            }
        }, 200);
    });

    const loadMoreData = () => {
        var cin = document.getElementById("cin_number");
        var log_id = document.getElementById("file_select");
        var xferElement = document.getElementById("xfer_select");
        var security_code = document.getElementById("security_code");
        let localsession = JSON.parse(sessionStorage.getItem("multidevident_meta"));
        let data = {
            cin: cin.value,
            security_code: security_code.value,
            log_id: log_id.value,
            xfer_date: xferElement.value.split(",")[0],
            division: xferElement.value.split(",")[1],
            skip: localsession['skip'] + localsession['take'],
            take: localsession['take']
        };
        // Loader
        var $tbody = $("#multipledivident");
        $tbody.find(".removable-loader").remove();
        $tbody.append(`
        <tr class="removable-loader"><td colspan="5">
        <div class="text-center">
        <span class="spinner-border spinner-border-sm-load" role="status" aria-hidden="true"></span>
        </div>
        </td>
        </tr>`);
        var Data = $.ajax({
            type: 'POST',
            // url: "<?php echo e(env('FLASK_API', '')); ?>" + "api/get-multidividend-data",
            url: "<?php echo e(url('/getMultipleDividendData')); ?>",
            data: JSON.stringify(data),
            contentType: 'application/json; charset=utf-8',
            dataType: "json",
            success: function(resultData) {
                localsession['take'] = resultData['meta']['take'];
                localsession['skip'] = resultData['meta']['skip'];
                sessionStorage.setItem("multidevident_meta", JSON.stringify(localsession));
                // console.log(resultData);
                if (resultData['multiple_dividend'].length === 0) {
                    $("#tablemsgdiv").show("slow");
                    $("#tablemsg").html(
                        "<p>No dividend data available :(</p>"
                    );
                }
                var radioValue = $(
                    "input[name='selectdividend']:checked"
                ).val();
                var DIV = 0;
                if (radioValue) {
                    var selecteddividend = radioValue;
                    var arr = selecteddividend.split("/");
                    DIV = arr[2];
                    comapanyname =
                        $("#companyNameselect").val();
                    dividendamount = DIV;
                }
                if (resultData['multiple_dividend'].length > 0) {
                    for (i = 0; i < resultData['multiple_dividend'].length; i++) {
                        var year =
                            resultData['multiple_dividend'][i]["proposeddateoftransfer"] ==
                            null ?
                            resultData['multiple_dividend'][i][
                                "proposeddateoftransfer"
                            ] :
                            resultData['multiple_dividend'][i][
                                "proposeddateoftransfer"
                            ].split("-");
                        var newDate =
                            year == null ?
                            "--" :
                            year[2] +
                            "-" +
                            year[1] +
                            "-" +
                            year[0];

                        let noOfSharesHTML = "-";
                        if (DIV !== 0) {
                            let noOfShares = resultData['multiple_dividend'][i]["amounttransfered"] / DIV;
                            if (noOfShares % 1 !== 0) {
                                noOfSharesHTML =
                                    '<span style = "color:red;font-weight: bold;">' +
                                    String(noOfShares).replace(
                                        /(\.\d\d\d).+/,
                                        "$1"
                                    ) +
                                    "</span>";
                            } else {
                                noOfSharesHTML =
                                    "<span>" +
                                    noOfShares +
                                    "</span>";
                            }
                        }

                        data2 = data2 + resultData['multiple_dividend'][i]["id"] + ",";
                        var html =
                            "<tr><td>" +
                            resultData['multiple_dividend'][i]["firstname"] +
                            " " +
                            resultData['multiple_dividend'][i]["middlename"] +
                            "</td><td>" +
                            resultData['multiple_dividend'][i]["folionumber"] +
                            "</td><td>" +
                            resultData['multiple_dividend'][i]["amounttransfered"] +
                            "</td><td>" +
                            newDate +
                            "</td><td>" +
                            (noOfSharesHTML ?? "-") +
                            "</td></tr>";
                        $("#multipledivident").append(html);
                        $tbody.find(".removable-loader").remove();
                        $("td")
                            .filter(function() {
                                return this.innerHTML.match(
                                    /^[0-9\s\.,]+$/
                                );
                            })
                            .css("text-align", "left");
                    }
                }
            }
        });
        Data.error(function() {
            $("#tablemsgdiv").show("slow");
            $("#tablemsg").html("<p>Something went wrong </p>");
        });
    }
    const process_data = () => {
        let local_session = JSON.parse(sessionStorage.getItem("multidevident_meta"));
        var iepf_row_ids = local_session['id_list'];
        var cin = document.getElementById("cin_number");
        var log_id = document.getElementById("file_select");
        var xferElement = document.getElementById("xfer_select");
        var security_code = document.getElementById("security_code");
        let localsession = JSON.parse(sessionStorage.getItem("multidevident_meta"));
        let dividend_id = $(
            "input[name='selectdividend']:checked"
        ).val().split('/')[0];
        let data = {
            'cin': cin.value,
            'log_id': log_id.value,
            'xfer_date': xferElement.value.split(",")[0],
            'division': xferElement.value.split(",")[1],
            'dividend_id': dividend_id
        };
        $("#process").html(
            '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>'
        );
        $("#process").prop("disabled", true);
        $("#getMultiDiv").prop("disabled", true);
        var Data = $.ajax({
            type: 'POST',
            // url: "<?php echo e(env('FLASK_API', '')); ?>" + "api/multiple-dividend",
            url: "<?php echo e(url('/processMultiDividend')); ?>",
            data: JSON.stringify(data),
            contentType: 'application/json; charset=utf-8',
            dataType: "json",
            success: function(resultData) {
                $("#tablemsgdiv").show("slow");
                $("#tablemsg").html("<p>Data added Successfully </p>");
                setTimeout(function() {
                    location.reload(true);
                }, 3000);
                $("#process").html("Process");
                $("#process").prop("disabled", false);
                $("#getMultiDiv").prop("disabled", false);
            }
        });
        Data.error(function() {
            $("#tablemsgdiv").show("slow");
            $("#tablemsg").html("<p>Something went wrong </p>");
            $("#process").html("Process");
            $("#process").prop("disabled", false);
            $("#getMultiDiv").prop("disabled", false);
        });



    }
</script>
<style>
    @import  url('https://fonts.googleapis.com/css2?family=PT+Sans:wght@700&family=Poppins:wght@600&display=swap');

    .spinner-border-sm-load {
        width: 3rem;
        height: 3rem;
        border-width: 0.4em;
    }

    .spinner-border-sm-go {
        width: 2rem;
        height: 2rem;
        border-width: 0.3em;
    }

    .default-font {
        font-size: 14px;
    }

    #companyNameselect {
        width: 120%;
        height: 25px;
    }

    .w-5 {
        width: 5% !important;
    }

    .thm-btn {
        padding-left: 16px;
        padding-right: 16px;
        color: white;
        background-color: rgb(116, 124, 0);
        border: rgb(116, 124, 0);
    }

    .thm-btn:disabled {
        padding-left: 16px;
        padding-right: 16px;
        color: white;
        background-color: rgb(116, 124, 0);
        border: rgb(116, 124, 0);
    }

    table th:first-child {
        border-radius: 10px 0 0 0;
    }

    table th:last-child {
        border-radius: 0 10px 0 0;
    }

    .dropbtn {
        background-color: #04AA6D;
        color: white;
        padding: 16px;
        font-size: 16px;
        border: none;
        cursor: pointer;
    }

    .dropbtn:hover,
    .dropbtn:focus {
        background-color: #3e8e41;
    }

    #myInput {
        box-sizing: border-box;
        background-image: url('searchicon.png');
        background-position: 14px 12px;
        background-repeat: no-repeat;
        font-size: 10px;
        padding: 2px 0px 4px 15px;
        border: none;
        /* border-bottom: 1px solid #ddd; */
    }

    #xInput {
        box-sizing: border-box;
        background-position: 14px 12px;
        background-repeat: no-repeat;
        font-size: 10px;
        padding: 2px 0px 4px 15px;
        border: none;
        border-bottom: 1px solid #ddd;
    }

    #myInput:focus {
        outline: 3px solid #ddd;
    }

    .dropdown {
        position: relative;
        display: inline-block;
    }

    .dropdown-content {
        display: none;
        position: absolute;
        background-color: #f6f6f6;
        /* min-width: 230px; */
        overflow: auto;
        border: 1px solid #ddd;
        z-index: 1;
    }

    .dropdown-content a {
        color: black;
        padding: 12px 16px;
        text-decoration: none;
        display: block;
    }

    .dropdown a:hover {
        background-color: #ddd;
    }

    .show {
        display: block;
    }


    table th:first-child {
        border-radius: 5px 0 0 0;
    }

    table th:last-child {
        border-radius: 0 5px 0 0;
    }

    .w-30 {
        width: 30%;
    }

    .testing {
        background-color: #e9c10f !important;
    }

    .white {
        color: red;
    }

    .export-button {
        background-color: lightsteelblue !important;
    }

    input[type="text"],
    input[type="number"] {
        width: 100%;
        height: calc(3em + 3px);
        margin: 0 0 1em;
        padding: 1em;
        /* border: 1px solid #ccc; */
        border: none;
        background: #fff;
        resize: none;
        outline: none;
        border-radius: 5px;
        /* background-color: #EBF5FB;
         */
        background-color: whitesmoke;

    }

    input[type="text"][required]:focus,
    input[type="number"][required]:focus {
        border-color: #e9c10f;
    }

    input[type="text"][required]:focus+label[placeholder]:before,
    input[type="number"][required]:focus+label[placeholder]:before {
        color: #e9c10f;
    }

    input[type="text"][required]:focus+label[placeholder]:before,
    input[type="number"][required]:focus+label[placeholder]:before,
    input[type="text"][required]:valid+label[placeholder]:before,
    input[type="number"][required]:valid+label[placeholder]:before {
        transition-duration: .2s;
        transform: translate(0, -1.5em) scale(0.9, 0.9);
    }

    input[type="text"][required]:invalid+label[placeholder][alt]:before,
    input[type="number"][required]:invalid+label[placeholder][alt]:before {
        content: attr(alt);
    }

    input[type="text"][required]+label[placeholder],
    input[type="number"][required]+label[placeholder] {
        display: block;
        pointer-events: none;
        line-height: 1em;
        margin-top: calc(-3em - 2px);
        margin-bottom: calc((3em - 1em) + 2px);
    }

    input[type="text"][required]+label[placeholder]:before,
    input[type="number"][required]+label[placeholder]:before {
        content: attr(placeholder);
        display: inline-block;
        margin: 0 calc(1em + 2px);
        padding: 0 2px;
        color: #898989;
        white-space: nowrap;
        transition: 0.3s ease-in-out;
        background-image: linear-gradient(to bottom, #fff, #fff);
        background-size: 100% 5px;
        background-repeat: no-repeat;
        background-position: center;
    }

    * {
        box-sizing: border-box
    }

    body {
        /* background-color: #d9ecf2; */
        font-family: 'Poppins', sans-serif;
        color: #666
    }

    .h2 {
        color: #444;
        font-family: 'PT Sans', sans-serif
    }

    thead {
        font-family: 'Poppins', sans-serif;
        font-weight: bolder;
        font-size: 10px;
        color: #3e3e3e
    }

    .name {
        display: inline-block
    }

    .bg-blue {
        /* background-color: #EBF5FB; */
        background-color: whitesmoke;
        border-radius: 8px
    }

    .fa-check,
    .fa-minus {
        color: blue
    }

    .bg-blue:hover .fa-check,
    .bg-blue:hover .fa-minus {
        background-color: #3e64ff;
        color: #eee
    }

    .table thead th,
    .table td {
        border: none
    }

    .table tbody td:first-child {
        border-bottom-left-radius: 10px;
        border-top-left-radius: 10px
    }

    .table tbody td:last-child {
        border-bottom-right-radius: 10px;
        border-top-right-radius: 10px
    }

    #spacing-row {
        height: 10px
    }

    @media(max-width:575px) {
        .container {
            width: 125%;
            padding: 20px 10px
        }
    }

    .fs-5 {
        font-size: 1.3rem !important;
    }

    .advanced-seaech-check {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* .search-table-head {
        background-color: #a7a7a7;
        color: #3a3a3a;
    } */


    tbody {
        display: block;
        height: 450px;
        overflow: auto;
        font-size: 10px !important;
    }

    thead,
    tbody tr {
        display: table;
        width: 100%;
        table-layout: fixed;
        /* even columns width , fix width of table too*/
    }

    thead {
        width: calc(100% - 1em)
            /* scrollbar is average 1em/16px width, remove it from thead width */
    }

    table {
        width: 100%;
    }

    .table> :not(:first-child) {
        /* border-top: 2px solid currentColor; */
        border: none;
    }
</style>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/srikanth2k23/public_html/resources/views/multidiv.blade.php ENDPATH**/ ?>