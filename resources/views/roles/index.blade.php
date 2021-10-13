@extends('layouts.master')


@section('content')
    <div class="card card-gray">
        <div class="card-header">
            <div class="header-block">
                <p class="title"> Role
                    <a href="{{url('role/create')}}"class="btn btn-primary btn-oval btn-sm mx-left">
                        <i class="fa fa-plus-circle"></i> Create
                    </a>
                </p>
            </div>
        </div>
        <hr>
        <div class="card-block">
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
            <!-- report table -->
            <div class="table-flip-scroll">
                <table class="table table-sm table-bordered table-hover flip-content">
                    <thead class="flip-header">
                        <tr> 
                            <th scope="col">Name</th>       
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $page = @$_GET['page'];
                            if(!$page)
                            {
                                $page = 1;
                            }
                            $i = config('app.row') * ($page-1) + 1;
                        ?>
                        @foreach($roles as $role)
                        <tr> 
                        
                            <td>{{$role->name}}</td>                                
                            <td>
                                <a href="{{url('role/delete?id='.$role->id)}}" title="Delete" class='text-danger'
                                 onclick="return confirm('You want to delete?')">
                                    <i class="fa fa-trash"></i>
                                </a>&nbsp;&nbsp;
                                <a href="{{url('role/edit/'.$role->id)}}" class="text-success" title="Edit">
                                    <i class="fa fa-edit"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                {{$roles->links()}}
            </div>
            <!-- end report table -->
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