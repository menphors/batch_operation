@extends('layouts.master')

@section('content')
    {{ csrf_field() }}
    <div class="card card-gray">
        <div class="card-header">
            <div class="header-block">
                <p class="title">Set Permission</p>
                <a href="{{url('user/function')}}"class="btn btn-primary-outline btn-oval btn-sm mx-left">
                    <i class="fa fa-reply"></i> Back
                </a>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-1">
                <a href="{{url('user/function/permission/create/'.$p->id)}}"class="btn btn-primary btn-oval btn-sm mx-left">
                        <i class="fa fa-plus-circle"></i> Create
                    </a>
            </div>
            <div class="col-sm-8"></div>
            <div class="col-sm-3">
                <p class="title">User : {{$p->name}}</p>
            </div>
        </div>
        <hr>
        <div class="card-block">
            <div class="table-flip-scroll">
                <table class="table table-sm table-bordered table-hover flip-content">
                    <thead class="flip-header">
                        <tr>
                            <th scope="col">Function</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $page = @$_GET['page'];
                            if(!$page)
                            {
                                $page = 1;
                            }
                            $i = config('app.row') * ($page-1) + 1;
                        ?>
                        @foreach($permission as $p)
                        <tr aid="{{$p->id}}" uid="{{$p->user_id}}" sid="{{$p->active?$p->active:'0'}}">
                            <td>{{$p->fname}}</td>
                            <td>
                                <input type="checkbox" value="{{$p->active?'1':'0'}}" {{ $p->active==1?'checked':'' }} 
                                onchange=save(this)>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                {{$permission->links()}}
            </div>
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
            $("#user_permission").addClass("active");
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
                active: active
            };
            $.ajax({
                type: 'POST',
                url: burl + "/user/function/access",
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