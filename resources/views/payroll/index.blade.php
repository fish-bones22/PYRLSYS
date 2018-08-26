@extends('layout.master')

@section('content')

<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="row">
            <div class="col-12 form-paper section-title">Employee Payroll</div>
            <div class="col-12 form-paper section-divider"></div>
        </div>
        <div class="row">
            <div class="col-12 form-paper">
                <div class="form-group float-right">
                    <label for="searchBox" class="form-paper-label">Search</label>
                    <input id="searchBox" type="search" class="form-control form-control-sm" />
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 form-paper section-divider"></div>
            <div class="col-12 form-paper">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Department</th>
                            <th>&nbsp;</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($employees as $employee)
                        <tr>
                            <td>{{ $employee->fullName }}</td>
                            <td>{{ $employee->current['department']['displayName'] }}</td>
                            <td>
                                <span class="btn-group">
                                    <a role="button" href="{{ action('PayrollController@viewNow', $employee->id) }}" class="btn btn-secondary btn-sm">View Pay</a>
                                    <a role="button" href="{{ route('deductible.get',  ['id' => $employee->id, 'date' => date_format(now(), 'Y-m-d')]) }}" class="btn btn-secondary btn-sm">Set Deductibles</a>
                                    <a role="button" href="{{ route('adjustments.get',  ['id' => $employee->id, 'date' => date_format(now(), 'Y-m-d')]) }}" class="btn btn-secondary btn-sm">Set Adjustments</a>
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@stop

@section('script')

@stop
