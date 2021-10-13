@extends('layouts.master')
@section('content')
    <div class="card card-gray">
        <div class="card-header">
            <div class="header-block">
            <p class="title"> Result </p>
            <hr>
                <div class="card-block">
                    <div class="row">
                        <div class="col-sm-5">
                            <table class="table table-sm table-bordered table-hover flip-content">
                                <thead class="flip-header bg-success text-dark">
                                    <tr>
                                        <th>Corporate Name</th>
                                        <th>Master Account</th>
                                        <th>Quantity Number</th>
                                        <th>Total Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>ABA</td>
                                        <td>1.23456</td>
                                        <td>18</td>
                                        <td>2300$</td>
                                    </tr>
                                </tbody>
                            </table>
                            <div class="form-group">
                                <button class="btn btn-danger btn-oval btn-sm">Download unl</button>
                                <button class="btn btn-danger btn-oval btn-sm">Download csv</button>
                            </div>
                        </div>
                        <div class="col-sm-5">
                            <table class="table table-sm table-bordered table-hover flip-content">
                                <thead class="flip-header bg-success text-dark">
                                    <tr class="text-small">
                                        <th>Corporate Name</th>
                                        <th>Master Account</th>
                                        <th>Quantity Number</th>
                                        <th>Total Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>ABA</td>
                                        <td>1.23456</td>
                                        <td>20</td>
                                        <td>9000$</td>
                                    </tr>
                                </tbody>
                            </table>
                            <div class="form-group">
                                <button class="btn btn-danger btn-oval btn-sm">Download unl</button>
                                <button class="btn btn-danger btn-oval btn-sm">Download csv</button>
                            </div>
                        </div>
                    </div>
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
			
            $("#menu_postpaid").addClass("active open");
			$("#postpaid_collapse").addClass("collapse in");
			
        });
    </script>
@endsection