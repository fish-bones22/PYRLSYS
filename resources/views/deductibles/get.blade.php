@extends('layout.master')

@section('content')
<div class="row">
    <div class="col-md-10 offset-md-1">

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

        <form action="{{ action('DeductibleRecordController@add', $employee->id) }}" method="POST">
            @csrf
            @method('post')

            <input type="hidden" name="employee_name" value="{{ $employee->fullName }}" />
            <input type="hidden" name="record_date" value="{{ $details['year'].'-'.$details['month'].'-'.$details['startday'] }}" />

            @if (isset($employee->deductibles['sss']))
            <div class="row">
                <div class="col-12 form-paper section-title">SSS</div>
                <div class="col-12 form-paper section-divider"></div>
                <div class="col-3 form-paper">
                    <div class="form-group">
                        <label class="form-paper-label">SS Number</label>
                        <div class="form-paper-display">{{ $employee->deductibles['sss'] }}</div>
                        <input type="hidden" name="models[sss][identifier]" value="{{ isset($employee->deductibles['sss']) ? $employee->deductibles['sss'] : old('models["sss"]["identifier"]') }}" />
                        <input type="hidden" name="models[sss][identifier_details]" value="SS Number" />
                        <input type="hidden" name="models[sss][details]" value="sss" />
                        <input type="hidden" name="models[sss][id]" value="{{ isset($models['sss']['id']) ? $models['sss']['id'] : old('models["sss"]["id"]') }}" />
                    </div>
                </div>
                <div class="col-3 form-paper">
                    <div class="form-group">
                        <label class="form-paper-label">Employee's Share</label>
                        <input type="number" class="form-control" name="models[sss][amount]" value="{{ isset($models['sss']['amount']) ? $models['sss']['amount'] : old('models["sss"]["amount"]') }}" />
                    </div>
                </div>
                <div class="col-3 form-paper">
                    <div class="form-group">
                        <label class="form-paper-label">Employer's Share</label>
                        <input type="number" class="form-control" name="models[sss][subamount]" value="{{ isset($models['sss']['subamount']) ? $models['sss']['subamount'] : old('models["sss"]["subamount"]') }}" />
                    </div>
                </div>
                <div class="col-3 form-paper">
                    <div class="form-group">
                        <label class="form-paper-label">SSS Loan</label>
                        <input type="number" class="form-control" name="models[sss][loan]" value="{{ isset($models['sss']['loan']) ? $models['sss']['loan'] : old('models["sss"]["loan"]') }}" />
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
                        <input type="hidden" name="models[philhealth][details]" value="philhealth" />
                        <input type="hidden" name="models[philhealth][id]" value="{{ isset($models['philhealth']['id']) ? $models['philhealth']['id'] : old('models["philhealth"]["id"]') }}" />
                    </div>
                </div>
                <div class="col-4 form-paper">
                    <div class="form-group">
                        <label class="form-paper-label">Employee's Share</label>
                        <input type="number" class="form-control" name="models[philhealth][amount]" value="{{ isset($models['philhealth']['amount']) ? $models['philhealth']['amount'] : old('models["philhealth"]["amount"]') }}" />
                    </div>
                </div>
                <div class="col-4 form-paper">
                    <div class="form-group">
                        <label class="form-paper-label">Employer's Share</label>
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
                        <input type="hidden" name="models[pagibig][details]" value="pagibig" />
                        <input type="hidden" name="models[pagibig][id]" value="{{ isset($models['pagibig']['id']) ? $models['pagibig']['id'] : old('models["pagibig"]["id"]') }}" />
                    </div>
                </div>
                <div class="col-3 form-paper">
                    <div class="form-group">
                        <label class="form-paper-label">Employee's Share</label>
                        <input type="number" class="form-control" name="models[pagibig][amount]" value="{{ isset($models['pagibig']['amount']) ? $models['pagibig']['amount'] : old('models["pagibig"]["amount"]') }}" />
                    </div>
                </div>
                <div class="col-3 form-paper">
                    <div class="form-group">
                        <label class="form-paper-label">Employer's Share</label>
                        <input type="number" class="form-control" name="models[pagibig][subamount]" value="{{ isset($models['pagibig']['subamount']) ? $models['pagibig']['subamount'] : old('models["pagibig"]["subamount"]') }}" />
                    </div>
                </div>
                <div class="col-3 form-paper">
                    <div class="form-group">
                        <label class="form-paper-label">PAGIBIG Loan</label>
                        <input type="number" class="form-control" name="models[pagibig][loan]" value="{{ isset($models['pagibig']['loan']) ? $models['pagibig']['loan'] : old('models["pagibig"]["loan"]') }}" />
                    </div>
                </div>
                <div class="col-12 form-paper section-divider"></div>
            </div>
            @endif

            <div class="row">
                <div class="col-12 form-paper section-title">OTHER DEDUCTIONS</div>
                <div class="col-12 form-paper section-divider"></div>
            </div>

            @for ($i = 0; $i < sizeof($otherModels); $i)

            <div class="row">
                <div class="col-3 form-paper">
                    <div class="form-group">
                        <label class="form-paper-label">Deduction Type</label>
                        <select class="form-control" name="other_model[{{$i}}]['details']">
                            <?php
                            $options = [
                                'Company Loan', 'Meal Deduction', 'Medical Deduction'
                            ]
                            ?>
                            @foreach ($options as $option)
                            <option value="{{ $option }}" {{ $otherModels[$i]['details'] === $option ? 'selected' : '' }}>{{ $option }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-3 form-paper">
                    <div class="form-group">
                        <label class="form-paper-label">Details</label>
                        <div class="form-paper-subdisplay">Loan Details</div>
                    </div>
                </div>
                <div class="col-3 form-paper">
                    <div class="form-group">
                        <label class="form-paper-label">Amount</label>
                        <input type="number" class="form-control" name="otherModels[{{$i}}][amount]" value="{{ isset($otherModels[$i]['amount']) ? $otherModels[$i]['amount'] : old("otherModels[".$i."][amount]") }}" />
                    </div>
                </div>
                <div class="col-3 form-paper">
                    <div class="form-group">
                        <label class="form-paper-label">Remarks</label>
                        <input type="text" class="form-control" name="otherModels[{{$i}}]['remarks']" value="{{ isset($otherModels[$i]['remarks']) ? $otherModels[$i][remarks] : old("otherModels[".$i."][remarks]") }}" />
                    </div>
                </div>
                <div class="col-12 form-paper section-divider"></div>
            </div>
            @endfor

            <div class="row">
                <div class="col-12 form-paper">
                    <button class="btn btn-link" type="button">Add Deductible</button>
                </div>
            </div>

            <div class="m-4">&nbsp;</div>
            <div class="fixed-bottom btn-container m-4">
                <div class="float-right">
                    <div class="btn-group">
                        <a class="btn btn-light" href="{{ action('EmployeeController@index') }}">Back to List</a>
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

@stop
