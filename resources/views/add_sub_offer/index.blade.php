@extends('layouts.master')

@section('content')
    <div class="card card-gray">
        <div class="card-header">
            <div class="header-block">
                <p class="title">
                    Add Supplementary Offering
                </p>
            </div>
        </div>
        <hr>
        <div class="card-block">
            <div class="row">
                <div class="col-sm-12">
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
                    <form action="{{url('add_sub_offer/save')}}" method="POST" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <div class="form-group">
                            <label for="number">Phone Number List as Excel</label>
                            <input type="file"class="form-control-file" name="number" id="number" required>
                        </div>
                        <div class="form-group">
                            <label for="remark">Remark</label>
                            <textarea class="form-control" id="remark" name="remark" required> </textarea>
                        </div>
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-oval btn-success">Submit</button>
                        </div>   
                    </form>

                     <!-- report table -->
          <div class="card-header">
            <div class="header-block">
                <p class="title">Customer Information Report</p>
            </div>
        </div>
            <div class="table-flip-scroll">
                <table class="table table-sm table-bordered table-hover flip-content">
                    <thead class="flip-header">
                        <tr> 
                            <th scope="col">Remark</th>
                            <th scope="col">Executed_Date</th>
                            <th scope="col">Executed_By</th>
                            <th scope="col">Quantity</th>    
                            <th scope="col">BatchNumber</th>  
                            <th scope="col">Report</th>     
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($customer_info as $completed_trn)

                            <tr> 
                        <?php
                            if ($completed_trn->batch_number < 10)
                            $value = "0000000".$completed_trn->batch_number."";
                        else if ($completed_trn->batch_number< 100)
                            $value = "000000".$completed_trn->batch_number."";
                        else if ($completed_trn->batch_number< 1000)
                            $value = "0000".$completed_trn->batch_number."";
                        else if ($completed_trn->batch_number< 10000)
                            $value = "000".$completed_trn->batch_number."";
                        else if ($completed_trn->batch_number< 100000)
                            $value = "00".$completed_trn->batch_number."";
                        else if ($completed_trn->batch_number< 100000)
                            $value = "0".$completed_trn->batch_number."";
                        else if ($completed_trn->batch_number<   100000)
                            $value = "".$completed_trn->batch_number."";

                            $val=$completed_trn->batch_number;
                        ?>
                        
                                <td>{{$completed_trn->Remark}}</td>
                                <td>{{$completed_trn->Executed_Date}}</td>
                                <td>{{$completed_trn->user_name}}</td>
                                <td>{{$completed_trn->Amount}}</td>
                                <td>{{$value}}</td>
                           
                               <td scope='row'><a href='/Download?variableName={{$val}}'>Download</a></td>
                            </tr>
                        @endforeach
                    
                    </tbody>
                </table>
                {{$customer_info->links()}}
                
            </div>
            <!-- end report table -->

            
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
			
            $("#menu_batch_operation").addClass("active open");
			$("#batch_operation_collapse").addClass("collapse in");
            $("#add_sub_offer").addClass("active");
			
        })
    </script>
@endsection