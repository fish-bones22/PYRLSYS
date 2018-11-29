@extends('layout.master')

@section('title')
{{ $employee->fullName }} - Deductibles
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
            <div class="col-12 form-paper section-title">{{ $employee->fullName }} - Deductible</div>
            <div class="col-12 form-paper section-divider"></div>
        </div>
        <div class="row">
            <div class="col-12 form-paper">

                <form id="setDateForm" action="{{ action('DeductibleRecordController@goToDate', $employee->id) }}" method="get">
                    @csrf
                    @method('get')
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
            <div class="col-12 form-paper section-divider"></div>

            <div class="col-12 form-paper"><span class="small">*Fields with </span><span class="auto-tag text-success">Auto</span><span class="small"> need to be saved first before being persisted to record.</span></div>
            <div class="col-12 form-paper section-divider"></div>
        </div>

        <form action="{{ action('DeductibleRecordController@add', $employee->id) }}" method="POST">
            @csrf
            @method('post')

            <input type="hidden" name="employee_name" value="{{ $employee->fullName }}" />
            <input type="hidden" name="record_date" value="{{ $details['year'].'-'.$details['month'].'-'.$details['startday'] }}" />
            <div class="row">
                <div class="col-12 form-paper section-title">Details</div>
                <div class="col-12 form-paper section-divider"></div>
                <div class="col-4 form-paper">
                    <div class="form-group">
                        <label class="form-paper-label">Rate ({{ $details['basis'] }})</label>
                        <div class="form-paper-display">{{ $details['rate'] }}</div>
                    </div>
                </div>
                <div class="col-4 form-paper">
                    <div class="form-group">
                        <label class="form-paper-label">Basic Salary</label>
                        <div class="form-paper-display">{{ $details['basic'] }}</div>
                    </div>
                </div>
                <div class="col-4 form-paper">
                    <div class="form-group">
                        <label class="form-paper-label">Gross Salary</label>
                        <div class="form-paper-display">{{ $details['gross'] }}</div>
                    </div>
                </div>
                <div class="col-12 form-paper section-divider"></div>
            </div>
            @if (isset($employee->deductibles['tin']))
            <div class="row">
                <div class="col-12 form-paper section-title">Withholding Tax</div>
                <div class="col-12 form-paper section-divider"></div>
                <div class="col-4 form-paper">
                    <div class="form-group">
                        <label class="form-paper-label">TIN</label>
                        <div class="form-paper-display">{{ $employee->deductibles['tin'] }}</div>
                        <input type="hidden" name="models[tin][identifier]" value="{{ isset($employee->deductibles['tin']) ? $employee->deductibles['tin'] : old('models["tin"]["identifier"]') }}" />
                        <input type="hidden" name="models[tin][identifier_details]" value="TIN" />
                        <input type="hidden" name="models[tin][key]" value="tin" />
                        <input type="hidden" name="models[tin][details]" value="Withholding Tax" />
                        <input type="hidden" name="models[tin][id]" value="{{ isset($models['tin']['id']) ? $models['tin']['id'] : old('models["tin"]["id"]') }}" />
                    </div>
                </div>
                <div class="col-4 form-paper">
                    <div class="form-group">
                        <label class="form-paper-label">{!! isset($models['tin']['auto']) ? '<span class="auto-tag text-success">AUTO</span> ' : '' !!}Employee's Share</label>
                        <input type="number" class="form-control" name="models[tin][amount]" value="{{ isset($models['tin']['amount']) ? $models['tin']['amount'] : old('models["tin"]["amount"]') }}" />
                    </div>
                </div>
                <div class="col-4 form-paper">
                    <div class="form-group">
                        <label class="form-paper-label">Due Date</label>
                        <input type="date" class="form-control" name="models[tin][duedate]" value="{{ isset($models['tin']['duedate']) ? $models['tin']['duedate'] : old('models["tin"]["duedate"]') }}" />
                    </div>
                </div>
                <div class="col-12 form-paper section-divider"></div>
            </div>
            @endif

            @if (isset($employee->deductibles['sss']))
            <div class="row">
                <div class="col-12 form-paper section-title">SSS</div>
                <div class="col-12 form-paper section-divider"></div>
                <div class="col-4 form-paper">
                    <div class="form-group">
                        <label class="form-paper-label">SS Number</label>
                        <div class="form-paper-display">{{ $employee->deductibles['sss'] }}</div>
                        <input type="hidden" name="models[sss][identifier]" value="{{ isset($employee->deductibles['sss']) ? $employee->deductibles['sss'] : old('models["sss"]["identifier"]') }}" />
                        <input type="hidden" name="models[sss][identifier_details]" value="SS Number" />
                        <input type="hidden" name="models[sss][key]" value="sss" />
                        <input type="hidden" name="models[sss][details]" value="SSS" />
                        <input type="hidden" name="models[sss][id]" value="{{ isset($models['sss']['id']) ? $models['sss']['id'] : old('models["sss"]["id"]') }}" />
                    </div>
                </div>
                <div class="col-2 form-paper">
                    <div class="form-group">
                        <label class="form-paper-label">{!! isset($models['sss']['auto']) ? '<span class="auto-tag text-success">AUTO</span> ' : '' !!}Employee's Share</label>
                        <input type="number" class="form-control" name="models[sss][amount]" value="{{ isset($models['sss']['amount']) ? $models['sss']['amount'] : old('models["sss"]["amount"]') }}" />
                    </div>
                </div>
                <div class="col-2 form-paper">
                    <div class="form-group">
                        <label class="form-paper-label">{!! isset($models['sss']['auto2']) ? '<span class="auto-tag text-success">AUTO</span> ' : '' !!}Employer's Share</label>
                        <input type="number" class="form-control" name="models[sss][subamount]" value="{{ isset($models['sss']['subamount']) ? $models['sss']['subamount'] : old('models["sss"]["subamount"]') }}" />
                    </div>
                </div>
                <div class="col-2 form-paper">
                    <div class="form-group">
                        <label class="form-paper-label">{!! isset($models['sss']['auto2']) ? '<span class="auto-tag text-success">AUTO</span> ' : '' !!}Emergency Contrib.</label>
                        <input type="number" class="form-control" name="models[sss][subamount2]" value="{{ isset($models['sss']['subamount2']) ? $models['sss']['subamount2'] : old('models["sss"]["subamount2"]') }}" />
                    </div>
                </div>
                <div class="col-2 form-paper">
                    <div class="form-group">
                        <label class="form-paper-label">SSS Loan</label>
                        <input type="number" class="form-control" name="models[sss][loan]" value="{{ isset($models['sssloan']['amount']) ? $models['sssloan']['amount'] : old('models["sssloan"]["amount"]') }}" />
                        <input type="hidden" name="models[sssloan][id]" value="{{ isset($models['sssloan']['id']) ? $models['sssloan']['id'] : old('models["sssloan"]["id"]') }}" />
                        <input type="hidden" name="models[sssloan][details]" value="SSS Loan" />
                    </div>
                </div>
                <div class="col-12 form-paper section-divider"></div>
            </div>
            @endif


            @if (isset($employee->deductibles['philhealth']))
            <div class="row">
                <div class="col-12 form-paper section-title">PhilHealth</div>
                <div class="col-12 form-paper section-divider"></div>
                <div class="col-4 form-paper">
                    <div class="form-group">
                        <label class="form-paper-label">PhilHealth ID Number</label>
                        <div class="form-paper-display">{{ $employee->deductibles['philhealth'] }}</div>
                        <input type="hidden" name="models[philhealth][identifier]" value="{{ isset($employee->deductibles['philhealth']) ? $employee->deductibles['philhealth'] : old('models["philhealth"]["identifier"]') }}" />
                        <input type="hidden" name="models[philhealth][identifier_details]" value="PhilHealth Number" />
                        <input type="hidden" name="models[philhealth][key]" value="philhealth" />
                        <input type="hidden" name="models[philhealth][details]" value="PhilHealth" />
                        <input type="hidden" name="models[philhealth][id]" value="{{ isset($models['philhealth']['id']) ? $models['philhealth']['id'] : old('models["philhealth"]["id"]') }}" />
                    </div>
                </div>
                <div class="col-4 form-paper">
                    <div class="form-group">
                        <label class="form-paper-label">{!! isset($models['philhealth']['auto']) ? '<span class="auto-tag text-success">AUTO</span> ' : '' !!}Employee's Share</label>
                        <input type="number" class="form-control" name="models[philhealth][amount]" value="{{ isset($models['philhealth']['amount']) ? $models['philhealth']['amount'] : old('models["philhealth"]["amount"]') }}" />
                    </div>
                </div>
                <div class="col-4 form-paper">
                    <div class="form-group">
                        <label class="form-paper-label">{!! isset($models['philhealth']['auto2']) ? '<span class="auto-tag text-success">AUTO</span> ' : '' !!}Employer's Share</label>
                        <input type="number" class="form-control" name="models[philhealth][subamount]" value="{{ isset($models['philhealth']['subamount']) ? $models['philhealth']['subamount'] : old('models["philhealth"]["subamount"]') }}" />
                    </div>
                </div>
                <div class="col-12 form-paper section-divider"></div>
            </div>
            @endif


            @if (isset($employee->deductibles['pagibig']))
            <div class="row">
                <div class="col-12 form-paper section-title">PAGIBIG</div>
                <div class="col-12 form-paper section-divider"></div>
                <div class="col-3 form-paper">
                    <div class="form-group">
                        <label class="form-paper-label">PAGIBIG Number</label>
                        <div class="form-paper-display">{{ $employee->deductibles['pagibig'] }}</div>
                        <input type="hidden" name="models[pagibig][identifier]" value="{{ isset($employee->deductibles['pagibig']) ? $employee->deductibles['pagibig'] : old('models["pagibig"]["identifier"]') }}" />
                        <input type="hidden" name="models[pagibig][identifier_details]" value="PAGIBIG Number" />
                        <input type="hidden" name="models[pagibig][key]" value="pagibig" />
                        <input type="hidden" name="models[pagibig][details]" value="PAGIBIG" />
                        <input type="hidden" name="models[pagibig][id]" value="{{ isset($models['pagibig']['id']) ? $models['pagibig']['id'] : old('models["pagibig"]["id"]') }}" />
                    </div>
                </div>
                <div class="col-3 form-paper">
                    <div class="form-group">
                        <label class="form-paper-label">{!! isset($models['pagibig']['auto2']) ? '<span class="auto-tag text-success">AUTO</span> ' : '' !!}Employee's Share</label>
                        <input type="number" class="form-control" name="models[pagibig][amount]" value="{{ isset($models['pagibig']['amount']) ? $models['pagibig']['amount'] : old('models["pagibig"]["amount"]') }}" />
                    </div>
                </div>
                <div class="col-3 form-paper">
                    <div class="form-group">
                        <label class="form-paper-label">{!! isset($models['pagibig']['auto2']) ? '<span class="auto-tag text-success">AUTO</span> ' : '' !!}Employer's Share</label>
                        <input type="number" class="form-control" name="models[pagibig][subamount]" value="{{ isset($models['pagibig']['subamount']) ? $models['pagibig']['subamount'] : old('models["pagibig"]["subamount"]') }}" />
                    </div>
                </div>
                <div class="col-3 form-paper">
                    <div class="form-group">
                        <label class="form-paper-label">PAGIBIG Loan</label>
                        <input type="number" class="form-control" name="models[pagibig][loan]" value="{{ isset($models['pagibigloan']['amount']) ? $models['pagibigloan']['amount'] : old('models["pagibigloan"]["amount"]') }}" />
                        <input type="hidden" name="models[pagibigloan][id]" value="{{ isset($models['pagibigloan']['id']) ? $models['pagibigloan']['id'] : old('models["pagibigloan"]["id"]') }}" />
                        <input type="hidden" name="models[pagibigloan][details]" value="PAGIBIG Loan" />
                    </div>
                </div>
                <div class="col-12 form-paper section-divider"></div>
            </div>
            @endif

            <div class="row">
                <div class="col-12 form-paper section-title">OTHER DEDUCTIONS</div>
                <div class="col-12 form-paper section-divider"></div>
            </div>

            <?php
            $actualSize = sizeof($otherModels);
            if ($actualSize == 0)
                $actualSize = 1;
            ?>

            <input type="hidden" id="loan-index" value="{{ $actualSize }}" />

            @for ($i = 0; $i < $actualSize; $i++)

            <div class="row loan-{{$i}}">
                <div class="col-12 form-paper section-delete">
                    <button type="button" class="close" data-index="{{$i}}" onclick="deleteRow(this, 'loan')">&times;</button>
                    {{-- <input type="hidden" name="other_models[{{$i}}][id]" value="{{ isset($otherModels[$i]['id']) ? $otherModels[$i]['id'] : '' }}" /> --}}
                </div>
                <div class="col-4 form-paper">
                    <div class="form-group">
                        <label class="form-paper-label">Deduction Type</label>
                        <select class="form-control" name="other_models[{{$i}}][details]">
                            <?php
                            $options = [
                                'Company Loan/Cash Advance', 'Meal Deduction', 'Medical Deduction'
                            ]
                            ?>
                            @foreach ($categories as $cat)
                            <option value="{{ $cat->value }}" {{ (isset($otherModels[$i]['details']) && $otherModels[$i]['details'] === $cat->value) ? 'selected' : '' }}>{{ $cat->value }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-4 form-paper">
                    <div class="form-group">
                        <label class="form-paper-label">Amount</label>
                        <input type="number" class="form-control" name="other_models[{{$i}}][amount]" value="{{ isset($otherModels[$i]['amount']) ? $otherModels[$i]['amount'] : '' }}" />
                    </div>
                </div>
                <div class="col-4 form-paper">
                    <div class="form-group">
                        <label class="form-paper-label">Remarks</label>
                        <input type="text" class="form-control" name="other_models[{{$i}}][remarks]" value="{{ isset($otherModels[$i]['remarks']) ? $otherModels[$i]['remarks'] : '' }}" />
                    </div>
                </div>
                <div class="col-12 form-paper section-divider"></div>
            </div>
            @endfor

            <div class="row addContainer">
                <div class="col-12 form-paper">
                    <button class="btn btn-link" type="button" onclick="createNewRow(this, 'loan')">Add Deductible</button>
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
