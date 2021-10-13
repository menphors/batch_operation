@extends('layouts.master')
<style>
.switch {
  position: relative;
  display: inline-block;
  width: 40px;
  height: 24px;
}

.switch input { 
  opacity: 0;
  width: 0;
  height: 0;
}

.slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: red;
  -webkit-transition: .4s;
  transition: .4s;
}

.slider:before {
  position: absolute;
  content: "";
  height: 16px;
  width: 16px;
  left: 2px;
  bottom: 4px;
  background-color: white;
  -webkit-transition: .4s;
  transition: .4s;
}

input:checked + .slider {
  background-color: #339900;
}

input:focus + .slider {
  box-shadow: 0 0 1px #2196F3;
}

input:checked + .slider:before {
  -webkit-transform: translateX(20px);
  -ms-transform: translateX(20px);
  transform: translateX(20px);
}

/* Rounded sliders */
.slider.round {
  border-radius: 14px;
}

.slider.round:before {
  border-radius: 50%;
}
</style>
@section('content')
 
    <div class="card card-gray">
        <div class="card-header">
            <div class="header-block">
                <p class="title">Clear Report</p>
            </div>
        </div>
        <hr>
        <div class="card-block">
            <!-- start form -->
            <form  action="{{route('Clear_Report')}}" method="POST" enctype="multipart/form-data" id='sub_mit'>
            @csrf
            <div class="container">
                <div class="row">
                    <div class="col-sm">
                    <div class="col-sm">
                            <div class="row">
                                <div class="col-2">
                                <label class="switch">
                                @if(isset($On))
                                   <input type="checkbox" class="toggle" name="del_option"  onclick="getValue()">
                                @foreach($On as $on)
                                {{$on}}
                                @endforeach
                                @else
                                <input type="checkbox" class="toggle" name="del_option" onclick="getValue()">
                                @endif
                                
                                    <span class="slider round"></span>
                                    <noscript><input type="submit" value="Submit"></noscript>
                                </label>
                                </div>
                                <div class="col-10">
                                <label  id = "lbl_Switch">
                                Delete data that is 30 days and older.
                                </label>
                                </div>
                            </div>
                      </div>
                      
                    </div>
                    <div class="col-sm">
                    <div class="d-flex justify-content-end">
                    <button type="submit" values='1' id="btn_" class="btn btn-oval btn-warning">Clear Data Report</button>
                </div>    
                    </div>
                </div>
                </div>
                                <!-- <div class="form-group">
                    <label for="new_primary_offering">Remark</label>
                    <textarea class="form-control" id="remark" name="remark" required> </textarea>
                </div> -->
                <!-- Default checked -->
                
                           
            </form>
            <!-- end form -->
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
@endsection

@section('js')
	<script>
           
           function getValue()
            {
            // $('form').submit(function(event){
            //     $('input[type=checkbox]').prop('checked', function(index, value){
            //         return !value;
            //     });
            //  });
           var result = document.getElementsByClassName("toggle")[0].checked ? 'yes' : 'no'
            if(result=='yes'){
                document.getElementById('sub_mit').submit();
                document.getElementById('lbl_Switch').innerHTML  ="Toggle this switch option to delete.";
                document.getElementById('btn_').style.visibility="hidden";
            }else{
                // document.getElementById('sub_mit').submit();
                document.getElementById('lbl_Switch').innerHTML  ="Delete data that is 30 days and older.";
                document.getElementById('btn_').style.visibility="visible";
            }
            }
        $(document).ready(function () {
            $("#sidebar-menu li ").removeClass("active open");
			$("#sidebar-menu li ul li").removeClass("active");
			
            $("#menu_batch_operation").addClass("active open");
			$("#batch_operation_collapse").addClass("collapse in");
            $("#Clear_Report").addClass("active");
			
        });
    </script>
@endsection