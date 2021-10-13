@extends('layouts.master')

@section('content')
    <div class="card card-gray">
        <div class="card-header">
            <div class="header-block">
                <p class="title">To Do</p>
            </div>
        </div>
        <hr>
        <div class="table-flip-scroll">
                <table class="table table-sm table-bordered table-hover flip-content">
                    <thead class="flip-header">
                        <tr> 
                            <th scope="col">New Offering</th>
                            <th scope="col">Remark</th>
                            <th scope="col">Effective Date</th>
                            <th scope="col">Execute Date</th>       
                            <th scope="col">Create Date</th>
                            <th scope="col">By</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($todos as $todo)
                        <tr> 
                            <td> {{$todo->offering_id}}</td>
                            <td>{{$todo->remark}}</td>
                            <td>{{$todo->effective_date}}</td>
                            <td>{{$todo->execute_date}}</td>
                            <td>{{$todo->created_at}}</td>
                            <td>{{$todo->user_name}}</td>
                        
                        </tr>
                        @endforeach
                    
                    </tbody>
                </table>
                {{$todos->links()}}
            </div>
        </div>
    </div>
@endsection

@section('js')
	<script>
        $(document).ready(function () {
            $("#sidebar-menu li ").removeClass("active open");
			$("#sidebar-menu li ul li").removeClass("active");
			
            $("#menu_report").addClass("active open");
			$("#report_collapse").addClass("collapse in");
            $("#todo").addClass("active");
			
        })
    </script>
@endsection