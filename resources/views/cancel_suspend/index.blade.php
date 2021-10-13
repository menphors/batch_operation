@extends('layouts.master')

@section('content')
    <div class="card card-gray">
        <div class="card-header">
            <div class="header-block">
                <p class="title">Cancel Suspend</p>
            </div>
        </div>
        <hr>
        <div class="card-block">
            <form action="">
                <div class="row">
                    <div class="col-sm-8">
                        <div class="form-group row">
                            <label class="col-sm-3" for="">
                                Company Code
                            </label>
                            <div class="col-sm-9">
                                <input class="form-control" type="text" name="company_code" id="company_code" placeholder="Input Company Code">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3" for="">
                                Phone Number
                            </label>
                            <div class="col-sm-9">
                                <input class="form-control" type="text" name="phone_number" id="phone_number" placeholder="Input Company Code">
                            </div>
                        </div>
                        <div class="form-group d-flex justify-content-end">
                            <button type="submit" class="btn btn-oval btn-success">Submit</button>
                        </div>
                    </div>
                </div>
            </form>
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
        $("#cancel_suspend").addClass("active");
        
    });
</script>
@endsection