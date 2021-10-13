@extends('layouts.app')

@section('content')
<div class="container">
<ul class="nav nav-tabs" id="myTab" role="tablist">
@php
$i=0;
@endphp
@foreach($functions as $f)
@php
$i++;
@endphp
  <li class="nav-item">
    <a class="nav-link @if($i==1) active @endif" id="{{$f->path}}-tab" data-toggle="tab" href="{{$f->path}}" role="tab" aria-controls="home" aria-selected="true">{{$f->function_name}}</a>
  </li>
  @endforeach
  </ul>

<div class="tab-content" id="myTabContent">
  <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
          <div class="card">
                <div class="card-header">Change Pri Offering</div>

                <div class="card-body">
                  
                <!-- start form -->
                        <form action="{{route('change_user_primary_offering')}}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group">
                                    <label for="msisdns">Phone Number List as Excel</label>
                                    <input type="file"  
                                           class="form-control-file" name="msisdns" id="msisdns" required>
                            </div>
                            <!-- accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" -->
                            <div class="form-group">
                                <label for="new_primary_offering">New Primary Offering</label>
                                <input type="text" class="form-control" name="new_primary_offering" id="new_primary_offering" required>
                             </div>
                             <div class="form-group">
                                <label for="effective_mode">Effective Mode &nbsp;</label>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="effective_mode" id="inlineRadio1" value="effective_immediately" checked>
                                        <label class="form-check-label" for="inlineRadio1">Immediately</label>
                                    </div>

                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="effective_mode" id="inlineRadio1" value="next_bill_cycle"  >
                                        <label class="form-check-label" for="inlineRadio1">Next Bill Cycle</label>
                                    </div>

                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="effective_mode" id="inlineRadio2" value="effective_schedule">
                                        <label class="form-check-label" for="inlineRadio2">Appointed</label>
                                    </div>
                                        
                                    <div class="form-group">
                                         <input disabled type="datetime-local" class="form-control" id="effective_schedule" name="effective_schedule" required>
                                    </div>
                             </div>

                             <div class="form-group">
                                <label for="effective_mode">Execution Mode &nbsp;</label>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="execution_mode" id="inlineRadio4" value="execute_immediately" checked>
                                        <label class="form-check-label" for="inlineRadio4">Immediately</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="execution_mode" id="inlineRadio5" value="execute_schedule">
                                        <label class="form-check-label" for="inlineRadio5">Run at</label>
                                    </div>
                                        
                                    <div class="form-group">
                                         <input disabled type="datetime-local" class="form-control" id="execute_schedule" name="execute_schedule" required>
                                    </div>
                             </div>
  
                             <div class="form-group">
                                <label for="new_primary_offering">Remark</label>
                                <textarea class="form-control" id="remark" name="remark" required> </textarea>
                             </div>
                                
                             <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-success">Submit</button>
                             </div>     

                             
                        </form>
                    <!-- end form -->
  
                </div>
            </div>
            <!-- end card -->
  </div>

  <!-- Change CustInfo -->

  <div class="tab-pane fade" id="cust" role="tabpanel" aria-labelledby="cust-tab">
          <div class="card">
                <div class="card-header">Change Cust Info</div>

                    <div class="card-body">
                  
                <!-- start form -->
                        <form action="{{route('change_cust_info')}}" 
                                method="POST" 
                                enctype="multipart/form-data">
                                
                            @csrf
                            <div class="form-group">
                                    <label for="cust_id">Phone Number List as Excel</label>
                                    <input type="file"  
                                           class="form-control-file" name="cust_id" id="cust_id" required>
                            </div>
                            <div class="form-group">
                                <label for="new_primary_offering">Remark</label>
                                <textarea class="form-control" id="remark" name="remark" required> </textarea>
                             </div>

                            <!-- accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" -->

                                
                             <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-success">Submit</button>
                             </div>     

                             
                        </form>
                        <!-- end form -->

                        <!-- report table -->
                        <div class="card-body">
                                    <table class="table">
                                        <thead>
                                            <tr> 
                                                <th scope="col">Remark</th>
                                                <th scope="col">Executed_Date</th>
                                                <th scope="col">Executed_By</th>
                                                <th scope="col">Quantity</th>       
                                            </tr>
                                        </thead>
                                        <tbody>
                                           @foreach($customer_info as $completed_trn)
                                           <tr> 
                                                <td>{{$completed_trn->Remark}}</td>
                                                <td>{{$completed_trn->Executed_Date}}</td>
                                                <td>{{$completed_trn->user_name}}</td>
                                                <td>{{$completed_trn->Amount}}</td>                                            
                                            </tr>
                                           @endforeach
                                        
                                        </tbody>
                                    </table>
                                    {{$customer_info->links()}}
                            </div>
                        <!-- end report table -->
                    </div>
                
          </div>
            <!-- end card -->
  </div>

  <!-- End Change CustInfo--> 


  <!-- Change EVC Info -->

  <div class="tab-pane fade" id="evc" role="tabpanel" aria-labelledby="evc-tab">
          <div class="card">
                <div class="card-header">Change EVC Info</div>

                    <div class="card-body">
                  
                <!-- start form -->
                        <form action="{{route('change_evc_info')}}" 
                                method="POST" 
                                enctype="multipart/form-data">
                                
                            @csrf
                            <div class="form-group">
                                    <label for="number">Phone Number List as Excel</label>
                                    <input type="file"  
                                           class="form-control-file" name="number" id="number" required>
                            </div>
                            <div class="form-group">
                                <label for="new_primary_offering">Remark</label>
                                <textarea class="form-control" id="remark" name="remark" required> </textarea>
                             </div>

                            <!-- accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" -->

                                
                             <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-success">Submit</button>
                             </div>     

                             
                        </form>
                        <!-- end form -->

                        <!-- report table -->
                        <div class="card-body">
                                    <table class="table">
                                        <thead>
                                            <tr> 
                                                <th scope="col">Remark</th>
                                                <th scope="col">Executed_Date</th>
                                                <th scope="col">Executed_By</th>
                                                <th scope="col">Quantity</th>       
                                            </tr>
                                        </thead>
                                        <tbody>
                                           @foreach($customer_info as $completed_trn)
                                           <tr> 
                                                <td>{{$completed_trn->Remark}}</td>
                                                <td>{{$completed_trn->Executed_Date}}</td>
                                                <td>{{$completed_trn->user_name}}</td>
                                                <td>{{$completed_trn->Amount}}</td>                                            
                                            </tr>
                                           @endforeach
                                        
                                        </tbody>
                                    </table>
                                    {{$customer_info->links()}}
                            </div>
                        <!-- end report table -->
                    </div>
                
          </div>
            <!-- end card -->
  </div>

  <!-- End Change EVC Info-->


  <!--Change Dealer info -->


   <div class="tab-pane fade" id="dealer" role="tabpanel" aria-labelledby="dealer-tab">
          <div class="card">
                <div class="card-header">Change Dealer Info</div>

                    <div class="card-body">
                  
                <!-- start form -->
                        <form action="{{route('change_dealer_info')}}" 
                                method="POST" 
                                enctype="multipart/form-data">
                                
                            @csrf
                            <div class="form-group">
                                    <label for="number">Phone Number List as Excel</label>
                                    <input type="file"  
                                           class="form-control-file" name="number" id="number" required>
                            </div>
                            <div class="form-group">
                                <label for="new_primary_offering">Remark</label>
                                <textarea class="form-control" id="remark" name="remark" required> </textarea>
                             </div>

                            <!-- accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" -->

                                
                             <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-success">Submit</button>
                             </div>     

                             
                        </form>
                        <!-- end form -->

                        <!-- report table -->
                        <div class="card-body">
                                    <table class="table">
                                        <thead>
                                            <tr> 
                                                <th scope="col">Remark</th>
                                                <th scope="col">Executed_Date</th>
                                                <th scope="col">Executed_By</th>
                                                <th scope="col">Quantity</th>       
                                            </tr>
                                        </thead>
                                        <tbody>
                                           @foreach($customer_info as $completed_trn)
                                           <tr> 
                                                <td>{{$completed_trn->Remark}}</td>
                                                <td>{{$completed_trn->Executed_Date}}</td>
                                                <td>{{$completed_trn->user_name}}</td>
                                                <td>{{$completed_trn->Amount}}</td>                                            
                                            </tr>
                                           @endforeach
                                        
                                        </tbody>
                                    </table>
                                    {{$customer_info->links()}}
                            </div>
                        <!-- end report table -->
                    </div>
                
          </div>
            <!-- end card -->
  </div>

  <!-- End Change Dealer Info-->

   <!--Start DeactivateSub -->

   <div class="tab-pane fade" id="deactivate" role="tabpanel" aria-labelledby="deactivate-tab">
          <div class="card">
                <div class="card-header">Deactivate Sub</div>

                    <div class="card-body">
                  
                <!-- start form -->
                        <form action="{{route('deactivate_sub')}}" 
                                method="POST" 
                                enctype="multipart/form-data">
                                
                            @csrf
                            <div class="form-group">
                                    <label for="number">Phone Number List as Excel</label>
                                    <input type="file"  
                                           class="form-control-file" name="number" id="number" required>
                            </div>
                            <div class="form-group">
                                <label for="new_primary_offering">Remark</label>
                                <textarea class="form-control" id="remark" name="remark" required> </textarea>
                             </div>

                            <!-- accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" -->

                                
                             <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-success">Submit</button>
                             </div>     

                             
                        </form>
                        <!-- end form -->

                        <!-- report table -->
                        <div class="card-body">
                                    <table class="table">
                                        <thead>
                                            <tr> 
                                                <th scope="col">Remark</th>
                                                <th scope="col">Executed_Date</th>
                                                <th scope="col">Executed_By</th>
                                                <th scope="col">Quantity</th>       
                                            </tr>
                                        </thead>
                                        <tbody>
                                           @foreach($customer_info as $completed_trn)
                                           <tr> 
                                                <td>{{$completed_trn->Remark}}</td>
                                                <td>{{$completed_trn->Executed_Date}}</td>
                                                <td>{{$completed_trn->user_name}}</td>
                                                <td>{{$completed_trn->Amount}}</td>                                            
                                            </tr>
                                           @endforeach
                                        
                                        </tbody>
                                    </table>
                                    {{$customer_info->links()}}
                            </div>
                        <!-- end report table -->
                    </div>
                
          </div>
            <!-- end card -->
  </div>

  <!-- Start Change Pre To Post-->


  <div class="tab-pane fade" id="changepretopost" role="tabpanel" aria-labelledby="changepretopost-tab">
          <div class="card">
                <div class="card-header">Pre to Post</div>

                    <div class="card-body">
                  
                <!-- start form -->
                        <form action="{{route('change_pre_to_post')}}" 
                                method="POST" 
                                enctype="multipart/form-data">
                                
                            @csrf
                            <div class="form-group">
                                    <label for="number">Phone Number List as Excel</label>
                                    <input type="file"  
                                           class="form-control-file" name="number" id="number" required>
                            </div>
                            <div class="form-group">
                                <label for="new_primary_offering">Remark</label>
                                <textarea class="form-control" id="remark" name="remark" required> </textarea>
                             </div>

                            <!-- accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" -->

                                
                             <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-success">Submit</button>
                             </div>     

                             
                        </form>
                        <!-- end form -->

                        <!-- report table -->
                        <div class="card-body">
                                    <table class="table">
                                        <thead>
                                            <tr> 
                                                <th scope="col">Remark</th>
                                                <th scope="col">Executed_Date</th>
                                                <th scope="col">Executed_By</th>
                                                <th scope="col">Quantity</th>       
                                            </tr>
                                        </thead>
                                        <tbody>
                                           @foreach($customer_info as $completed_trn)
                                           <tr> 
                                                <td>{{$completed_trn->Remark}}</td>
                                                <td>{{$completed_trn->Executed_Date}}</td>
                                                <td>{{$completed_trn->user_name}}</td>
                                                <td>{{$completed_trn->Amount}}</td>                                            
                                            </tr>
                                           @endforeach
                                        
                                        </tbody>
                                    </table>
                                    {{$customer_info->links()}}
                            </div>
                        <!-- end report table -->
                    </div>
                
          </div>
            <!-- end card -->
  </div>

  <!-- End Change Pre to Post-->


   <!-- Start Change Post To Pre-->


   <div class="tab-pane fade" id="changeposttopre" role="tabpanel" aria-labelledby="changeposttopre-tab">
          <div class="card">
                <div class="card-header">Post to Pre</div>

                    <div class="card-body">
                  
                <!-- start form -->
                        <form action="{{route('change_post_to_pre')}}" 
                                method="POST" 
                                enctype="multipart/form-data">
                                
                            @csrf
                            <div class="form-group">
                                    <label for="number">Phone Number List as Excel</label>
                                    <input type="file"  
                                           class="form-control-file" name="number" id="number" required>
                            </div>
                            <div class="form-group">
                                <label for="new_primary_offering">Remark</label>
                                <textarea class="form-control" id="remark" name="remark" required> </textarea>
                             </div>

                            <!-- accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" -->

                                
                             <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-success">Submit</button>
                             </div>     

                             
                        </form>
                        <!-- end form -->

                        <!-- report table -->
                        <div class="card-body">
                                    <table class="table">
                                        <thead>
                                            <tr> 
                                                <th scope="col">Remark</th>
                                                <th scope="col">Executed_Date</th>
                                                <th scope="col">Executed_By</th>
                                                <th scope="col">Quantity</th>      
                                            </tr>
                                        </thead>
                                        <tbody>
                                           @foreach($customer_info as $completed_trn)
                                           <tr> 
                                                <td>{{$completed_trn->Remark}}</td>
                                                <td>{{$completed_trn->Executed_Date}}</td>
                                                <td>{{$completed_trn->user_name}}</td>
                                                <td>{{$completed_trn->Amount}}</td>                                            
                                            </tr>
                                           @endforeach
                                        
                                        </tbody>
                                    </table>
                                    {{$customer_info->links()}}
                            </div>
                        <!-- end report table -->
                    </div>
                
          </div>
            <!-- end card -->
  </div>

  <!-- End Change Post to Pre-->


  <!-- Start Change Acct Info-->


  <div class="tab-pane fade" id="changeacctinfo" role="tabpanel" aria-labelledby="changeacctinfo-tab">
          <div class="card">
                <div class="card-header">Change Acct info</div>

                    <div class="card-body">
                  
                <!-- start form -->
                        <form action="{{route('change_acct_info')}}" 
                                method="POST" 
                                enctype="multipart/form-data">
                                
                            @csrf
                            <div class="form-group">
                                    <label for="number">Phone Number List as Excel</label>
                                    <input type="file"  
                                           class="form-control-file" name="number" id="number" required>
                            </div>
                            <div class="form-group">
                                <label for="new_primary_offering">Remark</label>
                                <textarea class="form-control" id="remark" name="remark" required> </textarea>
                             </div>

                            <!-- accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" -->

                                
                             <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-success">Submit</button>
                             </div>     

                             
                        </form>
                        <!-- end form -->

                        <!-- report table -->
                        <div class="card-body">
                                    <table class="table">
                                        <thead>
                                            <tr> 
                                                <th scope="col">Remark</th>
                                                <th scope="col">Executed_Date</th>
                                                <th scope="col">Executed_By</th>
                                                <th scope="col">Quantity</th>       
                                            </tr>
                                        </thead>
                                        <tbody>
                                           @foreach($customer_info as $completed_trn)
                                           <tr> 
                                                <td>{{$completed_trn->Remark}}</td>
                                                <td>{{$completed_trn->Executed_Date}}</td>
                                                <td>{{$completed_trn->user_name}}</td>
                                                <td>{{$completed_trn->Amount}}</td>                                            
                                            </tr>
                                           @endforeach
                                        
                                        </tbody>
                                    </table>
                                    {{$customer_info->links()}}
                            </div>
                        <!-- end report table -->
                    </div>
                
          </div>
            <!-- end card -->
  </div>

  <!-- End Change Acct Info-->


  <!--Start activateSub -->

  <div class="tab-pane fade" id="activate" role="tabpanel" aria-labelledby="activate-tab">
          <div class="card">
                <div class="card-header">Activate Sub</div>

                    <div class="card-body">
                  
                <!-- start form -->
                        <form action="{{route('activate_sub')}}" 
                                method="POST" 
                                enctype="multipart/form-data">
                                
                            @csrf
                            <div class="form-group">
                                    <label for="number">Phone Number List as Excel</label>
                                    <input type="file"  
                                           class="form-control-file" name="number" id="number" required>
                            </div>
                            <div class="form-group">
                                <label for="new_primary_offering">Remark</label>
                                <textarea class="form-control" id="remark" name="remark" required> </textarea>
                             </div>

                            <!-- accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" -->

                                
                             <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-success">Submit</button>
                             </div>     

                             
                        </form>
                        <!-- end form -->

                        <!-- report table -->
                        <div class="card-body">
                                    <table class="table">
                                        <thead>
                                            <tr> 
                                                <th scope="col">Remark</th>
                                                <th scope="col">Executed_Date</th>
                                                <th scope="col">Executed_By</th>
                                                <th scope="col">Quantity</th>       
                                            </tr>
                                        </thead>
                                        <tbody>
                                           @foreach($customer_info as $completed_trn)
                                           <tr> 
                                                <td>{{$completed_trn->Remark}}</td>
                                                <td>{{$completed_trn->Executed_Date}}</td>
                                                <td>{{$completed_trn->user_name}}</td>
                                                <td>{{$completed_trn->Amount}}</td>                                            
                                            </tr>
                                           @endforeach
                                        
                                        </tbody>
                                    </table>
                                    {{$customer_info->links()}}
                            </div>
                        <!-- end report table -->
                    </div>
                
          </div>
            <!-- end card -->
  </div>

  <!--End Activatesub-->




  <!-- end tab -->

  <div class="tab-pane fade" id="todo" role="tabpanel" aria-labelledby="todo-tab">
  <div class="card">
                        <div class="card-header">TODO Batch</div>
                            <div class="card-body">
                                    <table class="table">
                                        <thead> 
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
                            <!-- end card body -->
                        </div>
                    <!-- end card -->
        </div>
        <!-- end tab -->

  <div class="tab-pane fade" id="completed" role="tabpanel" aria-labelledby="completed-tab">
  <div class="card">
                    <div class="card-header">Completed Transaction</div>

                            <div class="card-body">
                                    <table class="table">
                                        <thead>
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
                            <!-- end card body -->
                </div>
                    <!-- end card -->
  </div>
  
</div>

 
 

<script> 

    @if(Session::has('success'))
        swal({
            title: "Success!",
            text:  "{{Session::get('success')}}",
            icon: "success",
        });
    @endif
    

    // A $( document ).ready() block.
$( document ).ready(function() {
    console.log( "ready!" );
    $('.form-check-input').change(function(val){

        if(val.target.value == "execute_schedule"){
            $("#execute_schedule").prop('disabled', false);
        }else if(val.target.value == "execute_immediately"){
            $("#execute_schedule").prop('disabled', true);
        }

        if(val.target.value == "effective_schedule"){
            $("#effective_schedule").prop('disabled', false);
        }else if(val.target.value == "next_bill_cycle"){
            $("#effective_schedule").prop('disabled', true);
        }else if(val.target.value == "effective_immediately"){
            $("#effective_schedule").prop('disabled', true);
        }
    })
});
</script>

@endsection
