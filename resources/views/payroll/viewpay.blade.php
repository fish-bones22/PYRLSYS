@extends('layout.master')

@section('title')
{{ $employee->fullName }} - Payslip
@stop

@section('content')
<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="row">
            <div class="col-12 form-paper section-title">{{ $employee->fullName }} - Payslip</div>
            <div class="col-12 form-paper section-divider"></div>
        </div>
        <div class="row">
            <div class="col-9 form-paper">
                <form action="{{ action('PayrollController@setRecordDate', $employee->id) }}" method="POST" id="setDateForm">
                    @csrf
                    @method('post')
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
            <div class="col-3 form-paper">
                <div class="form-group">
                    <label class="form-paper-label">Payslip Date</label>
                    <input type="date" class="form-control form-control-sm" id="payslipDate" />
                </div>
            </div>
            <div class="col-12 form-paper section-divider"></div>
            <div class="col-12 form-paper section-title">Summary</div>
            <div class="col-12 form-paper section-divider"></div>
            <div class="col-12 form-paper">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>&nbsp;</th>
                            <th>&nbsp;&nbsp;</th>
                            <th>Hours</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr style="background-color:whitesmoke;">
                            <td><strong>Basic Pay</strong></td>
                            <td></td>
                            <td>{{ isset($payroll->regularHours) ? $payroll->regularHours .' hrs' : '0' }}</td>
                            <td>{{ isset($payroll->basicPay) ? $payroll->basicPay : '0' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Overtime</strong></td>
                            <td></td>
                            <td>{{  isset($payroll->otHours) ? $payroll->otHours.' hrs' : '0' }}</td>
                            <td>{{ isset($payroll->otPay) ? $payroll->otPay : '0' }}</td>
                        </tr>
                        <tr>
                            <td><i>&emsp;ROT</i></td>
                            <td>1.25</td>
                            <td>{{ isset($payroll->otDetails['rot']) ? $payroll->otDetails['rot'] : 0 }} hrs</td>
                            <td>{{ isset($payroll->otDetails['rotrate']) ? $payroll->otDetails['rotrate'] : 0  }}</td>
                        </tr>
                        <tr>
                            <td><i>&emsp;SOT/SPH</i></td>
                            <td>1.3</td>
                            <td>{{ isset($payroll->otDetails['sot']) ? $payroll->otDetails['sot'] : 0  }} hrs</td>
                            <td>{{ isset($payroll->otDetails['sotrate']) ? $payroll->otDetails['sotrate'] : 0  }}</td>
                        </tr>
                        <tr>
                            <td><i>&emsp;XSOT</i></td>
                            <td>1.69</td>
                            <td>{{ isset($payroll->otDetails['xsot']) ? $payroll->otDetails['xsot'] : 0  }} hrs</td>
                            <td>{{ isset($payroll->otDetails['xsotrate']) ? $payroll->otDetails['xsotrate'] : 0  }}</td>
                        </tr>
                        <tr>
                            <td><i>&emsp;LHOT</i></td>
                            <td>2</td>
                            <td>{{ isset($payroll->otDetails['lhot']) ? $payroll->otDetails['lhot'] : 0  }} hrs</td>
                            <td>{{ isset($payroll->otDetails['lhotrate']) ? $payroll->otDetails['lhotrate'] : 0  }}</td>
                        </tr>
                        <tr>
                            <td><i>&emsp;XLHOT</i></td>
                            <td>2.69</td>
                            <td>{{ isset($payroll->otDetails['xlhot']) ? $payroll->otDetails['xlhot'] : 0  }} hrs</td>
                            <td>{{ isset($payroll->otDetails['xlhotrate']) ? $payroll->otDetails['xlhotrate'] : 0  }}</td>
                        </tr>
                        <tr>
                            <td><i>&emsp;ND</i></td>
                            <td>0.1</td>
                            <td>{{ isset($payroll->otDetails['nd']) ? $payroll->otDetails['nd'] : 0  }} hrs</td>
                            <td>{{ isset($payroll->otDetails['ndrate']) ? $payroll->otDetails['ndrate'] : 0  }}</td>
                        </tr>
                        <tr style="background-color:whitesmoke;">
                            <td><strong>Gross Pay</strong></td>
                            <td></td>
                            <td></td>
                            <td>{{ isset($payroll->grossPay) ? $payroll->grossPay : '0' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Less: Deductions</strong></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        @foreach ($payroll->exemptionDetails as $key => $ex)
                        <?php if ($key == '_TOTAL' || $key == '_TOTAL_BEFORE_TAX') continue; ?>
                        <tr>
                            <td>&emsp;{{ $key }}</td>
                            <td></td>
                            <td></td>
                            <td>{{ $ex }}</td>
                        </tr>
                        @endforeach
                        <tr style="background-color:whitesmoke;">
                            <td><strong>Net Pay</strong></td>
                            <td></td>
                            <td></td>
                            <td>{{ isset($payroll->netPay) ? $payroll->netPay : '0' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Add: Allowance</strong></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        @if ($payroll->allowance != null && $payroll->allowance != 0)
                        <tr>
                            <td>&emsp;Allowance</td>
                            <td></td>
                            <td></td>
                            <td>{{ $payroll->allowance }}</td>
                        </tr>
                        @endif
                        @foreach ($payroll->adjustmentsDetails as $key => $ex)
                        <?php if ($key == '_TOTAL') continue; ?>
                        <tr>
                            <td>&emsp;{{ $key }}</td>
                            <td></td>
                            <td></td>
                            <td>{{ $ex }}</td>
                        </tr>
                        @endforeach
                        <tr style="background-color:whitesmoke;">
                            <td><strong>Take Home Pay</strong></td>
                            <td></td>
                            <td></td>
                            <td>{{ $payroll->takeHomePay }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>

    </div>
</div>

<div class="m-4">&nbsp;</div>
<div class="fixed-bottom btn-container m-4">
    <div class="float-right">
        <div class="btn-group">
            <a class="btn btn-light" href="{{ action('PayrollController@index') }}">Back to List</a>
            <button type="button" class="btn btn-primary" onclick="printOne({{$employee->id}}, '{{$details['date']}}')" {!! (!isset($payroll->basicPay) || $payroll->basicPay == null || $payroll->basicPay == 0) && (!isset($payroll->takeHomePay) || $payroll->takeHomePay == null || $payroll->takeHomePay == 0) ? 'disabled' : '' !!}>Save as PDF</button>
        </div>
    </div>
</div>

@stop

@section('script')
<script src="{{ asset('js/printPayrollPage.js') }}"></script>
@stop
