@extends('layouts.master')
@section('content')
    <div class="row">
        <div class="col-md-2"></div>
        <div class="col-md-8">
            <div class="card card-block">
            <div class=""> <p></p></div>
                <div class="card-header">
                    <div class="header-block">
                        <h1>Name :<b class="text-success upper"> {{$profile->name}}</b></h1>
                        <h5>{{$profile->position}}</h5>
                    </div>
                    
                </div>
                <hr>
                <div class="card-block">
                    <div class="form-group row">
                        <label class="col-sm-3 form-control-label text-dark"><b>Email</b></label>
                        <div class="col-sm-9">
                            : {{$profile->email}}
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 form-control-label text-dark"><b>Phone Number</b></label>
                        <div class="col-sm-9">
                            : {{$profile->phone_number}}
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 form-control-label text-dark"><b>Location</b></label>
                        <div class="col-sm-9">
                            : {{$profile->location}}
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-success">
                    <p> </p>
                </div>
            </div>
        </div>
        <div class="col-md-2"></div>
    </div>
@endsection