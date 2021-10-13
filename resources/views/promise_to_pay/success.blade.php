@extends('layouts.master')

@section('content')
<div class="jumbotron text-center">
    <h1 class="display-3">Thank You!</h1>
    <p class="lead">Result Code:<span style="color:red">{{ $resultCode }}</span></p>
    <hr>
    <!-- <p>Paid Id: <b>{{$paidId}}</b></p> -->
    <p>Message:<b style="color:green">{{$resultDesc}}</b></p>
    <div class="form-group row">
        <div class="col-md-8">
            <a class="btn btn-primary btn-sm" href="{{url('batch-ptp')}}" role="button"><i class="far fa-arrow-alt-circle-left"></i> Back to Promise To Pay</a>
        </div>
        <div class='col-md-4'>
        <a class="btn btn-primary btn-sm col-md-8" href="{{url('view-history')}}" role="button"><i class="far fa-arrow-alt-circle-left"></i>View Transaction History</a>
        </div>
    </div>
</div>
@endsection