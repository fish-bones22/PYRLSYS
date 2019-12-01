@extends('layout.master')

@section('title')
OT Requests
@stop

@section('content')

@if (session('error') != null)
<div class="alert alert-danger">{{ session('error') }}<button type="button" class="close" data-dismiss="alert">&times;</button></div>
@endif
@if (session('success') != null)
<div class="alert alert-success">{{ session('success') }}<button type="button" class="close" data-dismiss="alert">&times;</button></div>
@endif


<div class="row">
    <div class="col-lg-10 offset-lg-1 form-paper section-title">Overtime Requests
        <span class="float-right">
            <a class="btn btn-link" role="button" href="{{ route('otrequest.new') }}">New OT Request</a>
        </span>
    </div>
    <div class="col-lg-10 offset-lg-1 form-paper section-divider"></div>
</div>
<div class="row">
    <div class="col-lg-4 col-12 order-lg-3 form-paper">
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
    <div class="col-lg-6 offset-lg-1 form-paper">
        <form action="{{ route('otrequest.post') }}" method="POST" id="setDateForm">
            @csrf
            @method('post')
            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <label class="form-paper-label">Date Range</label>
                        <div class="input-group">
                            <input type="date" class="form-control form-control-sm" name="datefrom" id="dateFrom" value="{{ isset($details['datefrom']) ? $details['datefrom'] : '' }}" />
                            <input type="date" class="form-control form-control-sm" name="dateto" id="dateTo" value="{{ isset($details['dateto']) ? $details['dateto'] : "" }}" />
                            <button type="submit" class="btn btn-secondary btn-sm"><i class="fa fa-arrow-right"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<div class="row">
    <div class="col-lg-10 offset-lg-1 form-paper section-divider"></div>
    <div class="col-lg-10 offset-lg-1 form-paper">
        <div style="overflow-x:auto">
            <form action="{{ route('otrequest.batchapprove') }}" method="POST" id="batchApprovalForm">
                @csrf
                @method('post')
            </form>
            <table class="table table-sm" id="otRequestTable">
                <thead>
                    <tr>
                        <th>&nbsp;&nbsp;<input type="checkbox" id="cb-batch-approval-all" /></th>
                        <th>Employee</th>
                        <th>Department</th>
                        <th>Date</th>
                        <th>From</th>
                        <th>To</th>
                        <th>Hours</th>
                        <th>Reason</th>
                        <th>&nbsp;</th>
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
                        <td><input form="batchApprovalForm" type="checkbox" class="cb-batch-approval" name="batchapproval[{{ $req->id }}]" />&nbsp;</td>
                        <td>{{ $req->employeeName }}</td>
                        <td>{{ $req->department['displayName'] }}</td>
                        <td>{{ date_format($otDate, 'M d Y') }}</td>
                        <td>{{ date_format($startTime, 'H:i') }}</td>
                        <td>{{ date_format($endTime, 'H:i') }}</td>
                        <td>{{ $req->allowedHours }}</td>
                        <td>{{ $req->reason }}</td>
                        <td>
                            <div class="form-inline">
                            <form action="{{ route('otrequest.approve', $req->id) }}" method="POST">
                                @csrf
                                @method('post')
                                <button type="submit" class="btn btn-sm btn-primary" data-confirm="approve">Approve</button>
                            </form>
                            <form action="{{ route('otrequest.deny', $req->id) }}" method="POST">
                                @csrf
                                @method('post')
                                <button type="submit" class="btn btn-sm btn-light" data-confirm="deny">Deny</button>
                            </form>
                            {{-- <form action="{{ route('otrequest.deny', $req->id) }}" method="POST">
                                @csrf
                                @method('post')
                                <button type="submit" class="close" data-confirm="delete">&times;</button>
                            </form> --}}
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="m-4">&nbsp;</div>
<div class="fixed-bottom btn-container m-4">
    <div class="float-right">
        <?php
        $datefrom = date_format(now(), 'Y').'-'.date_format(now(), 'm').'-'.(date_format(now(), 'd')*1 < 16 ? '01' : '16');
        $dateto = date_format(now(), 'Y').'-'.date_format(now(), 'm').'-'.(date_format(now(), 'd')*1 < 16 ? '15' : date_format(now(), 't'));
        ?>
        <div class="btn-group">
            <button form="batchApprovalForm" class="btn btn-secondary" id="btn-batch-approve" type="submit" style="display:none">Batch Approve</button>
            <a class="btn btn-primary" href="{{ action('OtRequestController@viewApproved', ['datefrom' => $datefrom, 'dateto' => $dateto]) }}">View Processed Requests</a>
        </div>
    </div>
</div>

@stop

@section('script')
<script src="{{ asset('js/otRequestsPage.js') }}"></script>
@stop
