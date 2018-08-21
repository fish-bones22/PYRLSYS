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
                </div>
            </div>
            <div class="col-3 form-paper">
                <div class="form-group">
                    <label class="form-paper-label">Employee's Share</label>
                    <input type="number" class="form-control" />
                </div>
            </div>
            <div class="col-3 form-paper">
                <div class="form-group">
                    <label class="form-paper-label">Employer's Share</label>
                    <input type="number" class="form-control" />
                </div>
            </div>
            <div class="col-3 form-paper">
                <div class="form-group">
                    <label class="form-paper-label">SSS Loan</label>
                    <input type="number" class="form-control" />
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
                    <div class="form-paper-display">92-123139-23</div>
                </div>
            </div>
            <div class="col-4 form-paper">
                <div class="form-group">
                    <label class="form-paper-label">Employee's Share</label>
                    <input type="number" class="form-control" />
                </div>
            </div>
            <div class="col-4 form-paper">
                <div class="form-group">
                    <label class="form-paper-label">Employer's Share</label>
                    <input type="number" class="form-control" />
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
                </div>
            </div>
            <div class="col-3 form-paper">
                <div class="form-group">
                    <label class="form-paper-label">Employee's Share</label>
                    <input type="number" class="form-control" />
                </div>
            </div>
            <div class="col-3 form-paper">
                <div class="form-group">
                    <label class="form-paper-label">Employer's Share</label>
                    <input type="number" class="form-control" />
                </div>
            </div>
            <div class="col-3 form-paper">
                <div class="form-group">
                    <label class="form-paper-label">PAGIBIG Loan</label>
                    <input type="number" class="form-control" />
                </div>
            </div>
            <div class="col-12 form-paper section-divider"></div>
        </div>

        <div class="row">
            <div class="col-12 form-paper section-title">OTHER DEDUCTIONS</div>
            <div class="col-12 form-paper section-divider"></div>
            <div class="col-3 form-paper">
                <div class="form-group">
                    <label class="form-paper-label">Deduction Type</label>
                    <select class="form-control">
                        <option>Company Loan</option>
                        <option>Meal Deduction</option>
                        <option>Medical Deduction</option>
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
                    <input type="number" class="form-control" />
                </div>
            </div>
            <div class="col-3 form-paper">
                <div class="form-group">
                    <label class="form-paper-label">Remarks</label>
                    <input type="text" class="form-control" />
                </div>
            </div>
            <div class="col-12 form-paper section-divider"></div>
        </div>

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
