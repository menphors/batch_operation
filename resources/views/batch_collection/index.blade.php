@extends('layouts.master')

@section('content')
    <div class="card card-gray">
        <div class="card-header">
            <div class="header-block">
            <p class="title">Batch file for dunning (barring, un-barring, suspend, un-suspend)</p>
            </div>
        </div>
        <hr>
        <div class="card-block">
            <div class="row">
                <div class="col-sm-2"></div>
                <div class="col-sm-8">
                @if(Session::has('fail'))
                    <div class="alert alert-danger" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <div>
                            {{session('fail')}}
                        </div>
                    </div>
                @endif
                    <form action="{{url('batch_collection/request')}}" method="POST" enctype="multipart/form-data">
                    {{ csrf_field() }}
                        <div class="form-group row">
                            <label for="master_acct" class="col-sm-4">Corp No<span class="text-danger">*</span></label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="master_acct" name="master_acct" auto-focus required placeholder="Input Corp No account">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-4" for="remark">Remark</label>
                            <div class="col-sm-8">
                                <textarea class="form-control" name="remark" id="remark" cols="51" rows="3" placeholder="Input remark"></textarea>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-4" for="remark">Due Date</label>
                            <div class="col-sm-8">
                            <input type="date" value=<?php echo date('Y-m-d');?> name="date">
                            </div>
                        </div>




                        <div class="form-group row">
                            <label class="col-sm-4">&nbsp;</label>
                            <div class="col-sm-8">
                                <button class="btn btn-primary btn-oval">
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
@endsection
@section('js')

<script>
    $(document).ready(function () {
        $("#sidebar-menu li ").removeClass("active open");
        $("#sidebar-menu li ul li").removeClass("active");
        
        $("#menu_postpaid").addClass("active open");
        $("#postpaid_collapse").addClass("collapse in");
        $("#batch").addClass("active");
        
    });
</script>
@endsection