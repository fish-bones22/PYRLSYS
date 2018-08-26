@extends('layout.master')

@section('title')
Adjustments
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

        <div class="row">
            <div class="col-12 form-paper">

                <form id="setDateForm" action="{{ action('AdjustmentsRecordController@goToDate', $employee->id) }}" method="get">
                    @csrf
                    @method('get')
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
            <div class="col-12 form-paper section-divider"></div>
        </div>

        <form action="{{ action('AdjustmentsRecordController@add', $employee->id) }}" method="POST">
            @csrf
            @method('post')

            <input type="hidden" name="employee_name" value="{{ $employee->fullName }}" />
            <input type="hidden" name="record_date" value="{{ $details['year'].'-'.$details['month'].'-'.$details['startday'] }}" />

            <div class="row">
                <div class="col-12 form-paper section-title">Adjustments</div>
                <div class="col-12 form-paper section-divider"></div>
            </div>

            <?php
            $actualSize = sizeof($models);
            if ($actualSize == 0)
                $actualSize = 1;
            ?>

            <input type="hidden" id="adjustment-index" value="{{ $actualSize }}" />

            @for ($i = 0; $i < $actualSize; $i++)

            <div class="row adjustment-{{$i}}">
                <div class="col-12 form-paper section-delete">
                    <button type="button" class="close" data-index="{{$i}}" onclick="deleteRow(this, 'adjustment')">&times;</button>
                </div>
                <div class="col-4 form-paper">
                    <div class="form-group">
                        <label class="form-paper-label">Adjustment Type</label>
                        <select class="form-control" name="models[{{$i}}][details]">
                            <?php
                            $options = [
                                'Allowance', 'Cash Advance'
                            ]
                            ?>
                            @foreach ($categories as $cat)
                            <option value="{{ $cat->value }}" {{ (isset($models[$i]['details']) && $models[$i]['details'] === $cat->value) ? 'selected' : '' }}>{{ $cat->value }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-4 form-paper">
                    <div class="form-group">
                        <label class="form-paper-label">Amount</label>
                        <input type="number" class="form-control" name="models[{{$i}}][amount]" value="{{ isset($models[$i]['amount']) ? $models[$i]['amount'] : '' }}" />
                    </div>
                </div>
                <div class="col-4 form-paper">
                    <div class="form-group">
                        <label class="form-paper-label">Remarks</label>
                        <input type="text" class="form-control" name="models[{{$i}}][remarks]" value="{{ isset($models[$i]['remarks']) ? $models[$i]['remarks'] : '' }}" />
                    </div>
                </div>
                <div class="col-12 form-paper section-divider"></div>
            </div>
            @endfor

            <div class="row addContainer">
                <div class="col-12 form-paper">
                    <button class="btn btn-link" type="button" onclick="createNewRow(this, 'adjustment')">Add Adjustments</button>
                </div>
            </div>

            <div class="m-4">&nbsp;</div>
            <div class="fixed-bottom btn-container m-4">
                <div class="float-right">
                    <div class="btn-group">
                        <a class="btn btn-light" href="{{ action('PayrollController@index') }}">Back to List</a>
                        <button type="reset" class="btn btn-secondary">Reset</button>
                        <input type="submit" class="btn btn-primary" data-confirm="submit" value="Save"/>
                    </div>
                </div>
            </div>

        </form>

    </div>
</div>

@stop

@section('script')
<script src="{{ asset('js/dynamicAddAndDelete.js') }}"></script>
@stop
