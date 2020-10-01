@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Personal Client</div>
                <div class="card-body">
               <passport-clients></passport-clients>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
