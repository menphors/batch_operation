@extends('layouts.master')

@section('content')
    <div class="card card-gray">
        <div class="card-header">
            <div class="header-block">
            <p class="title"> Batch Payment 
                <a href="{{url('payment')}}" class="btn btn-primary-outline btn-oval btn-sm mx-left">
                    <i class="fa fa-reply"></i> Back
                </a>
            </p>
            </div>
        </div>
        <hr>
        <div class="card-block">
            <div class="row">
                <div class="col-sm-12">
                    <div class="table-flip-scroll">
                        <table class="table table-sm table-bordered table-hover flip-content">
                            <thead class="flip-header bg-success text-dark">
                                <tr>
                                    <th>Corporate Name</th>
                                    <th>Master Account</th>
                                    <th>Quantity Number</th>
                                    <th>Total Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- For Loop the data from Payment controller -->
                                @foreach($total_display as $cat)
                                    <tr>
                                        <td>{{$cat['cust_name']}}</td>
                                        <td>{{$cat['Master_acct']}}</td>
                                        <td>{{$cat['numbers_line']}}</td>
                                        <td>{{$cat['Total_Out']}}</td>
                                    </tr>   
                                @endforeach
                            </tbody>
                        </table>
                        <button class="btn btn-success btn-oval btn-sm" onClick="download()">Download CSV File</button>
                        <button class="btn btn-success btn-oval btn-sm" onClick="downloadunl()">Download UNL File</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')

<!-- For Module High Light -->
<script>
    $(document).ready(function () {
        $("#sidebar-menu li ").removeClass("active open");
        $("#sidebar-menu li ul li").removeClass("active");
        
        $("#menu_postpaid").addClass("active open");
        $("#postpaid_collapse").addClass("collapse in");
        $("#testing").addClass("active");
    });
</script>

<!-- For Download CSV File -->
<script>

    function convertToCSV(objArray) {
        var array = typeof objArray != 'object' ? JSON.parse(objArray) : objArray;
        var str = '';

        for (var i = 0; i < array.length; i++) {
            var line = '';
            for (var index in array[i]) {
                if (line != '') line += ','

                line += array[i][index];
            }

            str += line + '\r\n';
        }

        return str;
    }

    function exportCSVFile(headers, items, fileTitle) {
        if (headers) {
            items.unshift(headers);
        }

        // Convert Object to JSON
        var jsonObject = JSON.stringify(items);

        var csv = this.convertToCSV(jsonObject);

        var exportedFilenmae = fileTitle + '.csv' || 'export.csv';

        var blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
        if (navigator.msSaveBlob) { // IE 10+
            navigator.msSaveBlob(blob, exportedFilenmae);
        } else {
            var link = document.createElement("a");
            if (link.download !== undefined) { // feature detection
                // Browsers that support HTML5 download attribute
                var url = URL.createObjectURL(blob);
                link.setAttribute("href", url);
                link.setAttribute("download", exportedFilenmae);
                link.style.visibility = 'hidden';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            }
        }
    }   
    // Catch data from controller
        var comment = {!! json_encode($comment) !!};
    // Catch data from controller
        var total_file_display = {!! json_encode($total_file_display) !!};
        var method_pay = {!! json_encode($method_pay) !!};
        console.log(method_pay);


        var currentDate = new Date();
        var date = currentDate.getDate();
        var month = currentDate.getMonth(); //Be careful! January is 0 not 1
        var year = currentDate.getFullYear();
        var dateString ="0"+date+"0"+(month + 1)+ year+"BatchPayment";

    //
    function download(){
    var headers = {
        master_acct: total_file_display[0]['Master_acct'].replace(/,/g, ''), // remove commas to avoid errors
        bill_type: "BILL PAYMENT",
        method: method_pay,
        cust_name: total_file_display[0]['cust_name'],
        comment: comment
    };


        var itemsFormatted = [];
    // format the data
    if(total_file_display[0]['bill_cycle_id'] !== undefined)
    {
        total_file_display.forEach((item) => {
        itemsFormatted.push({
            service_number: item.service_number.replace(/,/g, ''), // remove commas to avoid errors,
            open_amt: item.Open_AMT,
            method_pay: method_pay,
            method: '8',
            // number: "201711070038",
            remark: comment,
            bill_cycle: '{"bill_cycle_id'+'":'+'"'+item.bill_cycle_id+'"}'
        });
    });
    }
    else if(total_file_display[0]['bill_cycle_id'] === undefined){
        total_file_display.forEach((item) => {
        itemsFormatted.push({
            service_number: item.service_number.replace(/,/g, ''), // remove commas to avoid errors,
            open_amt: item.Open_AMT,
            method_pay: method_pay,
            method: '8',
            // number: "201711070038",
            remark: comment
        });
    });
    }

    var fileTitle = dateString;

    exportCSVFile(headers, itemsFormatted, fileTitle); // call the exportCSVFile() function to process the JSON and trigger the download
    }
</script>


<!-- For Download UNL File -->
<script>
        function convertToUnl(objArray) {
        var array = typeof objArray != 'object' ? JSON.parse(objArray) : objArray;
        var str = '';

        for (var i = 0; i < array.length; i++) {
            var line = '';
            for (var index in array[i]) {
                if (line != '') line += ','

                line += array[i][index];
            }

            str += line + '\r\n';
        }

        return str;
    }

    function exportUNLFile(headers,items, fileTitle) {
        if (headers) {
            items.unshift(headers);
        }
        // Convert Object to JSON
        var jsonObject = JSON.stringify(items);

        var unl = this.convertToUnl(jsonObject);

        var exportedFilenmae = fileTitle + '.unl' || 'export.unl';

        var blob = new Blob([unl], { type: 'text;charset=utf-8;' });
        if (navigator.msSaveBlob) { // IE 10+
            navigator.msSaveBlob(blob, exportedFilenmae);
        } else {
            var link = document.createElement("a");
            if (link.download !== undefined) { // feature detection
                // Browsers that support HTML5 download attribute
                var url = URL.createObjectURL(blob);
                link.setAttribute("href", url);
                link.setAttribute("download", exportedFilenmae);
                link.style.visibility = 'hidden';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            }
        }
    }
        var comment = {!! json_encode($comment) !!};
        var total_file_display = {!! json_encode($total_file_display) !!};
        var method_pay = {!! json_encode($method_pay) !!};
        console.log(method_pay);
        console.log(total_file_display);
        console.log(comment);

        var currentDate = new Date();
        var date = currentDate.getDate();
        var month = currentDate.getMonth(); //Be careful! January is 0 not 1
        var year = currentDate.getFullYear();
        var dateString ="0"+date+"0"+(month + 1)+ year+"BatchPayment";
    function downloadunl(){
        
        var headers = {
        master_acct: total_file_display[0]['Master_acct'].replace(/,/g, ''), // remove commas to avoid errors
        bill_type: "BILL PAYMENT",
        method: method_pay,
        cust_name: total_file_display[0]['cust_name'],
        bill_cycle_id: comment
    };

        var itemsFormatted = [];

        if(total_file_display[0]['bill_cycle_id'] !== undefined )
        {
            total_file_display.forEach((item) => {
                itemsFormatted.push({
                    service_number: "," + item.service_number.replace(/,/g, ''), // remove commas to avoid errors,
                    open_amt: item.Open_AMT,
                    method_pay: method_pay,
                    number: "8,,,,,,,,,,,",
                    // date: "201711070038",
                    remark: comment,
                    bill_cycle: '{"bill_cycle_id'+'":'+'"'+item.bill_cycle_id+'"}'
                });
            });
        }
        else if(total_file_display[0]['bill_cycle_id'] === undefined)
        {
            total_file_display.forEach((item) => {
                itemsFormatted.push({
                    service_number: "," + item.service_number.replace(/,/g, ''), // remove commas to avoid errors,
                    open_amt: item.Open_AMT,
                    method_pay: method_pay,
                    number: "8,,,,,,,,,,,",
                    // date: "201711070038",
                    remark: comment
                });
            });
        }
    // format the data



    var fileTitle = dateString; // or 'my-unique-title'

    exportUNLFile(headers,itemsFormatted, fileTitle); // call the exportCSVFile() function to process the JSON and trigger the download
    }
</script>

@endsection