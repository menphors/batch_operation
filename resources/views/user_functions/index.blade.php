@extends('layouts.master')

@section('content')
    <div class="card card-gray">
        <div class="card-header">
            <div class="header-block">
                <p class="title">User Permission
                </p>
            </div>
        </div>
        <hr>
        <div class="card-block">
            <div class="table-flip-scroll">
                <table class="table table-sm table-bordered table-hover flip-content">
                    <thead class="flip-header">
                        <tr> 
                            <th scope="col">ID</th>
                            <th scope="col">Name</th>
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
                        @foreach($users as $user)
                            <tr>
                                <td>{{$user->id}}</td>
                                <td>
                                    <a class="text-dark" href="{{url('user/function/permission/'.$user->id)}}">
                                        {{$user->name}}
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                {{$users->links()}}
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