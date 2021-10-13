@extends('layouts.master')

@section('content')
        <article class="content error-500-page">
            <section class="section">
                <div class="error-card">
                    <div class="error-title-block">
                        <h3 class="error-title">500</h3>
                        <h2 class="error-sub-title text-danger">
                            You Don't have permission!
                        </h2>
                    </div>
                    <div class="error-container">
                        <a class="btn btn-primary" href="{{url('dashboard')}}">
                            <i class="fa fa-angle-left"></i>
                            Back to Dashboard
                        </a>
                    </div>

                </div>
            </section>
        </article>

@endsection