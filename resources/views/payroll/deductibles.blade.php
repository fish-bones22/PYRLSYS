@extends('layout.master')

@section('content')
<div class="row">
    <div class="col-md-10 offset-md-1">

        <div class="row">
            <div class="col-12 form-paper">
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
            </div>
            <div class="col-12 form-paper section-divider"></div>
        </div>

        <div class="row">
            <div class="col-12 form-paper section-title">SSS</div>
            <div class="col-12 form-paper section-divider"></div>
            <div class="col-3 form-paper">
                <div class="form-group">
                    <label class="form-paper-label">SS Number</label>
                    <div class="form-paper-display">92-123139-23</div>
                    <input type="hidden" name="models[0]['identifier']" value="{{ isset($models[0]['identifier']) ? $models[0]['identifier'] : old('models[0]["identifier"]') }}" />
                    <input type="hidden" name="models[0]['details']" value="{{ isset($models[0]['details']) ? $models[0]['details'] : old('models[0]["details"]') }}" />
                    <input type="hidden" name="models[0]['id']" value="{{ isset($models[0]['id']) ? $models[0]['id'] : old('models[0]["id"]') }}" />
                </div>
            </div>
            <div class="col-3 form-paper">
                <div class="form-group">
                    <label class="form-paper-label">Employee's Share</label>
                    <input type="number" class="form-control" name="models[0]['value']" value="{{ isset($models[0]['value']) ? $models[0]['value'] : old('models[0]["value"]') }}" />
                </div>
            </div>
            <div class="col-3 form-paper">
                <div class="form-group">
                    <label class="form-paper-label">Employer's Share</label>
                    <input type="number" class="form-control" name="models[0]['subvalue']" value="{{ isset($models[0]['subvalue']) ? $models[0]['subvalue'] : old('models[0]["subvalue"]') }}" />
                </div>
            </div>
            <div class="col-3 form-paper">
                <div class="form-group">
                    <label class="form-paper-label">SSS Loan</label>
                    <input type="number" class="form-control" name="models[0]['loan']" value="{{ isset($models[0]['loan']) ? $models[0]['loan'] : old('models[0]["loan"]') }}" />
                </div>
            </div>
            <div class="col-12 form-paper section-divider"></div>
        </div>


        <div class="row">
            <div class="col-12 form-paper section-title">PhilHealth</div>
            <div class="col-12 form-paper section-divider"></div>
            <div class="col-4 form-paper">
                <div class="form-group">
                    <label class="form-paper-label">PhilHealth ID Number</label>
                    <input type="hidden" name="models[1]['identifier']" value="{{ isset($models[1]['identifier']) ? $models[1]['identifier'] : old('models[1]["identifier"]') }}" />
                    <input type="hidden" name="models[1]['details']" value="{{ isset($models[1]['details']) ? $models[1]['details'] : old('models[1]["details"]') }}" />
                    <input type="hidden" name="models[1]['id']" value="{{ isset($models[1]['id']) ? $models[1]['id'] : old('models[1]["id"]') }}" />
                </div>
            </div>
            <div class="col-4 form-paper">
                <div class="form-group">
                    <label class="form-paper-label">Employee's Share</label>
                    <input type="number" class="form-control" name="models[1]['value']" value="{{ isset($models[1]['value']) ? $models[1]['value'] : old('models[1]["value"]') }}" />
                </div>
            </div>
            <div class="col-4 form-paper">
                <div class="form-group">
                    <label class="form-paper-label">Employer's Share</label>
                    <input type="number" class="form-control" name="models[1]['subvalue']" value="{{ isset($models[1]['subvalue']) ? $models[1]['subvalue'] : old('models[1]["subvalue"]') }}" />
                </div>
            </div>
            <div class="col-12 form-paper section-divider"></div>
        </div>


        <div class="row">
            <div class="col-12 form-paper section-title">PAGIBIG</div>
            <div class="col-12 form-paper section-divider"></div>
            <div class="col-3 form-paper">
                <div class="form-group">
                    <label class="form-paper-label">PAGIBIG Number</label>
                    <div class="form-paper-display">92-123139-23</div>
                    <input type="hidden" name="models[2]['identifier']" value="{{ isset($models[2]['identifier']) ? $models[2]['identifier'] : old('models[2]["identifier"]') }}" />
                    <input type="hidden" name="models[2]['details']" value="{{ isset($models[2]['details']) ? $models[2]['details'] : old('models[2]["details"]') }}" />
                    <input type="hidden" name="models[2]['id']" value="{{ isset($models[2]['id']) ? $models[2]['id'] : old('models[2]["id"]') }}" />
                </div>
            </div>
            <div class="col-3 form-paper">
                <div class="form-group">
                    <label class="form-paper-label">Employee's Share</label>
                    <input type="number" class="form-control" name="models[2]['amount']" value="{{ isset($models[2]['amount']) ? $models[2]['amount'] : old('models[2]["amount"]') }}" />
                </div>
            </div>
            <div class="col-3 form-paper">
                <div class="form-group">
                    <label class="form-paper-label">Employer's Share</label>
                    <input type="number" class="form-control" name="models[2]['subamount']" value="{{ isset($models[2]['subamount']) ? $models[2]['subamount'] : old('models[2]["subamount"]') }}" />
                </div>
            </div>
            <div class="col-3 form-paper">
                <div class="form-group">
                    <label class="form-paper-label">PAGIBIG Loan</label>
                    <input type="number" class="form-control" name="models[2]['loan']" value="{{ isset($models[2]['loan']) ? $models[2]['loan'] : old('models[2]["loan"]') }}" />
                </div>
            </div>
            <div class="col-12 form-paper section-divider"></div>
        </div>

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
                    <input type="number" class="form-control" name="otherModels[{{$i}}]['amount']" value="{{ isset($otherModels[$i]['amount']) ? $otherModels[$i]['amount'] : old("otherModels[".$i."]['amount']") }}" />
                </div>
            </div>
            <div class="col-3 form-paper">
                <div class="form-group">
                    <label class="form-paper-label">Remarks</label>
                    <input type="text" class="form-control" name="otherModels[{{$i}}]['remarks']" value="{{ isset($otherModels[$i]['remarks']) ? $otherModels[$i]['remarks'] : old("otherModels[".$i."]['remarks']") }}" />
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
                    <input type="submit" class="btn btn-primary" value="Save"/>
                </div>
            </div>
        </div>

    </div>
</div>

@stop

@section('script')

@stop
