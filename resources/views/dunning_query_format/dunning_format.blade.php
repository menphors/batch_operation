@extends('layouts.master')

@section('content')
    <div class="card card-gray">
        <div class="card-header">
            <div class="header-block">
            <p class="title"> Batch Dunning Format </p>
            </div>
        </div>
        <hr>
        <div class="card-block">
            <div class="row">
                <div class="col-sm-2"></div>
                <div class="col-sm-8">
                @if(Session::has('fail'))
                    <div class="alert alert-danger" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <div>
                            {{session('fail')}}
                        </div>
                    </div>
                @endif
                    <form action="{{url('payment/request')}}" method="POST" enctype="multipart/form-data">
                    {{ csrf_field() }}                        
                        <div class="form-group row">
                            <label class="col-sm-4" for="import_file">Exclude Number</label>
                            <div class="col-sm-8">
                            <input type="file" name="import_file" id="import_file">
                            </div>
                        </div>                        
                        <div class="form-group row" id="specific_date" style="display:none;">
                            <label for="date" class="col-sm-4">Date<span class="text-danger">*</span></label>
                            <div class="col-sm-8">
                                <input type="file" id="date" name="date">
                            </div>
                        </div>
                        <!-- <div class="form-group row">
                            <label for="amount_type" class="col-sm-4">Payment Choice<span class="text-danger">*</span></label>
                            <div class="col-sm-8">
                                <select name="amount_type" id="amount_type" class="form-control" required>
                                    <option value="">Payment Choice</option>
                                    <option value="bill_amount" id="opt_bill_amount" >Bill Amount</option>
                                    <option value="amount_due" id="opt_amount_due" style="display:none;">Amount Due</option>
                                </select>
                            </div>
                        </div> -->                        
                        <div class="form-group row">
                            <label class="col-sm-4" for="remark">Remark</label>
                            <div class="col-sm-8">
                                <textarea class="form-control" name="remark" id="remark" cols="51" rows="3" placeholder="Input remark"></textarea>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-4">&nbsp;</label>
                            <div class="col-sm-8">
                                <button class="btn btn-primary btn-oval">
                                    <i class="fa fa-save"></i> Submit
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-sm-2"></div>
            </div>
        </div>
    </div>
@endsection
@section('js')
<script>
        //Script Hide and Show
        $(document).ready(function(){
			//Bill Cycle
			$('#bill_cycle').on('change', function() {
				if (this.value === 'specific_bill') {
					$("#specific_date").show();
                    // $("option#opt_amount_due").show();
				} else if(this.value === 'all_bill') {
					$("#specific_date").hide();
                    // $("option#opt_bill_amount").show();
				}
			});
		});
</script>
<script>
    $(document).ready(function () {
        $("#sidebar-menu li ").removeClass("active open");
        $("#sidebar-menu li ul li").removeClass("active");
        
        $("#menu_postpaid").addClass("active open");
        $("#postpaid_collapse").addClass("collapse in");
        $("#testing").addClass("active");
        
    });
</script>
@endsection