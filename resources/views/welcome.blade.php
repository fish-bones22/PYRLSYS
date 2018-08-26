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
            <div class="col-12 form-paper">
                <div class="row">
                    <div class="col-sm-7">
                        <img src="{{ asset('images/logo-small.jpg') }}" class="image-responsive tile-logo"/>
                    </div>
                    <div class="col-sm-5 inline-block">
                        <div class="form-group align-bottom">
                            <input class="form-control form-control-sm" type="search" id="searchBox" placeholder="Search navigations, pages, etc" onkeyup="filterNavigation()"/>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 form-paper section-divider"></div>
        </div>

        {{-- <div class="row">
            <div class="col-12 form-paper section-title">Welcome $name, select from navigation to start</div>
        </div> --}}

        <div class="row">
            <div class="col-12 form-paper">
                <div class="mt-2">
                    <div class="float-right"><a href="{{ action('UserController@logout') }}" class="btn btn-link btn-sm">Log Out</a></div>
                    <i class="text-muted">Welcome $name. Select from naviagtion to start.</i>
                </div>
            </div>
            <div class="col-12 form-paper section-divider"></div>
        </div>

        <div class="row" id="masterNavigation">

            <div class="col-sm-6 form-paper tile-selection" onclick="filterType('Administrator')">
                <div class="form-group text-sm-center">
                    <div class="row">
                        <div class="col-sm-12 col-2">
                            <img src="{{ asset('images/icons/gears.svg') }}" class="img-responsive tile-icon" />
                        </div>
                        <div class="col-sm-12 col-10">
                            <div class="form-paper-display">Administrator</div>
                            <noscript>
                                <a href='{{ action('UserController@index') }}' class="btn btn-link">Administrator</a>
                            </noscript>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 form-paper tile-selection" onclick="filterType('Human Resource')">
                <div class="form-group">
                    <div class="form-group text-sm-center">
                        <div class="row">
                            <div class="col-sm-12 col-2">
                                <img src="{{ asset('images/icons/multi-user.svg') }}" class="img-responsive tile-icon" />
                            </div>
                            <div class="col-sm-12 col-10">
                                <div class="form-paper-display">Human Resource</div>
                                <noscript>
                                    <a href='{{ action('EmployeeController@index') }}' class="btn btn-link">Human Resource</a>
                                </noscript>
                             </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 form-paper tile-selection" onclick="filterType('Manhour')">
                <div class="form-group">
                    <div class="form-group text-sm-center">
                        <div class="row">
                            <div class="col-sm-12 col-2">
                                <img src="{{ asset('images/icons/clock.svg') }}" class="img-responsive tile-icon" />
                            </div>
                            <div class="col-sm-12 col-10">
                                <div class="form-paper-display">Manhour</div>
                                <noscript>
                                    <a href='{{ action('ManhourController@index') }}' class="btn btn-link">Manhour</a>
                                </noscript>
                             </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 form-paper tile-selection" onclick="filterType('Payroll')">
                <div class="form-group">
                    <div class="form-group text-sm-center">
                        <div class="row">
                            <div class="col-sm-12 col-2">
                                <img src="{{ asset('images/icons/money.svg') }}" class="img-responsive tile-icon" />
                            </div>
                            <div class="col-sm-12 col-10">
                                <div class="form-paper-display">Payroll</div>
                                <noscript>
                                    <a href='{{ action('UserController@index') }}' class="btn btn-link">Payroll</a>
                                </noscript>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div class="row" id="navigations" style="display:none">
            <div class="col-12 form-paper">
                <table class="table table-sm table-hover" id="navigationTable">
                    <thead>
                        <tr>
                            <th>&nbsp;</th>
                            <th class=""><button class="btn btn-link float-right" type="button" onclick="clearFilter()">Back to Main</button></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td><a href="{{ action('UserController@index') }}">Show Users</a></td><td><i class="text-muted small">Administrator</i></td></tr>
                        <tr><td><a href="{{ action('UserController@register') }}">Add User</a></td><td><i class="text-muted small">Administrator</i></td></tr>
                        <tr><td><a href="{{ action('CategoryController@manage', 'department') }}">Manage Departments</a></td><td><i class="text-muted small">Administrator</i></td></tr>
                        <tr><td><a href="{{ action('EmployeeController@index') }}">Show Employees</a></td><td><i class="text-muted small">Human Resource</i></td></tr>
                        <tr><td><a href="{{ route('employee.new') }}">Add Employee</a></td><td><i class="text-muted small">Human Resource</i></td></tr>
                        <tr><td><a href="{{ action('ApplicantController@index') }}">Show Applicants</a></td><td><i class="text-muted small">Human Resource</i></td></tr>
                        <tr><td><a href="{{ action('ApplicantController@new') }}">Show Application Form</a></td><td><i class="text-muted small">Human Resource</i></td></tr>
                        <tr><td><a href="{{ action('ManhourController@index') }}">Show Manhour Master List</a></td><td><i class="text-muted small">Manhour</i></td></tr>
                        <tr><td><a href="{{ route('manhour.input') }}">Input Manhour Records</a></td><td><i class="text-muted small">Manhour</i></td></tr>
                        <tr><td><a href="{{ action('ManhourController@viewNow') }}">Show Daily Work Hours</a></td><td><i class="text-muted small">Manhour</i></td></tr>
                        <tr><td><a href="{{ action('OtRequestController@index') }}">Show OT Requests</a></td><td><i class="text-muted small">Manhour</i></td></tr>
                        <tr><td><a href="{{ action('OtRequestController@new') }}">Add OT Requests</a></td><td><i class="text-muted small">Manhour</i></td></tr>
                        <tr><td><a href="{{ action('PayrollController@index') }}">Show Payroll Master List</a></td><td><i class="text-muted small">Payroll</i></td></tr>
                        <tr><td><a href="{{ action('DeductibleRecordController@getAll', date_format(now(), 'Y-m-d')) }}">Show Benefits Record List</a></td><td><i class="text-muted small">Payroll</i></td></tr>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

@stop

@section('script')
<script>
    var table;
    $(function() {

        // $(".tile-selection").click(function() {
        //     window.location = $(this).find("a").attr("href");
        // });

        table = $("#navigationTable").DataTable({
            "lengthChange": false,
            "info": false,
            "dom": "<t<'float-right'p>>",
            "paging": false,
            "ordering": false
        });
    });

    function filterType(type) {
        $("#navigations").show();
        $("#masterNavigation").hide();
        table.column(1).search(type);
        table.column(1).draw();
    }

    function filterNavigation() {
        var term = $("#searchBox").val();

        $("#navigations").show();
        $("#masterNavigation").hide();
        table.search(term);
        table.draw();

        if (term === '') {
            $("#navigations").hide();
            $("#masterNavigation").show();
            return;
        }
    }

    function clearFilter() {
        $("#navigations").hide();
        $("#masterNavigation").show();
        $("#searchBox").val('');
        table.column(1).search('');
        table.column(1).draw();
        table.search('');
        table.draw();
    }
</script>
@stop
