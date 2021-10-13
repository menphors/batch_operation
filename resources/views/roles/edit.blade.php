@extends('layouts.master')


@section('content')
    <div class="card card-gray">
        <div class="card-header">
            <div class="header-block">
                <p class="title">
                    Edit Role
                    <a href="{{url('role')}}"class="btn btn-primary-outline btn-oval btn-sm mx-left">
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
                    <form action="{{url('role/update')}}" method="POST" enctype="multipart/form-data">
                        {{csrf_field()}}
                        <input type="hidden" name="id" value="{{$role->id}}">
                        <div class="form-group row">
                            <label for="name" class="col-sm-3">Name<span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="name" name="name" 
                                    value="{{$role->name}}" required autofocus>
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
            $("#role").addClass("active");
			
        })
    </script>
@endsection