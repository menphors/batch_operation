@extends('layouts.master')

@section('content')
    <div class="card card-gray">
        <div class="card-header">
            <div class="header-block">
            <p class="title"> Batch Collection 
                <a href="{{url('batch_collection')}}" class="btn btn-primary-outline btn-oval btn-sm mx-left">
                    <i class="fa fa-reply"></i> Back
                </a>
            </p>
            </div>
        </div>
        <hr>
        <div class="card-block ">
            <div class="row">
                <div class="col-sm-12">
                    <div class="table-flip-scroll">
                        <table class="table table-sm table-bordered table-hover flip-content">
                            <thead class="flip-header bg-success text-dark">
                                <tr>
                                    <th>CUST_NAME</th>
                                    <th>TOTAL_NUMBER</th>
                                   
                                    
                                   
                                </tr>
                            </thead>
                            <tbody>
                                <!-- For Loop the data from Payment controller -->
                                @foreach($total_display as $r)
                                    <tr>
                                        <td>{{$r['cust_name']}}</td>
                                        <td>{{$r['total']}}</td>
                                       
                                    </tr>   
                                
                                                     
                                @endforeach
                         
                            </tbody>
                        </table>
                        <a href="{{url('excel')}}" class="btn btn-success btn-oval btn-sm" >Download CSV File</a>
                        <!-- <button class="btn btn-success btn-oval btn-sm" onClick="download()">Download CSV File</button> -->
                        <!-- <button class="btn btn-success btn-oval btn-sm" onClick="download()">Download UNL File</button> -->
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
        var total_display = {!! json_encode($total_display) !!};
    
        console.log(total_display);


        var currentDate = new Date();
        var date = currentDate.getDate();
        var month = currentDate.getMonth(); //Be careful! January is 0 not 1
        var year = currentDate.getFullYear();
        var dateString ="0"+date+"0"+(month + 1)+ year+"Result";

    //
    function download(){
    var headers = {
         cust_code: "CUST_CODE",
         acct_code: "ACCT_CODE",
         service_number: "SERVICE_NUMBER",
   
    };


     var itemsFormatted = [];
    // format the data
   
        total_display.forEach((item) => {
        itemsFormatted.push({

            cust_code: item.cust_code.replace(/,/g, ''), // remove commas to avoid errors,
            acct_code: item.acct_code,
            service_number: service_number,

        
        });
 


    var fileTitle = dateString;

    exportCSVFile(headers, fileTitle); // call the exportCSVFile() function to process the JSON and trigger the download
    }
</script>


@endsection