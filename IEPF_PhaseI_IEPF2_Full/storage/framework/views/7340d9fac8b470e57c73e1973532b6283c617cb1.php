

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
    <div class="row">
        <label class="col-md-1" for="selecteddata">Company :</label>
        

        <div class="dropdown col-md-2">
            <input style=" width: 96%;" id="companyNameselect" name="company_name" autocomplete="off" readonly>
            <!--  -->
            <input type="hidden" name="cin_number" id="cin_number" value="">
            <!--  -->
            <input type="hidden" id="security_code" value="">


            <!--  -->
            <div id="myDropdown" class="dropdown-content">
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
        <div class="col-md-2">
                <label for="file_select">file : </label>
                <select name="file_select" id="file_select" onchange="onFileChange()" style="width: 85%;">
                    <option value="0" selected disabled>--select--</option>
                </select>
        </div>
        <div class="col-md-3">
                <label for="xfer_select">xfer date : </label>
                <select name="xfer_select" id="xfer_select" onchange="document.getElementById('getMultiDiv').disabled = false;" style="width: 60%;">
                    <option value="0" selected disabled>--select--</option>
                </select>
        </div>
        <div class="col-md-1" id="processBtn">
            <button style="padding-bottom: 0px;" type="button" class="btn btn-success thm-btn default-font" id="getMultiDiv" disabled> Go </button>
        </div> 
        <div class="col-md-1"></div>
        
    </div>
     
    <br />
    <br /><br />
    <div class="row">
        <div class="col-md-6">
            <div class="table-responsive fixTableHead" style="height:400px;width:100%;">
                <table class="table" id="companyNameTable">
                    <thead class="thead-dark">
                        <tr>
                            <th scope=" col">#</th>
                            <th scope="col">Security code</th>
                            <th scope="col">Company Name</th>
                            <th scope="col">Ex Date</th>
                            <th scope="col">Year</th>
                            <th scope="col">Purpose</th>
                            <th scope="col">Dividend per share</th>
                        </tr>
                    </thead>
                    <tbody class="dividendtable" id="dividenttable" style="overflow-y: auto;"></tbody>
                </table>
            </div>
            <div class="row justify-content-center">
                <div class="col-md-12" id="processBtn">
                    <button type="button" class="btn btn-success thm-btn default-font" id="btnupload">Process</button>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="table-responsive fixTableHead" style="height:400px;width:100%;">
                <table class="table" id="the-table2">
                    <thead class="thead-dark">
                        <tr>
                            <th scope="col">Name</th>
                            <th scope="col">Folio Number</th>
                            <th scope="col">Dividend Amount</th>
                            <th scope="col">DateOf Transfer</th>
                            <th scope="col">No.Of shares</th>
                        </tr>
                    </thead>
                    <tbody class="dividendtable" id="multipledivident" style="overflow-y: auto;"></tbody>
                </table>
            </div>
            <div class="row justify-content-center">
                <div class="col-md-12">

                    <button type="button" class="btn btn-danger default-font" id="deletemembersdata">Purge</button>
                    <p id="Purge_s"></p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById("companyNameselect").addEventListener("click", myFunction);

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
        console.log(document.getElementById("companyNameselect").value);
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
        url: "/getfilelist/"+cin_number,
        data: '',
        dataType: "JSON",
        success: function (resultData) {
            console.log(resultData);
            let element = '<option value="0" selected disabled>--select--</option>';
            var fileSelect = document.getElementById('file_select');
            for (let i = 0; i < resultData.length; i++) {
                element = element + '<option value="'+resultData[i].id+'">'+resultData[i].excel_name+'</option>';
                fileSelect.innerHTML = element;
            }
        }
        });
        fileData.error(function () {
            alert("Something went wrong"); 
        });

        document.getElementById("xfer_select").innerHTML = '<option value="0" selected disabled>--select--</option>';
        document.getElementById('getMultiDiv').disabled = true;
    }

    function onFileChange(){
        var log_id = document.getElementById('file_select').value;
        var xferData = $.ajax({
        type: 'GET',
        url: "/getxferdate/"+log_id,
        data: '',
        dataType: "JSON",
        success: function (resultData) {
            console.log(resultData);
            let element = '<option value="0" selected disabled>--select--</option>';
            var xferSelect = document.getElementById("xfer_select")
            for (let i = 0; i < resultData.length; i++) {
                var yyyymmdd = resultData[i].proposeddateoftransfer == null ? resultData[i].proposeddateoftransfer : resultData[i].proposeddateoftransfer.split('-');
                var ddmmyyyy = yyyymmdd == null ? '--' : yyyymmdd[2] + '-' + yyyymmdd[1] + '-' + yyyymmdd[0];
                element = element + '<option value="'+resultData[i].proposeddateoftransfer+","+resultData[i].D_No+'">'+ddmmyyyy+' ('+resultData[i].D_No+')</option>';
                xferSelect.innerHTML = element;
            }
        }
        });
        xferData.error(function () {
            alert("Something went wrong"); 
        });
        document.getElementById('getMultiDiv').disabled = true;
    }
</script>
<style>
    .default-font{
        font-size:14px;
    }
    #companyNameselect {
        width: 120%;
        height: 25px;
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
    
</style>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\jiyuuSEVEN\GitHub\IEPF\resources\views\multidiv.blade.php ENDPATH**/ ?>