@extends('layouts.master')

@section('content')
    <div class="card card-gray">
        <div class="card-header">
            <div class="header-block">
                <p class="title">Hot Bill</p>
            </div>
        </div>
        <hr>
        <div class="card-block">
            <!-- start form -->
            <form action="{{route('Hot_Bill')}}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label for="number">Phone Number List as Excel</label>
                    <input type="file"class="form-control-file"  name="number" id="number" required>
                </div>
                <div class="form-group">
                    <label for="new_primary_offering">Remark</label>
                    <textarea class="form-control" id="remark" name="remark" required> </textarea>
                </div>
                    <!-- accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" -->
                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-oval btn-success">Submit</button>
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