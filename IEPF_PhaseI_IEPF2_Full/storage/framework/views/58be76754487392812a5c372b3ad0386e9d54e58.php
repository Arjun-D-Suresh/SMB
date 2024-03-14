

<?php $__env->startSection('content'); ?>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header upload-head">Upload Excel</div>

                <div class="card-body">
                    <form id="uploadForm" method="post"  enctype="multipart/form-data"  action="<?php echo e(url('/import')); ?>">
                        <?php echo csrf_field(); ?>
                        <div class="form-group">
                            <div class="custom-select" style="width: 30%; font-size:10px">
                                <select name="fileType" id="fileType" style="width: 63%;height: 25px;font-size: 12px;">
                                    <option value="IEPF1">IEPF1</option>
                                    <option value="IEPF2" selected>IEPF2</option>
                                    <option value="IEPF4">IEPF4</option>
                                    <option value="IEPF7">IEPF7</option>
                                    <option value="DIVIDEND">DIVIDEND</option>
                                    <option value="BONUS">BONUS</option>
                                    <option value="STOCK">STOCK</option>\
                                    <option value="COMPANY">COMPANY</option>
                                    <!-- <option value="BONUS"></option> -->
                                </select>

                            </div>
                            <br />
                            <label for="exampleFormControlFile1"></label>
                            <input id ="file" type="file" name="file" class="form-control-file">
                        </div>

                        <br>
                        <div class="row">
                            <div class="col-md-2">
                                <button type="submit" id="upload_btn" onclick= "Loader()" class="btn btn-primary thm-btn default-font" style="padding-left: 16px;padding-right: 16px;">Upload</button>
                            </div>
                            <div id="loadergif" class="col-md-1">
                                <img src="../../storage/loader1.gif" alt="Loader" style="width:30px; height:30px;">
                            </div>
                            <div class="col-md-8"></div>
                        </div>
                    </form>
                    <br />
                    <div id="textIEPF" style="display:none;">
                        <p class="alert alert-danger" role="alert">Please! Only upload IEPF2.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- <?php if( (session()->has('errmessage')) || (session()->has('message')) ): ?>
    <div>

    </div>

    <?php else: ?>
    <div>
        <p>nooooo</p>
    </div>
    <?php endif; ?> -->
    <?php if(session()->has('errmessage')): ?>
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="alert alert-danger">
                <?php echo e(session()->get('errmessage')); ?>

            </div>
        </div>
    </div>
    <?php endif; ?>
    <?php if(session()->has('message')): ?>
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="alert alert-success">
                <?php echo e(session()->get('message')); ?>

            </div>
        </div>
    </div>
    <?php endif; ?>

</div>
<script>
    $("#fileType").change(function() {

        var selected = $('#fileType').val();
        // if (selected == "IEPF2") {
        //     $(':input[type="submit"]').prop('disabled', false);
        //     $('#textIEPF').css("display", "none");


        // } else {
        //     $(':input[type="submit"]').prop('disabled', true);
        //     $('#textIEPF').css("display", "block");

        // }
    });
    
    $('#loadergif').hide();

    function Loader(){
        fileName = $('#file').val().split("\\");
        text = "Do you want to upload " +
        fileName[fileName.length - 1] +
        " ?"
        if(confirm(text) == true){
            $('#loadergif').show();
            //$('#upload_btn').hide();
            setTimeout(function () {
                $('#upload_btn').prop('disabled', true);
            }, 100);
        }else{
            $('#upload_btn').prop('disabled', true);
            setTimeout(function () {
                $('#upload_btn').prop('disabled', false);
            }, 100);
        }
    };
    // console.log(selected);
</Script>
<style>
    .default-font{
        font-size:14px;
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

    .custom-select {
        position: relative;
        font-family: Arial;
    }

    .upload-head {
        background: #695800;
        color: #fff;
        font-family: 'Raleway', sans-serif;
        font-size: 18px;
    }
</style>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/srikanth2k23/public_html/resources/views/home.blade.php ENDPATH**/ ?>