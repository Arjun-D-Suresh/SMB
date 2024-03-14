@extends('layouts.app')

@section('content')


<style>
    .table-head {
        background: #695800;
        color: #fff;
        font-family: 'Raleway', sans-serif;
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
</style>
<div>
    <div class="container fs-5">
        <div class="row">
            <div class="col-md-12">
                <form>
                    <div class="row">
                        <div class="col-12 row">
                            <div class="mb-3 col-3">
                                <label>Name</label>
                                <input id="investor_name" type="text" class="form-control bg-light fs-3">
                            </div>
                            <div class="mb-3 col-3">
                                <label>Father Name</label>
                                <input id="fs_name" type="text" class="form-control bg-light fs-3">
                            </div>
                            <div class="mb-3 col-3">
                                <label>Address</label>
                                <input id="address" type="text" class="form-control bg-light fs-3">
                            </div>
                            <div class="mb-3 col-3">
                                <label>Country</label>
                                <input id="country" type="text" class="form-control bg-light fs-3">
                            </div>
                            <div class="mb-3 col-3">
                                <label>State</label>
                                <input id="state" type="text" class="form-control bg-light fs-3">
                            </div>
                            <div class="mb-3 col-3">
                                <label>City</label>
                                <input id="district" type="text" class="form-control bg-light fs-3">
                            </div>
                            <div class="mb-3 col-3">
                                <label>Pincode</label>
                                <input id="pincode" type="text" class="form-control bg-light fs-3">
                            </div>
                            <!-- <div class="mb-3 col-3">
                                    <label>DOB</label>
                                    <input id="dob" type="date" class="form-control bg-light fs-3">
                                </div> -->
                            <div class="mb-3 col-3 advanced-search">
                                <label>Year</label>
                                <input id="year" type="number" class="form-control bg-light fs-3">
                            </div>
                            <!-- <div class="mb-3 col-3 advanced-search">
                                <label>CIN</label>
                                <input id="cin" type="text" class="form-control bg-light fs-3">
                            </div> -->
                            <!-- <div class="mb-3 col-3 advanced-search">
                                <label>PAN</label>
                                <input id="pan" type="text" class="form-control bg-light fs-3">
                            </div> -->
                            <!-- <div class="mb-3 col-3 advanced-search">
                                <label>ADHAAR</label>
                                <input id="aadhaar" type="text" class="form-control bg-light fs-3">
                            </div> -->
                            <div class="mb-3 col-3 advanced-search">
                                <label>Share value(min)</label>
                                <input id="min_share_value" id="min-share-value" type="number" class="form-control bg-light fs-3">
                            </div>
                            <div class="mb-3 col-3 advanced-search">
                                <label>Share value(max)</label>
                                <input id="max_share_value" id="max-share-value" type="number" class="form-control bg-light fs-3">
                            </div>
                        </div>
                        <div class="col-12 row justify-content-end">
                            <div class="mb-3 col-3">
                                <button id="reset-investor-search" type="button" class="btn btn btn-outline-danger form-control">
                                    Reset
                                </button>
                            </div>
                            <div class="mb-3 col-3">
                                <button id="advanced-investor-search" type="button" class="btn btn btn-outline-secondary form-control">
                                    Advanced Search
                                </button>
                            </div>
                            <div class="mb-3 col-3">
                                <button id="investor-search-btn" type="button" class="btn btn-primary form-control">
                                    Search
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
                <div class="table-responsive" style="overflow-y: auto;height: 100%;width: 100%;">

                    <table id="investor-search-table" class="table">
                        <thead class="thead-dark table-head">
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Name</th>
                                <th scope="col">Father Name</th>
                                <th class="w-30" scope="col">Address</th>
                                <th scope="col">Country</th>
                                <!-- <th scope="col">State</th> -->
                                <!-- <th scope="col">City</th> -->
                                <th scope="col">Pincode</th>
                                <!-- <th scope="col">DOB</th> -->
                                <th scope="col">Year</th>
                                <th scope="col">Company Name</th>
                                <th scope="col">Folio Number</th>
                                <!-- <th scope="col">CIN</th> -->
                                <!-- <th scope="col">PAN</th> -->
                                <!-- <th scope="col">Adhaar</th> -->
                                <th scope="col">Total Asset Value</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        $('.advanced-search').hide();

        var searchTable = $('#investor-search-table').DataTable({
            dom: 'Bfrtip',
            buttons: [
                'csv', 'excel'
            ],
            dom: 'Bfrtip',
            // ajax: "{{ url('/investor-search') }}",
            columns: [{
                    data: "row_num"
                },
                {
                    data: "investor_name"
                },
                {
                    data: "fs_name"
                },
                {
                    data: "address"
                },
                {
                    data: "country"
                },
                // {
                //     data: "state"
                // },
                // {
                //     data: "district"
                // },
                {
                    data: "pincode"
                },
                // {
                //     data: "dob"
                // },
                {
                    data: "year"
                },
                {
                    data: "CompanyName"
                },
                {
                    data: "folio_number"
                },
                // {
                //     data: "cin"
                // },
                // {
                //     data: "pan"
                // },
                // {
                //     data: "aadhaar"
                // },
                {
                    data: "share_value"
                },
            ],
            pagingType: 'full_numbers',
            searching: false,
        });

        const getInvestorDetails = () => {
            searchTable.clear().draw(true);
            $('#reset-investor-search').prop('disabled', true);
            $('#investor-search-btn').prop('disabled', true);
            $('#investor-search-btn').html(`
                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    Searching...
                `);
            var request = $.ajax({
                url: "{{ url('/investor-search') }}",
                method: "GET",
                data: {
                    investor_name: $('#investor_name').val(), //.replace(/ /g, "").split("").join("%"),
                    fs_name: $('#fs_name').val(), //.replace(/ /g, "").split("").join("%"),
                    address: $('#address').val(), //.replace(/ /g, "").split("").join("%"),
                    country: $('#country').val(), //.replace(/ /g, "").split("").join("%"),
                    state: $('#state').val(), //.replace(/ /g, "").split("").join("%"),
                    district: $('#district').val(), //.replace(/ /g, "").split("").join("%"),
                    pincode: $('#pincode').val(), //.replace(/ /g, "").split("").join("%"),
                    // dob: $('#dob').val().replace(/ /g, ""),
                    year: $('#year').val().replace(/ /g, ""),
                    // cin: $('#cin').val().replace(/ /g, "").split("").join("%"),
                    // pan: $('#pan').val().replace(/ /g, "").split("").join("%"),
                    // aadhaar: $('#aadhaar').val().replace(/ /g, "").split("").join("%"),
                    min_share_value: $('#min_share_value').val().replace(/ /g, ""),
                    max_share_value: $('#max_share_value').val().replace(/ /g, ""),
			  skip: 0,
                    take: 20
                },
                dataType: "JSON"
            });

            request.done(function(response) {
                searchTable.rows.add(response.data).draw(false);

                $('#reset-investor-search').prop('disabled', false);
                $('#investor-search-btn').prop('disabled', false);
                $('#investor-search-btn').html('Search');
            });
        }

 	$('#investor-search-table').on('page.dt', function () {
                var info = searchTable.page.info();
                console.log( 'Showing page: '+info.page+' of '+info.pages );

                let skip = (info.pages * 10);

                if(info.page === info.pages - 1){

                    var request = $.ajax({
                                url: "{{ url('/investor-search') }}",
                                method: "GET",
                                data: {
                                    investor_name: $('#investor_name').val(), //.replace(/ /g, "").split("").join("%"),
                    			fs_name: $('#fs_name').val(), //.replace(/ /g, "").split("").join("%"),
                    			address: $('#address').val(), //.replace(/ /g, "").split("").join("%"),
                    			country: $('#country').val(), //.replace(/ /g, "").split("").join("%"),
                    			state: $('#state').val(), //.replace(/ /g, "").split("").join("%"),
                    			district: $('#district').val(), //.replace(/ /g, "").split("").join("%"),
                    			pincode: $('#pincode').val(), //.replace(/ /g, "").split("").join("%"),
                    			// dob: $('#dob').val().replace(/ /g, ""),
                    			year: $('#year').val().replace(/ /g, ""),
                    			// cin: $('#cin').val().replace(/ /g, "").split("").join("%"),
                    			// pan: $('#pan').val().replace(/ /g, "").split("").join("%"),
                    			// aadhaar: $('#aadhaar').val().replace(/ /g, "").split("").join("%"),
                    			min_share_value: $('#min_share_value').val().replace(/ /g, ""),
                    			max_share_value: $('#max_share_value').val().replace(/ /g, ""),
                                    skip: skip,
                                    take: 10
                                },
                                dataType: "JSON"
                            });
    
                    request.done(function(response) {

                        for (const row of response.data) {
                            row.row_num += skip;
                        }

                        searchTable.rows.add(response.data).draw(false);
    
                        $('#reset-investor-search').prop('disabled', false);
                        $('#investor-search-btn').prop('disabled', false);
                        $('#investor-search-btn').html('Search');
                    });
                }
            });


        $('#investor-search-btn').click(function() {
            getInvestorDetails();
        });

        $('#advanced-investor-search').click((event) => {
            $('#cin').val('');
            $('#pan').val('');
            $('#aadhaar').val('');
            $('#min_share_value').val('');
            $('#max_share_value').val('');
            $('.advanced-search').slideToggle();
        });

        $('#reset-investor-search').click((event) => {
            $('#investor_name').val('');
            $('#fs_name').val('');
            $('#address').val('');
            $('#country').val('');
            $('#state').val('');
            $('#district').val('');
            $('#pincode').val('');
            $('#dob').val('');
            $('#year').val('');
            $('#cin').val('');
            $('#pan').val('');
            $('#aadhaar').val('');
            $('#min_share_value').val('');
            $('#max_share_value').val('');
            $('#investor-search-btn').click();
        });

    });
</script>
@endsection