@extends('layout.master')

@section('title')
Approved OT Requests
@stop

@section('content')

@if (session('error') != null)
<div class="alert alert-danger">{{ session('error') }}<button type="button" class="close" data-dismiss="alert">&times;</button></div>
@endif
@if (session('success') != null)
<div class="alert alert-success">{{ session('success') }}<button type="button" class="close" data-dismiss="alert">&times;</button></div>
@endif


<div class="row">
    <div class="col-md-10 offset-md-1 form-paper section-title">Approved Overtime Requests</div>
    <div class="col-md-10 offset-md-1 form-paper section-divider"></div>
</div>
<div class="row">
    <div class="col-md-10 offset-md-1">
        <div class="row">
            <div class="col-4 form-paper">
                <a class="mt-4 btn btn-sm btn-light btn-block" role="button" href="{{ route('otrequest.new') }}">New OT Request</a>
            </div>
            <div class="col-4 form-paper">
                <form action="{{ route('otrequest.gotodate') }}" method="POST" id="setDateForm">
                    @csrf
                    @method('post')
                    <div class="row">
                        <div class="col-5">
                            <div class="form-group">
                                <label class="form-paper-label">Period</label><br />
                                <div class="form-check-inline">
                                    <input id="secondPeriod" type="radio" name="period" value="second" {{ isset($details['startday']) && $details['startday'] <= 15 ? 'checked' : '' }} />
                                    <label for="secondPeriod" class="form-check-label small">Second (1-15)</label>
                                </div>
                                <div class="form-check-inline">
                                    <input id="firstPeriod" type="radio" name="period" value="first" {{ isset($details['startday']) && $details['startday'] >= 16 ? 'checked' : '' }} />
                                    <label for="firstPeriod" class="form-check-label small">First (16-EoM)</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-7">
                            <div class="form-group">
                                <label class="form-paper-label">Month and Year</label>
                                <div class="input-group">
                                    @include('layout.monthselect', ['form' => 'setDateForm', 'monthSelected' => ( isset($details['month']) ? $details['month'] : date_format(now(), 'm') ), 'name' => 'month' ])
                                    <input type="number" min="1991" max="2100" id="yearSelect" class="form-control form-control-sm" name="year" value="{{ isset($details['year']) ? $details['year'] : date_format(now(), 'Y') }}" />
                                    <button type="submit" class="btn btn-secondary btn-sm"><i class="fa fa-arrow-right"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-4 form-paper">
                <div class="row">
                    <div class="col-6">
                        <div class="form-group">
                            <label for="department" class="form-paper-label">Department</label>
                            <select class="form-control form-control-sm" id="department" onchange="filterDepartment()">
                                <option value="0">All</option>
                                @foreach ($departments as $dept)
                                    <option value="{{ $dept->id }}">{{ $dept->value }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group float-right">
                            <label for="searchBox" class="form-paper-label">Search</label>
                            <input type="search" class="form-control form-control-sm" id="searchBox" onkeyup="searchEmployee()" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-10 offset-md-1 form-paper section-divider"></div>
    <div class="col-md-10 offset-md-1 form-paper">
        <table class="table table-sm" id="otRequestTable">
            <thead>
                <tr>
                    <th>Employee</th>
                    <th>Department</th>
                    <th>Date</th>
                    <th>From</th>
                    <th>To</th>
                    <th>Hours</th>
                    <th>Reason</th>
                </tr>
            </thead>
            <tbody>

                {{-- @if (sizeof($otRequests) <= 0)
                <tr><td colspan="8"><i class="text-muted">No OT Requests</i></td></tbody>
                @endif --}}
                @foreach ($otRequests as $req)
                <tr>
                    <?php
                    $otDate = date_create($req->otDate);
                    $startTime = date_create($req->startTime);
                    $endTime = date_create($req->endTime);
                    ?>
                    <td>{{ $req->employeeName }}</td>
                    <td>{{ $req->department['displayName'] }}</td>
                    <td>{{ date_format($otDate, 'M d Y') }}</td>
                    <td>{{ date_format($startTime, 'h:i A') }}</td>
                    <td>{{ date_format($endTime, 'h:i A') }}</td>
                    <td>{{ $req->allowedHours }}</td>
                    <td>{{ $req->reason }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="m-4">&nbsp;</div>
<div class="fixed-bottom btn-container m-4">
    <div class="float-right">
        <div class="btn-group">
            <a class="btn btn-primary" href="{{ action('OtRequestController@index') }}">View Pending Requests</a>
        </div>
    </div>
</div>

@stop

@section('script')
<script src="{{ asset('js/otRequestsPage.js') }}"></script>
@stop
