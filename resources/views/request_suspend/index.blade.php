@extends('layouts.master')

@section('content')
    <div class="card card-gray">
        <div class="card-header">
            <div class="header-block">
                <p class="title">Request Temporary Suspend</p>
            </div>
        </div>
        <hr>
        <div class="card-block">
            <div class="row">
                <div class="col-sm-8">
                    <form action="">
                        <div class="form-group row">
                            <label class="col-sm-3">
                                Company Code
                            </label>
                            <div class="col-sm-9">
                                <input class="form-control" type="text" id="company_code" name="company_code" placeholder="Input Company CUST_ID">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3">
                                Phone Number
                            </label>
                            <div class="col-sm-9">
                                <input class="form-control" type="text" id="phone_number" name="phone_number" placeholder="Input Phone Number">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3">
                                Suspend Date
                            </label>
                            <div class="col-sm-9">
                                <input class="form-control" type="text" id="suspend_date" name="suspend_date" placeholder="Input Suspend Date YYYYMM">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3">
                                Suspend Period
                            </label>
                            <div class="col-sm-9">
                                <select class="form-control" name="suspend_period" id="suspend_period">
                                    <option value="">---Select---</option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group d-flex justify-content-end">
                            <button type="submit" class="btn btn-oval btn-success">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
<script>
    $(document).ready(function () {
        $("#sidebar-menu li ").removeClass("active open");
        $("#sidebar-menu li ul li").removeClass("active");
        
        $("#menu_postpaid").addClass("active open");
        $("#postpaid_collapse").addClass("collapse in");
        $("#request_suspend").addClass("active");
        
    });
</script>
@endsection