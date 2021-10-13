@extends('layouts.master')
@section('content')
<div class="card card-gray">
	<div class="card-header">
		<div class="header-block">
			<p class="title">User Detail
				<a href="{{url('user')}}"class="btn btn-primary-outline btn-oval btn-sm mx-left">
                    <i class="fa fa-reply"></i> Back
                </a>
                <a href="{{url('user/create')}}"class="btn btn-primary-outline btn-oval btn-sm mx-left">
                    <i class="fa fa-plus-circle"></i> Create
                </a>
                <a href="{{url('user/edit/'.$user_detail->id)}}"class="btn btn-primary-outline btn-oval btn-sm mx-left">
                    <i class="fa fa-edit"></i> Edit
                </a>
                <a href="{{url('user/delete?id='.$user_detail->id)}}"class="btn btn-danger-outline btn-oval btn-sm mx-left" onclick="return confirm('You want to delete?')">
                    <i class="fa fa-trash"></i> Delete
                </a>
			</p>
		</div>
	</div>
    <hr>
    <div class="card-block">
        <form>
		    <div class="row">
            
                <div class="col-sm-9">  
                    <div class="form-group row">
                        <label class="col-sm-3">Name</label>
                        <div class="col-sm-9">
                            : {{$user_detail->name}}
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 form-control-label">Email</label>
                        <div class="col-sm-9">
                            : {{$user_detail->email}}
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 form-control-label">Phone Number</label>
                        <div class="col-sm-9">
                            : {{$user_detail->phone_number}}
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 form-control-label">Position</label>
                        <div class="col-sm-9">
                            : {{$user_detail->position}}
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 form-control-label">Role</label>
                        <div class="col-sm-9">
                            : {{$user_detail->rname}}
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 form-control-label">Location</label>
                        <div class="col-sm-9">
                            : {{$user_detail->location}}
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 form-control-label">Created By</label>
                        <div class="col-sm-9">
                            : {{$user_detail->created_by}}
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 form-control-label">Created At</label>
                        <div class="col-sm-9">
                            : {{$user_detail->created_at}}
                        </div>
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
			
            $("#menu_user").addClass("active open");
			$("#user_collapse").addClass("collapse in");
            $("#user").addClass("active");
        })
    </script>
@endsection