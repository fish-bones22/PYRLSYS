@extends('layout.master')

@section('title')
Search results
@stop

@section('content')

<div class="row">
    <div class="col-md-8 offset-md-2 form-paper section-title">Search results
        <span class="float-right">
            <a href="{{ action('ManhourController@input', '') }}" class="btn btn-link btn-clipping">Back</a>
        </span>
    </div>
    <div class="col-md-8 offset-md-2 form-paper section-divider"></div>
    <div class="col-md-8 offset-md-2 form-paper">
        <div style="overflow-x:auto" class="mb-4">
            <table class="table table-sm" id="masterListTable">
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>Name</th>
                        <th>Department</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $index = 0;
                    ?>
                    @foreach($employees as $emp)
                    <tr>
                        <td>{!! $emp->employeeId != null ? $emp->employeeId : '<i class="small text-muted">No ID</i>' !!}</td>
                        <td><a href="{{ action('ManhourController@input', ['id' => $emp->id]) }}">{{ $emp->fullName }}</a></td>
                        <td>{{ isset($emp->current['department']['displayName']) ? $emp->current['department']['displayName'] : ''}}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@stop
@section('script')
<script src="{{ asset('js/manhourMasterlistPage.js') }}"></script>
@stop
