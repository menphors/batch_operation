@extends('layouts.master')

@section('content')
    <div class="card card-gray">
        <div class="card-header">
            <div class="header-block">
                <p class="title">Download PDF</p>
            </div>
        </div>
        <hr>
        <div class="card-block">
            <!-- start form -->
            <form action="#" method="POST" enctype="multipart/form-data">
                @csrf
                   <!-- accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" -->
                <div class="d-flex justify-content-end">
                <a href="<?php echo asset('storage/'.$fileName);?>" class="btn btn-success btn-oval btn-sm" >Download CSV File</a>
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
			
            $("#menu_batch_operation").addClass("active open");
			$("#batch_operation_collapse").addClass("collapse in");
            $("#Hot_Bill").addClass("active");
			
        });
    </script>
@endsection