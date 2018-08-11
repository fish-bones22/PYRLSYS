@extends('layout.master')

@section('title')
Employee Record
@stop

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="row">
            <div class="col-12 form-paper section-title">Record - {{ $details['name'] }}</div>
            <div class="col-12 form-paper section-divider"></div>
            <div class="col-12 form-paper">
                <table class="table table-sm" style="font-size:0.75em;">
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
                        $firstLoop = true;
                        ?>
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
                                    $trot += $record->rot != '' ? $record->rot : 0;
                                    $tsot += $record->sot != '' ? $record->tsot : 0;
                                    $txsot += $record->xsot != '' ? $record->txsot : 0;
                                    $tlhot += $record->lhot != '' ? $record->tlhot : 0;
                                    $txlhot += $record->xlhot != '' ? $record->txlhot : 0;
                                }
                            ?>

                            <td>{{ $trot }}</td>
                            <td>{{ $tsot }}</td>
                            <td>{{ $txsot }}</td>
                            <td>{{ $tlhot }}</td>
                            <td>{{ $txlhot }}</td>

                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@stop

