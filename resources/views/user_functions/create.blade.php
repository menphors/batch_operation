@extends('layouts.master')


@section('content')
    <div class="card card-gray">
        <div class="card-header">
            <div class="header-block">
                <p class="title">
                Create
                <a href="{{url('user/function')}}"class="btn btn-primary-outline btn-oval btn-sm mx-left">
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
                    <form action="{{url('user/function/permission/save')}}" method="POST" enctype="multipart/form-data">
                        {{csrf_field()}}
                        <input type="hidden" name="user_id" value="{{$per->user_id}}">
                        <div class="form-group row">
                            <label for="function_id" class="col-sm-3">Function<span class="text-danger">*</span></label>
                            <div class="col-sm-6 ">
                                <select name="function_id" id="function_id" class="form-control chosen-select" required>
                                    <option value="">--select--</option>
                                    @foreach($fs as $d)
                                    <option value="{{$d->id}}">{{$d->function_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-3"></div>
                            <div class="col-sm-6">
                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-oval btn-success">Submit</button>
                                </div>
                            </div>
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
            $("#user_permission").addClass("active");
			
        });
    </script>    
@endsection