@extends('layout.master')

@section('title')
Employee Record
@stop

@section('content')
<div class="row">
    <div class="col-md-12">

        <div class="row">
            <div class="col-12 form-paper section-title">Summary for {{ $details['name'] }}</div>
            <div class="col-12 form-paper section-divider"></div>
            <div class="col-2 form-paper">
                <div class="form-group">
                    <div class="">
                        <input id="Record" type="radio" name="summary" value="record" checked />
                        <label for="Record" class="form-check-label"> Record</label>
                    </div>
                    <div class="">
                        <input id="Outlier" type="radio" name="summary" value="outlier" />
                        <label for="Outlier" class="form-check-label"> Outlier</label>
                    </div>
                </div>
            </div>
            <div class="col-7 form-paper">
                <div class="row">
                    <div class="col-5">
                        <div class="form-group">
                            <label class="form-paper-label">Period</label><br />
                            <div class="form-check-inline">
                                <input id="secondPeriod" type="radio" name="period" value="second" checked />
                                <label for="secondPeriod" class="form-check-label">Second Period</label>
                            </div>
                            <div class="form-check-inline">
                                <input id="firstPeriod" type="radio" name="period" value="first" />
                                <label for="firstPeriod" class="form-check-label">First Period</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-7">
                        <div class="form-group">
                            <label class="form-paper-label">Month and Year</label>
                            <div class="input-group">
                                @include('layout.monthselect', ['form' => ''])
                                <input form="" type="number" min="1991" max="2100" id="yearSelect" class="form-control form-control-sm" name="year" value="{{ isset($date['year']) ? $date['year'] : date_format(now(), 'Y') }}" />
                                <button form="" type="submit" class="btn btn-secondary btn-sm"><i class="fa fa-arrow-right"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-3 form-paper">
                <div class="form-group">
                    <label for="searchBox" class="form-paper-label">Search</label>
                    <input type="search" class="form-control form-control-sm" id="searchBox" onkeyup="filterRecords()" />
                </div>
            </div>
            <div class="col-12 form-paper section-divider"></div>

            <div class="col-12">
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
                                <tr>
                                    <td>{{ $details['employeeId'] }}</td>
                                    <td>{{ $details['timecard'] }}</td>
                                    <td>{{ $details['name'] }}</td>
                                    <td>{{ $details['department'] }}</td>
                                    <?php
                                    $total = 0;
                                    ?>
                                    @for ($i = $details['startday']; $i <= $details['endday']; $i++)
                                    <?php
                                    $total = isset($records[$i]) ? $total + $records[$i]->hours : $total;
                                    ?>
                                    <td width="25px" style="border-right:1px solid lightgray;">{{ isset($records[$i]) ? $records[$i]->hours : '' }}</td>
                                    @endfor
                                    <td>{{ $total }}</td>

                                    <?php
                                        $trot = 0;
                                        $tsot = 0;
                                        $txsot = 0;
                                        $tlhot = 0;
                                        $txlhot = 0;
                                        foreach ($records as $key => $record) {
                                            if ($record == null)
                                                continue;
                                            $trot += $record->rot != '' ? $record->rot : 0;
                                            $tsot += $record->sot != '' ? $record->sot : 0;
                                            $txsot += $record->xsot != '' ? $record->xsot : 0;
                                            $tlhot += $record->lhot != '' ? $record->lhot : 0;
                                            $txlhot += $record->xlhot != '' ? $record->xlhot : 0;
                                        }
                                    ?>

                                    <td>{{ $trot }}</td>
                                    <td>{{ $tsot }}</td>
                                    <td>{{ $txsot }}</td>
                                    <td>{{ $tlhot }}</td>
                                    <td>{{ $txlhot }}</td>
                                    <td></td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                    <div class="col-12 form-paper section-divider"></div>

                </div>
            </div>
            <div class="col-12">
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
                                    <td>{{ $details['timecard'] }}</td>
                                    <td>{{ $details['name'] }}</td>
                                    <td>{{ $details['department'] }}</td>
                                    <td>{{ $record->date }}</td>
                                    <td>{{ $record->hours }}</td>
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
@stop

@section('script')
<script src="{{ asset('js\individualRecordPage.js') }}"></script>
@stop

