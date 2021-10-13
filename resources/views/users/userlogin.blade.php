@extends('layouts.master')


@section('content')
    {{ csrf_field() }}
    <div class="card card-gray">
        <div class="card-header">
            <div class="header-block">
                <p class="title">
                    Access User
                </p>
            </div>
        </div>
        <hr>
        <div class="card-block">
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
            <!-- report table -->
            <div class="table-flip-scroll">
                <table class="table table-sm table-bordered table-hover flip-content">
                    <thead class="flip-header">
                        <tr> 
                            <th scope="col">Name</th>
                            <th scope="col">Access</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $a)
                        <tr aid="{{$a->id}}" sid="{{$a->status?$a->status:'0'}}"> 
                            <td>{{$a->name}}</td>
                            <td>
                                <input type="checkbox" value="{{$a->status?'1':'0'}}" {{ $a->status==1?'checked':'' }} 
                                onchange="save(this)">
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <!-- end report table -->
        </div>
    </div>
@endsection

@section('js')
	<script>
        var burl = "{{url('/')}}";
        $(document).ready(function () {
            $("#sidebar-menu li ").removeClass("active open");
			$("#sidebar-menu li ul li").removeClass("active");
			
            $("#menu_user").addClass("active open");
			$("#user_collapse").addClass("collapse in");
            $("#userlogin").addClass("active");
			
        });

        function save(obj)
        {   
            let token = $("input[name='_token']").val();
            let val = $(obj).val();
            if(val==1)
            {
                $(obj).val(0);
            }
            else{
                $(obj).val(1);
            }

            let tr = $(obj).parent().parent();
            let aid = $(tr).attr('aid');
            let sid = $(tr).attr('sid');
            let td = $(tr).find('td');
            let active = $(td[1]).children('input').val();
            let data = {
                id: aid,
                status: active
            };
            $.ajax({
                type: 'POST',
                url: burl + "/user/access",
                data: data,
                beforeSend: function(request){
                    return request.setRequestHeader('X-CSRF-Token', token);
                },
                success: function(sms)
                {
                    $(tr).attr('aid', sms);
                }
            });
        }
    </script>
@endsection