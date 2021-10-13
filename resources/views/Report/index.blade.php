@extends('layouts.master')

@section('content')

            <!-- end form -->

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

            
  
@endsection

@section('js')
	<script>
        // A $( document ).ready() block.
        // $( document ).ready(function() {
        //     console.log( "ready!" );
        //     $('.form-check-input').change(function(val){

        //         if(val.target.value == "execute_schedule"){
        //             $("#execute_schedule").prop('disabled', false);
        //         }else if(val.target.value == "execute_immediately"){
        //             $("#execute_schedule").prop('disabled', true);
        //         }

        //         if(val.target.value == "effective_schedule"){
        //             $("#effective_schedule").prop('disabled', false);
        //         }else if(val.target.value == "next_bill_cycle"){
        //             $("#effective_schedule").prop('disabled', true);
        //         }else if(val.target.value == "effective_immediately"){
        //             $("#effective_schedule").prop('disabled', true);
        //         }
        //     })
        // });
        $(document).ready(function () {
            $("#sidebar-menu li ").removeClass("active open");
			$("#sidebar-menu li ul li").removeClass("active");
			
            $("#menu_report").addClass("active open");
			$("#report_collapse").addClass("collapse in");
            $("#customer_info_report").addClass("active");
			
        })
    </script>
@endsection
