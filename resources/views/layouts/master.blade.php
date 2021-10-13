
<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'Laravel') }}</title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- Styles -->
        <!-- <link href="{{ asset('css/app.css') }}" rel="stylesheet"> -->
        <link rel="icon" href="{{asset('admin/images/smart.png')}}">
        <link rel="stylesheet" href="{{asset('admin/css/vendor.css')}}">
        <link rel="stylesheet" href="{{asset('admin/css/app-green.css')}}">
        <link rel="stylesheet" href="{{asset('admin/fontawesome/css/all.min.css')}}" >
        <link rel="stylesheet" href="{{asset('admin/css/custom.css')}}" >

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
        <!-- Fonts
        <link rel="dns-prefetch" href="//fonts.gstatic.com">
        <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet" type="text/css"> -->
        
        <!-- Ubuntu Font Customize -->
        <link rel="stylesheet" href="{{asset('admin/css/style.css')}}">
        
        <!-- js chosen -->
        <link rel="stylesheet" href="{{asset('css/component-chosen.css')}}">
        <!-- JQuery DataTable Css -->
        <link href="{{ asset('admin/js/jquery-datatable/skin/bootstrap/css/dataTables.bootstrap.css') }}" rel="stylesheet">
    </head>
    <body>
        <div class="main-wrapper">
            <div class="app" id="app">
                <header class="header">
                    <div class="header-block header-block-collapse d-lg-none d-xl-none">
                        <button class="collapse-btn" id="sidebar-collapse-btn">
                            <i class="fa fa-bars"></i>
                        </button>
                    </div>
                    <div class="header-block header-block-search">
                        <strong>
                        CRM & BILLING DEPARTMENT
                        </strong> <br>
                        <strong>in-house web portal</strong>
                    </div>
                    <div class="header-block">
                        <!-- <img src="{{asset('admin/images/smart.png')}}" width="100"> -->
                    </div>
                    <div class="header-block header-block-nav">
                        <ul class="nav-profile">
                            <li class="profile dropdown">
                                <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
                                <!-- <i class="fas fa-user"></i> <span class="name">{{Auth::user()->name}}</span> -->
                                </a>
                                <div class="dropdown-menu profile-dropdown-menu" aria-labelledby="dropdownMenu1">
                                    <a class="dropdown-item" href="{{url('profile/'.Auth::user()->id.'/'.Auth::user()->name)}}">
                                        <i class="fa fa-user icon"></i> Profile 
                                    </a>
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                        onclick="event.preventDefault();
                                        document.getElementById('logout-form').submit();">
                                        <i class="fa fa-power-off icon"></i>{{ __('Logout') }}
                                    </a>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                    @csrf
                                    </form>
                                </div>
                            </li>
                        </ul>
                    </div>
                </header>
                <aside class="sidebar">
                    <div class="sidebar-container">
                        <div class="sidebar-header">
                            <div class="brand">
                                <div class="row">
                                    <div class="col-sm-2"></div>
                                    <div class="col-sm-3">
                                        <a href="{{ url('dashboard') }}"  class="navbar-brand" title="smart logo">
                                            <img src="{{asset('admin/images/smart.png')}}" alt="smart logo" width="100" >
                                        </a>
                                    </div>
                                    <div class="col-sm-3"></div>
                                </div>
                            </div>
                        </div>
                        <nav class="menu">
                            <ul class="sidebar-menu metismenu" id="sidebar-menu">
                                <li id="menu_dashboard">
                                    <a href="{{url('/')}}">
                                    <i class="fa fa-home"></i> Dashboard
                                    </a>
                                </li>
                                <li id='menu_batch_operation'>
                                    <a href="#"><i class="fab fa-ubuntu"></i>
                                    Batch Operation<i class="fa arrow"></i> 
                                    </a>
                                    <ul class="sidebar-nav" id="batch_operation_collapse" >
                                      
                                        <li id="Hot_Bill">
                                            <a href="{{ url('Hot_Bill') }}">Hot Bill</a>
                                        </li>
                                      
                                        @canview('activate')
                                        <li id="activate">
                                            <a href="{{ url('activate_sub') }}"> Activate Subcriber</a>
                                        </li>
                                        @endcanview
                                        @canview('home')
                                        <li id="home">
                                            <a href="{{ url('changeprimaryoffering') }}"> Change Primary Offering</a>
                                        </li>
                                        @endcanview
                                        <!-- @canview('changeacctinfo')
                                        <li id="menu_acc_info" id="changeacctinfo">
                                            <a href="{{ url('change_acct_info') }}"> Change Account Information</a>
                                        </li>
                                        @endcanview -->
                                        @canview('cust')
                                        <li id="cust">
                                            <a href="{{ url('change_cust_info') }}"> Change Customer Information</a>
                                        </li>
                                        @endcanview
                                        @canview('changeacctinfo')
                                        <li id="changeacctinfo">
                                            <a href="{{ url('change_acct_info') }}"> Change Acccount Information</a>
                                        </li>
                                        @endcanview 
                                            
                                        @canview('dealer')
                                        <li id="dealer">
                                            <a href="{{ url('change_dealer_info') }}"> Change Dealer NGBSS</a>
                                        </li>
                                        @endcanview
                                        @canview('evc')
                                        <li id="evc">
                                            <a href="{{ url('change_evc_info') }}"> Change EVC Information</a>
                                        </li>
                                        @endcanview
                                        @canview('deactivate')
                                        <li id="deactivate">
                                            <a href="{{ url('deactivate_sub') }}"> Deactivate Subcriber</a>
                                        </li>
                                        @endcanview
                                       
                                 
                                        @canview('changeposttopre')
                                        <li id="changeposttopre" >
                                            <a href="{{ url('change_post_to_pre') }}"> Postpaid To Prepaid</a>
                                        </li>
                                        @endcanview
                                        @canview('changepretopost')
                                        <li id="changepretopost">
                                            <a href="{{ url('change_pre_to_post') }}"> Prepaid To Postpaid</a>
                                        </li>
                                        @endcanview
                                        @canview('changebillmedium')
                                        <li id="changebillmedium">
                                            <a href="{{ url('change_bill_medium') }}"> Change Bill Meduim</a>
                                        </li>
                                        @endcanview
                                        @canview('add_sub_offer')
                                        <li id="add_sub_offer"> 
                                            <a href="{{ url('add_sub_offer') }}"> Add Sub Offering</a>
                                        </li>
                                        @endcanview
                                        @canview('remove_sub_offer')
                                        <li id="remove_sub_offer">
                                            <a href="{{ url('remove_sub_offer') }}">Remove Sub Offering</a>
                                        </li>
                                        @endcanview
                                        @canview('Clear_Report')
                                        <li id="Clear_Report">
                                            <a href="{{ url('Clear_Report') }}">Clear Report</a>
                                        </li>
                                        @endcanview
                                       
                                    </ul>
                                </li>
                                <li id="menu_postpaid">
                                    <a href="#"> <i class="fas fa-dollar-sign"></i>
                                        PostPaid <i class="fa arrow"></i>
                                    </a>
                                    <ul class="sidebar-nav" id="postpaid_collapse">
                                        <!-- <li id="batch_payment">
                                            <a href="{{url('batch_payment')}}">Batch Payment</a>
                                        </li>
                                        <li id="request_suspend">
                                            <a href="{{url('request_suspend')}}">Request Temporary Suspend</a>
                                        </li>
                                        <li id="cancel_suspend">
                                            <a href="{{url('cancel_suspend')}}">Cancel Suspend</a>
                                        </li> -->
                                        @canview('batch_collection')
                                        <li id="batch">
                                        <a href="{{ url('batch_collection') }}">Batch Dunning</a>
                                        </li>
                                        @endcanview
                                        <li id="testing">
                                            <a href="{{url('payment')}}">Batch Payment<span class="label label-screenful">Completed</span></a>
                                        </li>
                                        <li id="dunning_format">
                                            <a href="{{url('dunning_format')}}">Dunning Format<span class="label label-screenful">Completed</span></a>
                                        </li>
                                    </ul>
                                </li>
                                <li id='menu_report'>
                                    <a href="#"><i class="far fa-clipboard"></i>
                                    Report<i class="fa arrow"></i> 
                                    </a>
                                    <ul class="sidebar-nav" id="report_collapse">
                                        <!-- @canview('todo')
                                        <li id="todo">
                                            <a href="{{ url('to_do') }}"> TO DO</a>
                                        </li>
                                        @endcanview
                                        @canview('completed')
                                        <li id="completed">
                                            <a href="{{ url('completed') }}"> Completed</a>
                                        </li>
                                        @endcanview -->
                                         <!-- =================== More Menu ========================= -->
                                         @canview('customer info report')
                                        <li id="customer_info_report">
                                            <a href="{{ url('customer info report') }}">Customer info report</a>
                                        </li>
                                        @endcanview
                                    </ul>
                                </li>
                            


                                <li id='menu_user'>
                                    <a href="#"><i class="fas fa-lock"></i>
                                    Security<i class="fa arrow"></i> 
                                    </a>
                                    <ul class="sidebar-nav" id="user_collapse">
                                        @canview('user')
                                        <li id="user">
                                            <a href="{{ url('user') }}">User</a>
                                        </li>
                                        @endcanview
                                        @canview('userlogin')
                                        <li id="userlogin">
                                            <a href="{{ url('user/access/view') }}">User Login</a>
                                        </li>
                                        @endcanview
                                        @canview('role')
                                        <li id="role">
                                            <a href="{{ url('role') }}">Role</a>
                                        </li>
                                        @endcanview
                                        @canview('user_function')
                                        <li id="user_permission">
                                            <a href="{{ url('user/function') }}">User Permission</a>
                                        </li>
                                        @endcanview
                                        @canview('function')
                                        <li id="permission">
                                            <a href="{{ url('permission') }}">Permission</a>
                                        </li>
                                        @endcanview
                                    </ul>
                                </li>
                            </ul>
                        </nav>
                    </div>
                    <footer class="sidebar-footer">
                        <ul class="sidebar-menu metismenu" id="customize-menu">
                            <li>
                                <ul>
                                    <li class="customize">
                                        <div class="customize-item">
                                            <div class="row customize-header">
                                                <div class="col-4">
                                                </div>
                                                <div class="col-4">
                                                    <label class="title">fixed</label>
                                                </div>
                                                <div class="col-4">
                                                    <label class="title">static</label>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-4">
                                                    <label class="title">Sidebar:</label>
                                                </div>
                                                <div class="col-4">
                                                    <label>
                                                        <input class="radio" type="radio" name="sidebarPosition" value="sidebar-fixed">
                                                        <span></span>
                                                    </label>
                                                </div>
                                                <div class="col-4">
                                                    <label>
                                                        <input class="radio" type="radio" name="sidebarPosition" value="">
                                                        <span></span>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-4">
                                                    <label class="title">Header:</label>
                                                </div>
                                                <div class="col-4">
                                                    <label>
                                                        <input class="radio" type="radio" name="headerPosition" value="header-fixed">
                                                        <span></span>
                                                    </label>
                                                </div>
                                                <div class="col-4">
                                                    <label>
                                                        <input class="radio" type="radio" name="headerPosition" value="">
                                                        <span></span>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-4">
                                                    <label class="title">Footer:</label>
                                                </div>
                                                <div class="col-4">
                                                    <label>
                                                        <input class="radio" type="radio" name="footerPosition" value="footer-fixed">
                                                        <span></span>
                                                    </label>
                                                </div>
                                                <div class="col-4">
                                                    <label>
                                                        <input class="radio" type="radio" name="footerPosition" value="">
                                                        <span></span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                                <a href="">
                                <i class="fa fa-cog"></i> Customize </a>
                            </li>
                        </ul>
                    </footer>
                </aside>
                <div class="sidebar-overlay" id="sidebar-overlay"></div>
                <div class="sidebar-mobile-menu-handle" id="sidebar-mobile-menu-handle"></div>
                <div class="mobile-menu-handle"></div>
                <article class="content dashboard-page">
                    <section class="section">
                        @yield('content')
                    </section>
                   
                </article>
                <footer class="footer">
                    
                    <div class="footer-block author">
                        <ul>
                           
                        </ul>
                    </div>
                </footer>
            </div>
        </div>
               <!-- Reference block for JS -->
               <div class="ref" id="ref">
            <div class="color-primary"></div>
            <div class="chart">
                <div class="color-primary"></div>
                <div class="color-secondary"></div>
            </div>
        </div>

        <!-- js chosen -->
        <script src="{{asset('chosen/chosen.jquery.js')}}"></script>
        <script src="{{asset('chosen/chosen.jquery.min.js')}}"></script>
        <script src="{{asset('chosen/docsupport/init.js')}}"></script>

        <script src="{{url('admin/js/vendor.js')}}"></script>
        <script src="{{url('admin/js/app.js')}}"></script>
        <!-- Jquery DataTable Plugin Js -->
    <script src="{{ asset('admin/js/jquery-datatable/jquery.dataTables.js') }}"></script>
    <script src="{{ asset('admin/js/jquery-datatable/skin/bootstrap/js/dataTables.bootstrap.js') }}"></script>
    <script src="{{ asset('admin/js/jquery-datatable/extensions/export/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('admin/js/jquery-datatable/extensions/export/buttons.flash.min.js') }}"></script>
    <script src="{{ asset('admin/js/jquery-datatable/extensions/export/jszip.min.js') }}"></script>
    <script src="{{ asset('admin/js/jquery-datatable/extensions/export/pdfmake.min.js') }}"></script>
    <script src="{{ asset('admin/js/jquery-datatable/extensions/export/vfs_fonts.js') }}"></script>
    <script src="{{ asset('admin/js/jquery-datatable/extensions/export/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('admin/js/jquery-datatable/extensions/export/buttons.print.min.js') }}"></script>

        <script>
            var numberOfOptions = $('li#menu_report #report_collapse li').length
            if($('#menu_report #report_collapse li').length <= 0)
            {
                $("#menu_report").hide();
            }
            else{
                $("#menu_report").show();
            }

            var numberOfBatchOperation = $('li#menu_batch_operation #batch_operation_collapse li').length
            if($('#menu_batch_operation #batch_operation_collapse li').length <= 0)
            {
                $("#menu_batch_operation").hide();
            }
            else{
                $("#menu_batch_operation").show();
            }
    </script>
        @yield('js')
    </body>
</html>