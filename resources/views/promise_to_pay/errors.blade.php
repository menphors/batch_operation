@extends('layouts.master')
@section('content')
<div class="jumbotron text-center">
    <h1 class="display-3">Oop...!</h1>
    <p class="lead"><span style="color:red">Error Code:</span> {{ $resultCode }}</p>
    <hr>
    <p><span style="color:red">Error Message:</span>{{$resultDesc}}</p>
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