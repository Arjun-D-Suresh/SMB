// const { Alert } = require("bootstrap");
var data1 = "";
var data2 = "";
var comapanyname = "";
var dividendamount = "";
var baseurl = "https://ajaysrikanth.com/";
// var baseurl = "http://127.0.0.1:8000/";

// $(document).ready(function () {
//     $.ajaxSetup({
//         headers: {
//             "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
//         },
//     });
//     $("#btnupload").click(function () {
//         $("#btnupload").prop("disabled", true);
//         $("#btnupload").html(
//             '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...'
//         );

//         maindata = {
//             data1: data1,
//             data2: data2,
//             comapanyname: comapanyname,
//             dividendamount: dividendamount,
//         };
//         if (data1 != "" || data2 != "") {
//             $("#tablemsg").html("<p>No data to process</p>");
//             var saveData = $.ajax({
//                 type: "POST",
//                 url: baseurl + "storedata",
//                 data: maindata,
//                 dataType: "text",
//                 success: function (resultData) {
//                     $("#btnupload").html("Process");
//                     $("#btnupload").prop("disabled", false);
//                     $("#succmsg").show("slow");
//                     $("#msgcnt").html("<p>Data added Successfully</p>");

//                     setTimeout(function () {
//                         location.reload(true);
//                     }, 3000);
//                 },
//             });
//             saveData.error(function () {
//                 $("#tablemsgdiv").show("slow");
//                 $("#tablemsg").html("<p>Something went wrong</p>");
//                 $("#btnupload").html("Process");
//                 $("#btnupload").prop("disabled", false);
//             });
//         } else {
//             $("#tablemsgdiv").show("slow");
//             $("#tablemsg").html("<p>No data to process</p>");
//         }
//     });
// });

// $(function () {
//     $.ajaxSetup({
//         headers: {
//             "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
//         },
//     });

//     var select = document.getElementById("getMultiDiv");
//     select.addEventListener(
//         "click",
//         function () {
//             $("#dividenttable").html("");
//             setTimeout(() => {
//                 var security_code = document.getElementById("security_code");
//                 var cin_number = document.getElementById("cin_number");

//                 data = {
//                     cin_number: cin_number.value,
//                     security_code: security_code.value,
//                 };

//                 var saveData = $.ajax({
//                     type: "POST",
//                     url: baseurl + "dividentlist",
//                     data: data,
//                     dataType: "JSON",
//                     success: function (resultData) {
//                         var bykeList = resultData;

//                         $("#dividenttable").html("");
//                         if (bykeList.length > 0) {
//                             for (i = 0; i < bykeList.length; i++) {
//                                 var year =
//                                     bykeList[i]["ex_date"] == null
//                                         ? bykeList[i]["ex_date"]
//                                         : bykeList[i]["ex_date"].split("-");
//                                 var newDate =
//                                     year == null
//                                         ? "--"
//                                         : year[2] +
//                                           "-" +
//                                           year[1] +
//                                           "-" +
//                                           year[0];

//                                 var html =
//                                     "<tr><td>" +
//                                     "<input type='radio' class='selectdividend' name='selectdividend' value='" +
//                                     bykeList[i]["id"] +
//                                     "/" +
//                                     bykeList[i]["c_fullname"] +
//                                     "/" +
//                                     bykeList[i]["dividend_amount"] +
//                                     "'></input>" +
//                                     "</td><td>" +
//                                     bykeList[i]["security_code"] +
//                                     "</td><td>" +
//                                     bykeList[i]["c_fullname"] +
//                                     "</td><td>" +
//                                     bykeList[i]["proposed_date"] +
//                                     "</td><td>" +
//                                     bykeList[i]["year"] +
//                                     "</td><td>" +
//                                     bykeList[i]["purpose"] +
//                                     "</td><td>" +
//                                     bykeList[i]["dividend_amount"];
//                                 +"</td></tr>";
//                                 $("#dividenttable").append(html);

//                                 $("td")
//                                     .filter(function () {
//                                         return this.innerHTML.match(
//                                             /^[0-9\s\.,]+$/
//                                         );
//                                     })
//                                     .css("text-align", "right");
//                             }

//                             $(".selectdividend").click(function () {
//                                 var radioValue = $(
//                                     "input[name='selectdividend']:checked"
//                                 ).val();
//                                 data1 = radioValue;
//                                 if (radioValue) {
//                                     var dividendamount = radioValue;
//                                     var selecteddividend = radioValue;

//                                     var arr = selecteddividend.split("/");
//                                 }
//                             });
//                         } else {
//                             $("#tablemsgdiv").show("slow");
//                             $("#tablemsg").html(
//                                 "<p>No dividend data available :(</p>"
//                             );
//                         }
//                         // });
//                     },
//                 });
//                 saveData.error(function () {
//                     $("#tablemsgdiv").show("slow");
//                     $("#tablemsg").html("<p>Somthing went wrong :(</p>");
//                 });
//             }, 1000);
//         },
//         false
//     );

//     $.ajaxSetup({
//         headers: {
//             "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
//         },
//     });
//     var select = document.getElementById("getMultiDiv");
//     select.addEventListener(
//         "click",
//         function () {
//             $("#getMultiDiv").html(
//                 '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>'
//             );
//             $("#multipledivident").html("");
//             $("#getMultiDiv").prop("disabled", true);
//             setTimeout(() => {
//                 data1 = "";
//                 data2 = "";
//                 var select = document.getElementById("cin_number");
//                 var fileElement = document.getElementById("file_select");
//                 var xferElement = document.getElementById("xfer_select");
//                 comapanyname = document.getElementById("company_name");
//                 data = {
//                     cin: select.value,
//                     log_id: fileElement.value,
//                     xfer_date: xferElement.value.split(",")[0],
//                     D_No: xferElement.value.split(",")[1],
//                 };

//                 var saveData = $.ajax({
//                     type: "POST",
//                     url: baseurl + "multipledividend",
//                     data: data,
//                     dataType: "JSON",
//                     success: function (resultData) {
//                         $(document).ready(function () {
//                             var bykeList = resultData;
//                             if (bykeList.length === 0) {
//                                 $("#tablemsgdiv").show("slow");
//                                 $("#tablemsg").html(
//                                     "<p>No dividend data available :(</p>"
//                                 );
//                             }

//                             $("#multipledivident").html("");
//                             if (bykeList.length > 0) {
//                                 for (i = 0; i < bykeList.length; i++) {
//                                     var year =
//                                         bykeList[i]["proposeddateoftransfer"] ==
//                                         null
//                                             ? bykeList[i][
//                                                   "proposeddateoftransfer"
//                                               ]
//                                             : bykeList[i][
//                                                   "proposeddateoftransfer"
//                                               ].split("-");
//                                     var newDate =
//                                         year == null
//                                             ? "--"
//                                             : year[2] +
//                                               "-" +
//                                               year[1] +
//                                               "-" +
//                                               year[0];

//                                     data2 = data2 + bykeList[i]["id"] + ",";
//                                     var html =
//                                         "<tr><td>" +
//                                         bykeList[i]["firstname"] +
//                                         " " +
//                                         bykeList[i]["middlename"] +
//                                         "</td><td>" +
//                                         bykeList[i]["folionumber"] +
//                                         "</td><td>" +
//                                         bykeList[i]["amounttransfered"] +
//                                         "</td><td>" +
//                                         newDate +
//                                         "</td><td>" +
//                                         "-" +
//                                         "</td></tr>";
//                                     $("#multipledivident").append(html);

//                                     $("td")
//                                         .filter(function () {
//                                             return this.innerHTML.match(
//                                                 /^[0-9\s\.,]+$/
//                                             );
//                                         })
//                                         .css("text-align", "right");
//                                 }
//                             }
//                             $(".selectdividend").click(function () {
//                                 console.log($(this));
//                                 var radioValue = $(
//                                     "input[name='selectdividend']:checked"
//                                 ).val();
//                                 var DIV = "-";

//                                 if (radioValue) {
//                                     var selecteddividend = radioValue;
//                                     var arr = selecteddividend.split("/");
//                                     DIV = arr[2];
//                                     comapanyname =
//                                         $("#companyNameselect").val();
//                                     dividendamount = DIV;
//                                 }
//                                 $("#multipledivident").html("");
//                                 if (bykeList.length > 0) {
//                                     for (i = 0; i < bykeList.length; i++) {
//                                         var year =
//                                             bykeList[i][
//                                                 "proposeddateoftransfer"
//                                             ] == null
//                                                 ? bykeList[i][
//                                                       "proposeddateoftransfer"
//                                                   ]
//                                                 : bykeList[i][
//                                                       "proposeddateoftransfer"
//                                                   ].split("-");
//                                         var newDate =
//                                             year == null
//                                                 ? "--"
//                                                 : year[2] +
//                                                   "-" +
//                                                   year[1] +
//                                                   "-" +
//                                                   year[0];

//                                         let noOfShares =
//                                             bykeList[i]["amounttransfered"] /
//                                             DIV;
//                                         let noOfSharesHTML = "";
//                                         if (noOfShares % 1 !== 0) {
//                                             noOfSharesHTML =
//                                                 '<span style = "color:red;font-weight: bold;">' +
//                                                 String(noOfShares).replace(
//                                                     /(\.\d\d\d).+/,
//                                                     "$1"
//                                                 ) +
//                                                 "</span>";
//                                         } else {
//                                             noOfSharesHTML =
//                                                 "<span>" +
//                                                 noOfShares +
//                                                 "</span>";
//                                         }

//                                         var html =
//                                             "<tr><td>" +
//                                             bykeList[i]["firstname"] +
//                                             " " +
//                                             bykeList[i]["middlename"] +
//                                             "</td><td>" +
//                                             bykeList[i]["folionumber"] +
//                                             "</td><td>" +
//                                             bykeList[i]["amounttransfered"] +
//                                             "</td><td>" +
//                                             newDate +
//                                             "</td><td>" +
//                                             noOfSharesHTML +
//                                             "</td></tr>";
//                                         $("#multipledivident").append(html);

//                                         $("td")
//                                             .filter(function () {
//                                                 return this.innerHTML.match(
//                                                     /^[0-9\s\.,]+$/
//                                                 );
//                                             })
//                                             .css("text-align", "right");
//                                     }
//                                 }
//                             });
//                         });
//                         $("#getMultiDiv").html("Go");
//                         $("#getMultiDiv").prop("disabled", false);
//                     },
//                 });
//                 saveData.error(function () {
//                     $("#tablemsgdiv").show("slow");
//                     $("#tablemsg").html("<p>Somthing went wrong :(</p>");
//                     $("#getMultiDiv").html("Go");
//                 });
//             }, 1000);
//         },
//         false
//     );
// });
$.ajaxSetup({
    headers: {
        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
    },
});
$("#deletemembersdata").on("click", function () {
    var select = document.getElementById("cin_number");
    var fileElement = document.getElementById("file_select");
    var xferElement = document.getElementById("xfer_select");
    alert();
    data = {
        cin: select.value,
        log_id: fileElement.value,
        xfer_date: xferElement.value,
    };
    var saveData = $.ajax({
        type: "POST",
        url: baseurl + "deletemembersdata",
        data: data,
        dataType: "JSON",
        success: function (resultData) {
            $("<p>Data added Successfully</p>").appendTo("#Purge_s");
            setTimeout(function () {
                location.reload(true);
            }, 3000);
        },
    });
    location.reload();
});

$.ajaxSetup({
    headers: {
        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
    },
});

$(".excel-download").click(function () {
    btn = $(this);
    btn.html(
        '<div class="spinner-border text-light" role="status"><span class="sr-only">Loading...</span></div>'
    );
    $(".excel-download").prop("disabled", true);
    $.fn.excelDownload($(this).val(), btn);
});
$.fn.excelDownload = function (id, btn) {
    var saveData = $.ajax({
        type: "GET",
        url: baseurl + "create-excel/" + id,
        data: "",
        dataType: "JSON",
        success: function (resultData) {
            $(".excel-download").prop("disabled", false);
            btn.html("download");
            if (resultData.flag == 1) {
                const filename = id.split("/");
                window.location.replace(
                    baseurl + "excel-download/" + filename[1]
                );
            }
        },
    });
    saveData.error(function () {
        $(".excel-download").prop("disabled", false);
        btn.html("download");
        alert("Something went wrong");
    });
};
