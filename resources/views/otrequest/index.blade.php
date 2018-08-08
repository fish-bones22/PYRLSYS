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
    <div class="col-md-10 offset-md-1">
        <a class="btn btn-link" role="button" href="{{ route('otrequest.new') }}">New OT Request</a>
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
                    <td>{{ $req->employeeName }}</td>
                    <td>{{ $req->department['displayName'] }}</td>
                    <td>{{ date_format($otDate, 'M d Y') }}</td>
                    <td>{{ date_format($startTime, 'h:i A') }}</td>
                    <td>{{ date_format($endTime, 'h:i A') }}</td>
                    <td>{{ $req->allowedHours }}</td>
                    <td>{{ $req->reason }}</td>
                    <td>
                        <div class="form-inline">
                        <form action="{{ route('otrequest.approve', $req->id) }}" method="POST">
                            @csrf
                            @method('post')
                            <button type="submit" class="btn btn-link">Approve</button>
                        </form>
                        <form action="{{ route('otrequest.deny', $req->id) }}" method="POST">
                            @csrf
                            @method('post')
                            <button type="submit" class="btn btn-link">Deny</button>
                        </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@stop

@section('script')
<script src="{{ asset('js/otRequestsPage.js') }}"></script>
@stop
