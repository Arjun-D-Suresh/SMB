@extends('layouts.app')

@section('content')
<div >
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <input type="text" id="logSearchInput" onkeyup="logSearch()" placeholder="Search for names.." title="Type in a name">
                <br>
                <div class="table-responsive fixTableHead" style="overflow-y: auto;height:100%;width:100%;">
                    
                    <table class="table" id="excellog">
                        <thead class="thead-dark">
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Excel</th>
                                <th scope="col">Type</th>
                                <th scope="col">Uploaded At</th>
                                <th scope="col">Uploaded By</th>
                                <th scope="col">Rows Processed</th>
                                <th scope="col">Message</th>
                                <th scope="col"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data as $d)
                                <tr>
                                    <td>{{ $d->row_num }}</td>
                                    <td>{{ $d->excel_name }}</td>
                                    <td>{{ $d->type }}</td>
                                    <td>{{ $d->uploadedat }}</td>
                                    <td>{{ $d->usertype }}</td>
                                    <td>{{ $d->dataprocessed }}</td>
                                    <td>{{ $d->file_type }}</td>
                                    <td>
                                        <!-- <form method="post" enctype="multipart/form-data"  >
                                            @csrf
                                            <div class="form-group">
                                                <input type="hidden" name="id" value="<?php echo $d->id ?>"/>
                                                <input type="hidden" name="excel" value="<?php echo $d->excel_name ?>"/>
                                            </div>
                                            
                                        </form> -->
                                        <button value="<?php echo $d->id ?>/<?php echo $d->excel_name ?>"  class="btn btn-success thm-btn excel-download" {{$d->dataprocessed == 0 ? "disabled" : ""}}>download</button>
                                        
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div id="dvjson"></div>
    <!-- <?php print_r(get_loaded_extensions());?> -->
</div>
<style>
    #logSearchInput{
        margin-bottom: 2%;
    }

    table th:first-child {
        border-radius: 10px 0 0 0;
    }

    table th:last-child {
        border-radius: 0 10px 0 0;
    }

    .thm-btn {
        padding-left: 16px;
        padding-right: 16px;
        color: white;
        background-color: rgb(116, 124, 0);
        border: rgb(116, 124, 0);
    }

    .thm-btn:hover {
        padding-left: 16px;
        padding-right: 16px;
        color: white;
        background-color: rgb(90, 98, 0);
        border: rgb(116, 124, 0);
    }
</style>
<script>
    function logSearch() {
  var input, filter, table, tr, td, tdRow, i, txtValue, flag;
  input = document.getElementById("logSearchInput");
  filter = input.value.toUpperCase();
  table = document.getElementById("excellog");
  tr = table.getElementsByTagName("tr");
  for (i = 1; i < tr.length; i++) {
    //
    tdRow = tr[i].getElementsByTagName("td");
    flag = 0;
    for (j = 0; j < tdRow.length; j++) {
        td = tr[i].getElementsByTagName("td")[j];
        console.log(td);
        if (td) {
            txtValue = td.textContent || td.innerText;
            if (txtValue.toUpperCase().indexOf(filter) > -1) {
                flag = 1;
            }
        }
    }
    //
    if (flag == 0){
        tr[i].style.display = "none";
    }else{
        tr[i].style.display = "";
    }
           
  }
}
</script>
@endsection