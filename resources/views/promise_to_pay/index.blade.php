@extends('layouts.master')

@section('content')
<div class="card card-gray">
        <div class="card-header">
            <div class="header-block">
            <p class="title">Individual Promise To Pay</p>
            </div>
        </div>
        <hr>
        <div class="card-block">
            <div class="row">
                <div class="col-sm-2"></div>
                <div class="col-sm-8" style="border: 1px solid #EEE;">
                    <form action="{{url('get-ptp')}}" method="GET" enctype="multipart/form-data" name="form2">
                    {{csrf_field()}}
                        <div class="card-header">
                            <div class="header-block">
                            <p class="title">Search</p>
                            </div>
                        </div>
                        <hr>
                        <div class="form-group row requiredptp">
                            <label for="billcycle" class="col-sm-4 col-form-label">Promise to Pay Mode</label>
                            <div class="col-sm-8">
                                <select name="billcycle" id="billcycle" class="form-control">
                                    <option value="ptp"><a href="{{url('ptp')}}">By MSISDN</a></option>
                                    <option value="batch-ptp"><a href="{{url('batch-ptp')}}">By Batch Phone Number List</a></option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row requiredptp">
                            <label for="msisdn" class="col-sm-4 col-form-label">MSISDN</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" id="msisdn" name="msisdn" auto-focus placeholder="Input the Subscriber">
                            </div>
                            <!-- <label class="col-sm-4">&nbsp;</label> -->
                            <div class="col-sm-2">
                                <button class="btn btn-primary btn-oval detail-btn" type="button"  data-toggle="modal" data-target="#myModal" data-id="965797037">
                                    <i class="fa fa-search"></i> Search
                                </button>
                            </div>
                        </div>
                        <!-- Modal -->
                        <div class="modal" id="myModal" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="myModalLabel">#Invoice No</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div class="table-responsive">
                                    <table class="table table-bordered" id="tableId">
                                        <thead id="thead">
                                            <tr class="table-head-style">
                                            </tr>
                                        </thead>
                                        <tbody style="font-size:10pt;" id="tbody">
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <button type="button" class="btn btn-primary" data-dismiss="modal">Ok</button>
                            </div>
                            </div>
                        </div>
                        </div>
                        <div class="card-header">
                            <div class="header-block">
                            <p class="title">Records</p>
                            </div>
                        </div>
                        <hr>
                        <div class="form-group row requiredptp">
                            <label class="col-sm-4 col-form-label" for="agreedToPayDate">PTP End Date</label>
                            <div class="col-sm-8">
                                <input type="date" value=<?php echo date('Y-m-d');?> name="agreedToPayDate">
                            </div>
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </div>
                        <div class="form-group row">
                            <label for="amount_type" class="col-sm-4">Remark</label>
                            <div class="col-sm-8">
                            <textarea class="form-control" id="remark" name="remark" rows="3"></textarea>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-4">&nbsp;</label>
                            <div class="col-sm-8">
                                <button class="btn btn-primary btn-oval subptp">
                                    <i class="fa fa-save"></i> Submit
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-sm-2"></div>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function(){
            $('#myModal').modal('hide');
            $('.detail-btn').click(function(){
                //const id = $(this).attr('data-id');
                const msisdn = $('#msisdn').val();
                $.ajax({
                    url: 'get-msisdn/'+msisdn,
                    type:'GET',
                    data: {
                        "msisdn": msisdn
                    },
                    success:function(data) {
                        console.log(data);
                        var $thead = $('#tableId').find('thead');
                        var $tr = $("<tr>");
                        var table_body = $("#tbody");
                        $tr.append("<thead><tr><th><input type='checkbox' id='allInvoiceNo' name='allInvoiceNo' value='data[i].invoice_no' auto-focus/></th><th style='padding:7px'>Service No</th><th style='margin:-5px'>Outs<br>.Bill</th><th style='padding-left:85px;text-align:center'>Invoice No</th><th>Due Date</th><th>BillCycle</th></tr></thead>");
                        $.each(data, function(i, invoice_no){
                            table_body.append("<tbody><tr><td><input type='checkbox' id='invoiceNo' name='invoiceNo' auto-focus/></td>"+ 
                            "<td>" + data[i].service_no + "</td>"+
                            "<td>$" + data[i].open_amt + "</td>"+
                            "<td>" + data[i].invoice_no + "</td>"+
                            "<td>" + data[i].due_date + "</td>"+
                            "<td>" + data[i].billcycle + "</td></tr>");
                        });
                        $thead.append($tr);
                    }
                });
            });
        });
    </script>
    <script>
        $(document).ready(function(){
            $("#billcycle").change(function(){
                var query = $(this).val();
                if(query == "ptp"){
                    window.location.replace("https://crm-inhouse.smart.com.kh/ptp");
                }
                if(query == "batch-ptp") {
                    window.location.replace("https://crm-inhouse.smart.com.kh/batch-ptp");
                }
            });
        });
    </script>
@endsection