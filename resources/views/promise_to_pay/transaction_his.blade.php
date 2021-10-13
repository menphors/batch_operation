@extends('layouts.master')

@section('content')
<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="ptp">Promise To Pay</a></li>
    <li class="breadcrumb-item active" aria-current="page">Transaction History</li>
  </ol>
</nav>
<div class="jumbotron">
    <form action="#" method="GET">
        {{csrf_field()}}
            <div class="card-header">
                <div class="header-block">
                <p class="title" class="align-left">Search</p>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-4" for="reqPayDate">Start Date:</label>
                <div class="col-sm-8">
                <input type="date" value=<?php echo date('Y-m-d');?> name="reqPayDate">
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-4" for="agdPayDate">End Date:</label>
                <div class="col-sm-8">
                    <input type="date" value=<?php echo date('Y-m-d');?> name="agdPayDate">
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-4" for="serviceNo">Service No:</label>
                <div class="col-sm-8">
                    <input type="text" value="" name="service No" placeholder="Service no">
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-4">&nbsp;</label>
                <div class="col-sm-8">
                    <button class="btn btn-primary btn-oval">
                        <i class="fa fa-save"></i> Search
                    </button>
                </div>
            </div>
    </form>
</div>
<div class="jumbotron">
    <div class="form-group row">
        <button class="btn btn-primary btn-oval">
                <i class="fa fa-save"></i><a href="{{ url('export-Excel')}}">Export Excel</a>
        </button>
    </div>
    <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover dataTable js-exportable" id="table">
                <thead>
                    <tr class="table-head-style">
                    <th scope="col">#</th>
                    <th scope="col">Service No</th>
                    <th scope="col">Invoice No</th>
                    <th scope="col">Invoice Date</th>
                    <th scope="col">Status</th>
                    <th scope="col">message</th>
                    <th scope="col">Start Date</th>
                    <th scope="col">End Date</th>
                    <th scope="col">Execute By</th>
                    <th scope="col">Remark</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i=0;?>
                    @foreach($data as $result)
                    <?php $i++;?>
                    <tr>
                        <th scope="row"><?php echo $i;?></th>
                        <td>{{ $result['service_no']}}</td>
                        <td>{{ $result['invoice_no']}}</td>
                        <td>{{ $result['invoice_date']}}</td>
                        @if($result['error_code'] == '0')
                        <td><p class="badge badge-pill badge-success">Completed</p></d>
                        @else
                        <td><p class="badge badge-pill badge-danger">Failed</p></d>
                        @endif
                        <td>{{ $result['error_messages']}}</d>
                        <td>{{ $result['created_date']}}</d>
                        <td>{{ $result['end_date']}}</d>
                        <td>{{ $result['execute_by']}}</d>
                        <td>{{ $result['remark']}}</d>
                    </tr>
                    @endforeach
                </tbody>
            </table>
    </div>
</div>
<script>
    $(document).ready(function() {
        $('#table').DataTable();
    });
 </script>
@endsection