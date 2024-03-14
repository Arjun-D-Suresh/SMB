

<?php $__env->startSection('content'); ?>
    <div class="container" >
        <div class="input-group mb-3" style="width: 20%;">
            
            <input id="folioSearch" type="text" class="form-control" value="">
            <span id="folioSearchSpn" class="input-group-text dropbtn" >Search</span>
        </div>

        <div>
            <div>
                <div id="folioHeaderTableDiv" class="table-responsive fixTableHead" style="overflow-y: auto;height:250px;width:100%;">
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
                        <tbody class="dividendtable" id="folioHeaderTableBody" ></tbody>
                    </table>
                    <div id="noData">

                    </div>
                </div>    
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
                        <tbody class="dividendtable" id="folioDividendTableBody" ></tbody>
                    </table>
                    <div id="noDividendData">

                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        #folioSearch, span{
            font-size:15px;
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
    </style>

    <script>
        var base_url = window.location.origin+'/';
        $(document).ready(function () {

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('#noData').html('<div style="padding: 70px 0;text-align: center;"><p>No data available</p></div>');
            $('#noDividendData').html('<div style="padding: 70px 0;text-align: center;"><p>No data available</p></div>');
            let timeout = null;
            $("#folioSearch").keyup(function () { 
                clearTimeout(timeout);

                timeout = setTimeout(function () {
                    searchFunction();
                }, 1000); 
            }); 
            
            let searchFunction=()=>{
                let folioSearch = $("#folioSearch").val();
                
                let html = '';
                $('#noData').html('');
                $('#folioHeaderTableBody').html(html);
                $('#folioSearchSpn').html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');

                let data = { search : folioSearch}
                var saveData = $.ajax({
                type: 'GET',
                url: base_url+"folio-header-data"+"/"+folioSearch,
                data: data,
                dataType: "text",
                success: function (resultData) {
                        let data = JSON.parse(resultData);
                        for(let i = 0; i < data.length; i++){
                            html = html + '<tr>'+
                                '<td><input type="radio" value="'+data[i].urn+'" name="urnSelect" class="urnSelect"/></td>'+
                                '<td>'+data[i].urn+'</td>'+
                                '<td>'+data[i].cin+'</td>'+
                                '<td>'+data[i].investor_name+'</td>'+
                                '<td>'+data[i].fs_name+'</td>'+
                                '<td>'+data[i].address+'</td>'+
                                '<td>'+data[i].country+'</td>'+
                                '<td>'+data[i].state+'</td>'+
                                '<td>'+data[i].district+'</td>'+
                                '<td>'+data[i].pincode+'</td>'+
                                '<td>'+data[i].nof_shares+'</td>'+
                                '<td>'+data[i].folio_status+'</td>'+
                                '<td>'+(data[i].pan == null? '':data[i].pan)+'</td>'+
                                '<td>'+(data[i].aadhaar == null? '':data[i].aadhaar)+'</td>'+
                                '<td>'+(data[i].dob == null? '':data[i].dob)+'</td>'+
                                '<td>'+(data[i].iepf_claimed == 0? 'No':'Yes')+'</td>'+
                                '<td>'+(data[i].ucd_claimed == 0? 'No':'Yes')+'</td>'+
                                '<td>'+(data[i].joint_holder_name == null? '':data[i].joint_holder_name)+'</td>'+
                            '</tr>';
                        }
                        $('#folioHeaderTableBody').html(html);

                        $('.urnSelect').click(function(){

                            let folio = {urn : $(this).val()};
                            var saveData = $.ajax({
                            type: 'GET',
                            url: base_url+"folio-dividend-data"+"/"+$(this).val(),
                            data: folio,
                            dataType: "text",
                            success: function (resultDividendData) {
                                let dividendHTML = '';
                                $('#noDividendData').html('');
                                $('#folioDividendTableBody').html(dividendHTML);
                                let dividendData = JSON.parse(resultDividendData);
                                for(let i = 0; i < dividendData.length; i++){
                                    dividendHTML = dividendHTML + '<tr>'+
                                        '<td>'+(i+1)+'</td>'+
                                        '<td>'+dividendData[i].urn_key+'</td>'+
                                        '<td>'+dividendData[i].cin+'</td>'+
                                        '<td>'+dividendData[i].dividend_amount+'</td>'+
                                        '<td>'+dividendData[i].pd_of_xfer+'</td>'+
                                        '<td>'+dividendData[i].purpose+'</td>'+
                                    '</tr>';
                                }
                                $('#folioDividendTableBody').html(dividendHTML);
                                if(dividendData.length < 1){
                                    $('#noDividendData').html('<div style="padding: 70px 0;text-align: center;"><p>No data available</p></div>');
                                }
                            }
                            });
                        });

                        if(data.length < 1){
                            $('#noData').html('<div style="padding: 70px 0;text-align: center;"><p>No data available</p></div>');
                        }
                        $('#folioSearchSpn').html('Search');
                    }
                });
                saveData.error(function () { alert("Something went wrong"); $('#noData').html('<div style="padding: 70px 0;text-align: center;"><p>No data available</p></div>');$('#folioSearchSpn').html('Search');});
            }

        });
    </script>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\jiyuuSEVEN\GitHub\IEPF\resources\views\folioDetails.blade.php ENDPATH**/ ?>