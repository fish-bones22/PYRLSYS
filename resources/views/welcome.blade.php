@extends('layout.secondary')

@section('title')
Welcome
@stop

@section('style')
<link href="{{ asset('css/tile.css') }}" media="all" rel="stylesheet" type="text/css" />
@stop

@section('content')
<div class="row">
    <div class="col-md-6 offset-md-3">
        <div class="row">

            <div class="col">
                <div class="tile tile-light bg-primary">
                    <div class="tile-content">
                        <div class="tile-body">
                            <a href='{{ action('EmployeeController@index') }}'>Human Resource</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col">
                <div class="tile tile-light bg-primary">
                    <div class="tile-content">
                        <div class="tile-body">
                            <a href='{{ action('UserController@index') }}'>App Users</a>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@stop
