<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="{{ asset('css/app.css') }}" media="all" rel="stylesheet" type="text/css" />
    <link href="{{ asset('css/dev.css') }}" media="all" rel="stylesheet" type="text/css" />
    <link href="{{ asset('css/form-paper.css') }}" media="all" rel="stylesheet" type="text/css" />
    @yield('style')
    <title>@yield('title')</title>
</head>
<body>
    <nav class="navbar navbar-dark fixed-top navbar-expand-md bg-primary">
        <a href="/" class="navbar-brand"><img src="{{ asset('images/icons/logo-inverted.svg') }}" style="width:50px"/></a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav">
                @if(AuthUtility::hasAuth('accountsmanagement'))
                <li class="nav-item dropdown">
                    <a href="#" class="nav-link dropdown-toggle" role="button" id="humanResourceDropdown" data-toggle="dropdown">Administrator</a>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="{{ action('UserController@index') }}">Users</a>
                        <a class="dropdown-item" href="{{ action('CategoryController@manage', 'department') }}">Departments</a>
                    </div>
                </li>
                @endif
                @if(AuthUtility::hasAuth('humanresourcemanagement'))
                <li class="nav-item dropdown">
                    <a href="#" class="nav-link dropdown-toggle" role="button" id="humanResourceDropdown" data-toggle="dropdown">Human Resource</a>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="{{ action('EmployeeController@index') }}">Employees</a>
                        <a class="dropdown-item" href="{{ action('ApplicantController@index') }}">Applicants</a>
                    </div>
                </li>
                @endif
                @if(AuthUtility::hasAuth('manhourmanagement'))
                <li class="nav-item dropdown">
                    <a href="#" class="nav-link dropdown-toggle" role="button" data-toggle="dropdown" data-target="#manhourDropdown">Manhour</a>
                    <div class="dropdown-menu" id="manhourDropdown">
                        <a class="dropdown-item" href="{{ action('ManhourController@index') }}">Masterlist</a>
                        <a class="dropdown-item" href="{{ action('ManhourController@viewNow') }}">Daily Work Hours</a>
                        <a class="dropdown-item" href="{{ action('ManhourController@input', '') }}">Input Records</a>
                        <a class="dropdown-item" href="{{ action('ManhourController@inputAll', date_format(now(),'Y-m-d')) }}">Batch Input Records</a>
                        <a class="dropdown-item" href="{{ action('OtRequestController@index') }}" role="button">OT Requests</a>
                    </div>
                </li>
                @endif
                @if(AuthUtility::hasAuth('payrollmanagement'))
                <li class="nav-item dropdown">
                    <a href="#" class="nav-link dropdown-toggle" role="button" data-toggle="dropdown" data-target="#payrollDropdown">Payroll</a>
                    <div class="dropdown-menu" id="payrollDropdown">
                        <a class="dropdown-item" href="{{ action('PayrollController@index') }}">Masterlist</a>
                        <a class="dropdown-item" href="{{ action('PayrollController@summary', date_format(now(), 'Y-m-d')) }}">Payroll Summary</a>
                        <a class="dropdown-item" href="{{ action('DeductibleRecordController@getAll', date_format(now(), 'Y-m-d')) }}">Benefits Records</a>
                    </div>
                </li>
                @endif
            </ul>
            <div class="ml-auto">
                <a href="{{ action('UserController@logout') }}" class="btn btn-outline-light float-right">Log out</a>
            </div>
        </div>
    </nav>
    <div class="m-4">&nbsp;</div>
    <div class="m-4">&nbsp;</div>
    <div class="container">
        @yield('content')
    </div>

    <div id="confirmationModal" class="modal fade" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="row">
                    <div class="col-12 form-paper section-title">
                        <button class="close" type="button" data-dismiss="modal">&times;</button>
                        <span class="confirmationTitle">Confirmation</span>
                    </div>
                    <div class="col-12 form-paper section-divider"></div>
                    <div class="col-12 form-paper">
                        <div class="form-group">
                            <div class="form-paper-display">Do you want to <span class="confirmationAction">{action}</span>?</div>
                        </div>
                    </div>
                    <div class="col-12 form-paper">
                        <div class="form-group">
                            <div class="btn-group float-right">
                                <button class="btn btn-primary btn-confirm-yes" type="button">Yes</button>
                                <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                            </div>
                        </div>
                        <div class="m-2">&nbsp;</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/app.js') }}" ></script>
    <script type="text/javascript" src="{{ asset('js/vfs_fonts.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/confirmation.js') }}"></script>
    @yield('script')
</body>
</html>
