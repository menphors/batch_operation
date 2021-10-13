@extends('layouts.master')

@section('content')
    <h5>Logged By :
        <strong class="text-danger">{{Auth::user()->name}}</strong> 
        is <strong class="text-success">{{Auth::user()->position}}</strong>
    </h5>

@endsection
@section('js')
	<script>
        $(document).ready(function () {
            $("#sidebar-menu li").removeClass('active');
		    $("#menu_dashboard").addClass('active');	
        });
    </script>
@endsection