@extends('layout.master')

@section('content')
<div class="row">
    <div class="col-md-10 offset-md-1">
        <div class="row">
            <div class="col-12 form-paper section-title">{{ $employee->fullName }} - Payroll</div>
            <div class="col-12 form-paper section-divider"></div>
        </div>
        <div class="row">
            <div class="col-8 form-paper">
                <form action="{{ action('ManhourController@setRecordDate', $employee->id) }}" method="POST" id="setDateForm">
                    @csrf
                    @method('post')
                    <div class="row">
                        <div class="col-5">
                            <div class="form-group">
                                <label class="form-paper-label">Period</label><br />
                                <div class="form-check-inline">
                                    <input id="secondPeriod" type="radio" name="period" value="second" {{ isset($details['startday']) && $details['startday'] <= 16 ? 'checked' : '' }} />
                                    <label for="secondPeriod" class="form-check-label small">Second (1-16)</label>
                                </div>
                                <div class="form-check-inline">
                                    <input id="firstPeriod" type="radio" name="period" value="first" {{ isset($details['startday']) && $details['startday'] >= 17 ? 'checked' : '' }} />
                                    <label for="firstPeriod" class="form-check-label small">First (17-EoM)</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-7">
                            <div class="form-group">
                                <label class="form-paper-label">Month and Year</label>
                                <div class="input-group">
                                    @include('layout.monthselect', ['form' => 'setDateForm', 'monthSelected' => ( isset($details['month']) ? $details['month'] : date_format(now(), 'm') ) ])
                                    <input type="number" min="1991" max="2100" id="yearSelect" class="form-control form-control-sm" name="year" value="{{ isset($details['year']) ? $details['year'] : date_format(now(), 'Y') }}" />
                                    <button type="submit" class="btn btn-secondary btn-sm"><i class="fa fa-arrow-right"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-4 form-paper">
                <div class="form-group">
                    <label for="searchBox" class="form-paper-label">Search</label>
                    <input id="searchBox" type="search" class="form-control form-control-sm" />
                </div>
            </div>
           <span>Gross Pay: {{ $payroll->grossPay }}</span>
        </div>

    </div>
</div>
@stop

@section('script')

@stop
