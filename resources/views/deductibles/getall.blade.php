@extends('layout.master')

@section('title')
{{date_format(date_create($details['date']), 'M Y') }} - Deductibles
@stop

@section('content')

<?php
$rcd = array();
foreach ($records as $record) {
    if (!isset($rcd[$record->employee['id']])) {
        $rcd[$record->employee['id']] = [
            'employeeId' => $record->employee['employeeId'],
            'name' => $record->employee['name'],
            'inactive' => $record->employee['inactive']
        ];
    }

    $rcd[$record->employee['id']][$record->key] = [
        'employee' => $record->amount,
        'employer' => $record->subamount
    ];


}
?>

<div class="row">
    <div class="col-md-10 offset-md-1">
        <div class="row">
            <div class="col-12 form-paper section-title" id="title">{{ date_format(date_create($details['date']), 'M Y') }} - Deductibles</div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="row">
                    <div class="col-4 form-paper">
                        <div class="form-group">
                            <div class="form-check-inline">
                                <input id="overrideValues" class="form-check-input" type="checkbox" name="override_values" />
                                <label for="overrideValues" class="form-check-label small">Override Values <span class="small"></span></label>
                                <input id="date" type="hidden" value="{{ $details['date'] }}" />
                            </div>
                            <button type="button" class="btn btn-light btn-sm" onclick="autogenerate()">Auto-generate Remittances</button>
                        </div>
                    </div>
                    <div class="col-8 form-paper">
                        <form id="setDateForm" action="{{ action('DeductibleRecordController@getAllOnDate') }}" method="get">
                            @csrf
                            @method('get')
                            <div class="row">
                                <div class="col-md-5 col-12">
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
                                <div class="col-md-7 col-12">
                                    <div class="form-group">
                                        <label class="form-paper-label">Month and Year</label>
                                        <div class="input-group">
                                            @include('layout.monthselect', ['form' => 'setDateForm', 'monthSelected' => ( isset($details['month']) ? $details['month'] : date_format(now(), 'm') ), 'name' => 'month' ])
                                            <input type="number" min="1991" max="2100" id="yearSelect" class="form-control form-control-sm" name="year" value="{{ isset($details['year']) ? $details['year'] : date_format(now(), 'Y') }}" />
                                            <button type="submit" class="btn btn-secondary btn-sm"><i class="fa fa-arrow-right"></i></button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 mb-2">
                                    <div class="form-check float-right">
                                        <label for="statusToggler" class="form-check-label"><input type="checkbox" class="form-check-input" id="statusToggler" onkeyup="filterStatus()" /> Show inactive employees</label>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-12 form-paper section-divider"></div>
        </div>

        <div class="row">
            <div class="col-12 form-paper">
                <div style="overflow-x:auto">
                    <table class="table table-sm" id="deductiblesTable" style="font-size:11px;">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>SSS Emp</th>
                                <th>SSS Emr</th>
                                <th>PhilHealth Emp</th>
                                <th>PhilHealth Emr</th>
                                <th>PAGIBIG Emp</th>
                                <th>PAGIBIG Emr</th>
                                <th>Withholding Tax</th>
                                <th style="display:none">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sssEmp = 0;
                            $philhealthEmp = 0;
                            $pagibigEmp = 0;
                            $sssEmr = 0;
                            $philhealthEmr = 0;
                            $pagibigEmr = 0;
                            $tax = 0;
                            ?>
                            @foreach ($rcd as $key => $record)
                            <?php
                            $sssEmp += (isset($record['sss']) ? $record['sss']['employee'] : 0);
                            $philhealthEmp += (isset($record['philhealth']) ? $record['philhealth']['employee'] : 0);
                            $pagibigEmp += (isset($record['pagibig']) ? $record['pagibig']['employee'] : 0);
                            $sssEmr += (isset($record['sss']) ? $record['sss']['employer'] : 0);
                            $philhealthEmr += (isset($record['philhealth']) ? $record['philhealth']['employer'] : 0);
                            $pagibigEmr += (isset($record['pagibig']) ? $record['pagibig']['employer'] : 0);
                            $tax += (isset($record['tin']) ? $record['tin']['employee'] : 0);
                            ?>
                                <tr>
                                    <td>{{ $record['employeeId'] }}</td>
                                    <td>{{ $record['name'] }}</td>
                                    <td>{{ isset($record['sss']) ? $record['sss']['employee'] : '' }}</td>
                                    <td>{{ isset($record['sss']) ? $record['sss']['employer'] : '' }}</td>
                                    <td>{{ isset($record['philhealth']) ? $record['philhealth']['employee'] : '' }}</td>
                                    <td>{{ isset($record['philhealth']) ? $record['philhealth']['employer'] : '' }}</td>
                                    <td>{{ isset($record['pagibig']) ? $record['pagibig']['employee'] : '' }}</td>
                                    <td>{{ isset($record['pagibig']) ? $record['pagibig']['employer'] : '' }}</td>
                                    <td>{{ isset($record['tin']) ? $record['tin']['employee'] : 0 }}</td>
                                    <td style="display: none">{{ $record['inactive'] ? 'Inactive' : 'Active' }}</td>
                                </tr>
                            @endforeach
                            @if ($sssEmp + $sssEmr + $philhealthEmp + $philhealthEmr + $pagibigEmp + $pagibigEmr + $tax > 0)
                            <tr>
                                <td>TOTAL</td>
                                <td></td>
                                <td>{{ $sssEmp }}</td>
                                <td>{{ $sssEmr }}</td>
                                <td>{{ $philhealthEmp }}</td>
                                <td>{{ $philhealthEmr }}</td>
                                <td>{{ $pagibigEmp }}</td>
                                <td>{{ $pagibigEmr }}</td>
                                <td>{{ $tax }}</td>
                                <td style="display: none"></td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>

<div class="m-4">&nbsp;</div>
<div class="fixed-bottom btn-container m-4">
    <div class="float-right">
        <div class="btn-group">
            {{-- <a class="btn btn-light" href="{{ action('EmployeeController@index') }}">Back to List</a> --}}
            <button type="button" class="btn btn-primary" onclick="saveAsPDF()">Save as PDF</button>
            <button type="button" class="btn btn-primary" onclick="saveAsExcel()">Save as Excel</button>
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="generateDialogModal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div class="row">
                    <div class="col-12">
                        <p><strong>Do you want to auto-generate deductibles? This can take a while.</strong></p>
                    </div>
                    <div class="col-12">
                        <div class="float-right btn-group">
                            <button type="button" class="btn btn-primary btn-generate" onclick="startGeneration()">Generate</button>
                            <button type="button" class="btn btn-secondary btn-generate" data-dismiss="modal" >Cancel</button>
                        </div>
                    </div>
                    <div class="col-12 text-center" id="generation-notification">
                        <hr/>
                        <p>Generated <span id="generate-current">0</span> of <span id="generate-total">0</span></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@stop

@section('script')
<script src="{{ asset('js/getAllDeductibleRecord.js') }}"></script>
@stop
