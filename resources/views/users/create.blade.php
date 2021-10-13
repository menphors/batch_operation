@extends('layouts.master')


@section('content')
    <div class="card card-gray">
        <div class="card-header">
            <div class="header-block">
                <p class="title">
                    Create User
                    <a href="{{url('user')}}"class="btn btn-primary-outline btn-oval btn-sm mx-left">
                        <i class="fa fa-reply"></i> Back
                    </a>
                </p>
            </div>
        </div>
        <hr>
        <div class="card-block">
            <div class="row">
                <div class="col-sm-8">
                    @if(Session::has('success'))
                    <div class="alert alert-success" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <div>
                            {{session('success')}}
                        </div>
                    </div>
                    @endif
                    @if(Session::has('error'))
                    <div class="alert alert-danger" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <div>
                            {{session('error')}}
                        </div>
                    </div>
                    @endif
                    <form action="{{url('user/save')}}" method="POST" enctype="multipart/form-data">
                        {{csrf_field()}}
                        <div class="form-group row">
                            <label for="name" class="col-sm-3">Name<span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="name" name="name" 
                                    value="{{old('name')}}" required autofocus>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="email" class="col-sm-3">Email<span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="email" name="email" 
                                    value="{{old('email')}}"required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="phone_number" class="col-sm-3">Phone Number</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="phone_number" name="phone_number" 
                                    value="{{old('phone_number')}}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="position" class="col-sm-3">Position</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="position" name="position" 
                                    value="{{old('position')}}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="location" class="col-sm-3">Location</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="location" name="location" 
                                    value="{{old('location')}}">
                            </div>
                        </div>
                        <div class="form-group row">
                                <label for="role_id" class="col-sm-3">Role<span class="text-danger">*</span></label>
                                <div class="col-sm-9">
                                    <select name="role_id" id="role_id" class="form-control" required>
                                        <option value="">--select--</option>
                                        @foreach($rs as $d)
                                        <option value="{{$d->id}}">{{$d->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div> 
                        <div class="form-group row">
                            <label for="created_by" class="col-sm-3">Created By</label>
                            <div class="col-sm-9">
                                <label for="created_by" id="created_by" name="created_by">{{Auth::user()->name}}</label>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end">
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
			
            $("#menu_user").addClass("active open");
			$("#user_collapse").addClass("collapse in");
            $("#user").addClass("active");
			
        })
    </script>
@endsection