@extends('layout.master')

@section('title')
Employee Record
@stop

@section('content')
<div class="row">
    <div class="col-md-12">

        <div class="row">
            <div class="col-12 form-paper section-title" id="title">Summary for {{ $details['name'] }}</div>
            <div class="col-12 form-paper section-divider"></div>
            <div class="col-2 form-paper">
                <div class="form-group">
                    <input type="hidden" id="currentOutlier" value="record" />
                    <div class="">
                        <input id="Record" type="radio" name="summary" value="record" onchange="changeMode(this)" checked />
                        <label for="Record" class="form-check-label"> Record</label>
                    </div>
                    <div class="">
                        <input id="Outlier" type="radio" name="summary" value="outlier" onchange="changeMode(this)" />
                        <label for="Outlier" class="form-check-label"> Outlier</label>
                    </div>
                </div>
            </div>
            <div class="col-7 form-paper">
                <form action="{{ action('ManhourController@setRecordDate', $details['id']) }}" method="POST" id="setDateForm">
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
            <div class="col-3 form-paper">
                <div class="form-group">
                    <label for="searchBox" class="form-paper-label">Search</label>
                    <input type="search" class="form-control form-control-sm" id="searchBox" onkeyup="filterTables()" />
                </div>
            </div>
            <div class="col-12 form-paper section-divider"></div>

            <div class="col-12 mode-view">
                <div class="row">
                    <div class="col-12 form-paper section-title">Record</div>
                    <div class="col-12 form-paper section-divider"></div>
                    <div class="col-12 form-paper">
                        <table id="recordSummaryTable" class="table table-sm" style="font-size:0.75em;">
                            <thead>
                                <tr>
                                    <th rowspan="2">ID</th>
                                    <th rowspan="2">Timecard</th>
                                    <th rowspan="2">Name</th>
                                    <th rowspan="2">Department</th>
                                    <th colspan="{{ $details['endday'] - $details['startday'] + 2 }}">Regular Hours</th>
                                    <th rowspan="2">ROT</th>
                                    <th rowspan="2">SSHOT</th>
                                    <th rowspan="2">XSSHOT</th>
                                    <th rowspan="2">LHOT</th>
                                    <th rowspan="2">XLHOT</th>
                                    <th rowspan="2">ND</th>
                                </tr>
                                <tr>
                                    @for ($i = $details['startday']; $i <= $details['endday']; $i++)
                                    <th>{{ $i }}</th>
                                    @endfor
                                    <th>T</th>
                                </tr>
                            </thead>
                            <tbody>

                                @if (sizeof($records) > 0)
                                <?php
                                $currentDept = '';
                                $currentTimecard = '';
                                ?>

                                @foreach ($records as $record)
                                <?php
                                // Skip record with the same timecard and department
                                if ($record == null || $record->timeCard == null || $currentTimecard === $record->timeCard)
                                    continue;

                                $currentTimecard = $record->timeCard;
                                $currentDept = $record->departmentName;
                                ?>
                                <tr>
                                    <td>{{ $details['employeeId'] }}</td>
                                    <td>{{ $record->timeCard }}</td>
                                    <td>{{ $details['name'] }}</td>
                                    <td>{{ $record->departmentName }}</td>
                                    <?php
                                    $total = 0;
                                    ?>
                                    @for ($i = $details['startday']; $i <= $details['endday']; $i++)
                                    {{-- Skip record not belonging to current timecard --}}
                                    @if ($records[$i]->timeCard != $currentTimecard)
                                    <td width="25px" style="border-right:1px solid lightgray;"></td>
                                    @else
                                    <?php
                                    $total = isset($records[$i]) ? $total + $records[$i]->totalHours : $total;
                                    ?>
                                    <td width="25px" style="border-right:1px solid lightgray;">{{ isset($records[$i]) ? $records[$i]->totalHours : '' }}</td>
                                    @endif
                                    @endfor
                                    <td>{{ $total }}</td>

                                    <?php
                                        $trot = 0;
                                        $tsot = 0;
                                        $txsot = 0;
                                        $tlhot = 0;
                                        $txlhot = 0;
                                        foreach ($records as $key => $record) {
                                            if ($record == null || $record->timeCard != $currentTimecard)
                                                continue;
                                            $trot += $record->rot != '' ? $record->rot : 0;
                                            $tsot += $record->sot != '' ? $record->sot : 0;
                                            $txsot += $record->xsot != '' ? $record->xsot : 0;
                                            $tlhot += $record->lhot != '' ? $record->lhot : 0;
                                            $txlhot += $record->xlhot != '' ? $record->xlhot : 0;
                                        }
                                    ?>

                                    <td>{{ $trot != 0 ? $trot : '' }}</td>
                                    <td>{{ $tsot != 0 ? $tsot : ''  }}</td>
                                    <td>{{ $txsot != 0 ? $txsot : ''  }}</td>
                                    <td>{{ $tlhot != 0 ? $tlhot : ''  }}</td>
                                    <td>{{ $txlhot != 0 ? $txlhot : ''  }}</td>
                                    <td></td>
                                </tr>

                                @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                    <div class="col-12 form-paper section-divider"></div>

                </div>
            </div>
            <div class="col-12 mode-view" style="display:none">
                <div class="row">

                    <div class="col-12 form-paper section-title">Outliers</div>
                    <div class="col-12 form-paper section-divider"></div>
                    <div class="col-12 form-paper">
                        <table id="outlierSummaryTable" class="table table-sm">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Timecard</th>
                                    <th>Name</th>
                                    <th>Department</th>
                                    <th>Date</th>
                                    <th>Hours</th>
                                    <th>Type</th>
                                    <th>Authorized</th>
                                    <th>Reason</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($records as $record)
                                <?php if ($record->outlier == null) continue; ?>
                                <tr>
                                    <td>{{ $details['employeeId'] }}</td>
                                    <td>{{ $record->timeCard }}</td>
                                    <td>{{ $details['name'] }}</td>
                                    <td>{{ $record->departmentName }}</td>
                                    <td>{{ $record->date }}</td>
                                    <td>{{ $record->totalHours }}</td>
                                    <td>{{ $record->outlier }}</td>
                                    <td>{{ $record->authorized == true ? 'Yes' : 'No' }}</td>
                                    <td>{{ $record->remarks }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

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

@stop

@section('script')
<script src="{{ asset('js\individualRecordPage.js') }}"></script>
@stop

