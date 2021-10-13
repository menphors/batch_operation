@extends('layouts.master')

@section('content')
    <div class="card card-gray">
        <div class="card-header">
            <div class="header-block">
                <p class="title">Completed</p>
            </div>
        </div>
        <hr>
        <div class="card-block">
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
                        @foreach($completed_trns as $completed_trn)
                        <tr> 
                            <td> {{$completed_trn->offering_id}}</td>
                            <td>{{$completed_trn->remark}}</td>
                            <td>{{$completed_trn->effective_date}}</td>
                            <td>{{$completed_trn->execute_date}}</td>
                            <td>{{$completed_trn->created_at}}</td>
                            <td>{{$completed_trn->user_name}}</td>
                        
                        </tr>
                        @endforeach
                    
                    </tbody>
                </table>
                {{$completed_trns->links()}}
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
            $("#completed").addClass("active");
			
        })
    </script>
@endsection