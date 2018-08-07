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
            <div class="col-12 form-paper"><div class="display-4">CJI Payroll System</div></div>

            <div class="col-6 form-paper">
                <div class="form-group">
                    <a href='{{ action('UserController@index') }}' class="btn btn-link">Administrator</a>
                </div>
            </div>

            <div class="col-6 form-paper">
                <div class="form-group">
                    <a href='{{ action('EmployeeController@index') }}' class="btn btn-link">Human Resource</a>
                </div>
            </div>

            <div class="col-6 form-paper">
                <div class="form-group">
                    <a href='{{ action('ManhourController@index') }}' class="btn btn-link">Manhour</a>
                </div>
            </div>

            <div class="col-6 form-paper">
                <div class="form-group">
                    <a href='{{ action('ManhourController@index') }}' class="btn btn-link">Payroll</a>
                </div>
            </div>

        </div>

    </div>
</div>
@stop
