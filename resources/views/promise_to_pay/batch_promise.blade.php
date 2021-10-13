@extends('layouts.master')

@section('content')
<div class="card card-gray">
        <div class="card-header">
            <div class="header-block">
            <p class="title"> Promise To Pay by Number Lists</p>
            </div>
        </div>
        <hr>
        <div class="card-block">
            <div class="row">
                <div class="col-sm-2"></div>
                <div class="col-sm-8" style="border: 1px solid #EEE;">
                    <form action="{{url('do-batch-ptp')}}" method="POST" enctype="multipart/form-data" name="form1">
                    {{csrf_field()}}
                        <div class="card-header">
                            <div class="header-block">
                            <p class="title">File</p>
                            </div>
                        </div>
                        <hr>
                        <div class="form-group row requiredptp">
                            <div class="col-sm-4 ">
                                <label class="col-form-label" for="number">Phone Number List as Excel</label>
                            </div>
                            <div class="col-sm-6">
                                <input type="file"class="form-control-file" name="msisdn" id="msisdn" required/>
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
                        <div class="card-header">
                            <div class="header-block">
                            <p class="title">Date</p>
                            </div>
                        </div>
                        <hr>
                        <div class="form-group row requiredptp">
                            <label class="col-sm-4 col-form-label" for="agreedToPayDate">PTP End Date</label>
                            <div class="col-sm-8">
                            <input type="date" value=<?php echo date('Y-m-d');?> name="agreedToPayDate" id="agreedToPayDate" required>
                            </div>
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
                                <button class="btn btn-primary btn-oval sub">
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
            $('button#sub').click(function(){
                $.ajax({
                    url: 'do-batch-ptp',
                    type:'GET',
                    success:function(data) {
                        console.log(data);
                    }
                });
            });
        });
    </script>
@endsection