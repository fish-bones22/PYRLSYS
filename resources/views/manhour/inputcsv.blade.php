@extends('layout.master')

<?php
$title = 'CSV Input'
?>
@section('title')
{{ $title }}
@stop

@section('content')

@if (session('error') != null)
<div class="alert alert-danger">{{ session('error') }}<button type="button" class="close" data-dismiss="alert">&times;</button></div>
@endif
@if (session('success') != null)
<div class="alert alert-success">{{ session('success') }}<button type="button" class="close" data-dismiss="alert">&times;</button></div>
@endif

<div class="row">
    <div class="col-12 form-paper section-title" id="title">{{ $title }}
        <span class="float-right">
            <a href="{{ route('manhour.inputall', date_format(now(), 'Y-m-d')) }}" class="btn btn-link btn-clipping">Manual batch input</a>
        </span>
    </div>
    <div class="col-12 form-paper section-divider"></div>
</div>
<form action="{{ route('manhour.postcsv') }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('post')
    <div class="row">
        <div class="col-md-9 form-paper">
            <div class="form-group">
                <label for="csvFile" class="form-paper-label">CSV File</label>
                <div><input name="csvfile" id="csvFile" type='file' /></div>
            </div>
        </div>
        <div class="col-md-3 form-paper">
            <div class="form-group mt-3">
                <input type="submit" value="Upload" class="btn btn-sm btn-block btn-primary"/>
            </div>
        </div>
    </div>
</form>
@if ($records != null)
<form action="{{ route('manhour.postcsvrecords') }}" method="POST" id="cvrRecordsForm">
    @csrf
    @method('post')
    <div class="row">
        <div class="col-12 form-paper section-divider"></div>
        <div class="col-lg-3 col-md-5 form-paper">
            <div class="form-check-inline">
                <div class="">
                    <input id="overwrite" type="checkbox" name="overwrite" class="form-check-input" />
                    <label for="overwrite" class="form-check-label">Overwrite existing records</label>
                </div>
            </div>
        </div>
        <div class="col-lg-9 col-md form-paper">
            <div class="form-check-inline">
                <div class="">
                    <span class="">
                        <input id="approveOt" type="radio" name="otselection" value="approve" class="form-check-input" />
                        <label for="approveOt" class="form-check-label">Approve OTs</label>&nbsp;
                    </span>
                    <span>
                        <input id="denyOt" type="radio" name="otselection" value="deny" class="form-check-input" />
                        <label for="denyOt" class="form-check-label">Deny OTs</label>
                    </span>
                </div>
            </div>
        </div>
        <div class="col-12 form-paper section-divider"></div>
    </div>
    <div class="row">
        <div class="col form-paper">
            <div style="overflow-x: auto" class="mb-4">
                @if (isset($records) && $records != null)
                <table class="table table-sm" id="dailyWorkingHoursTable" style="font-size:0.8em;">
                    <thead>
                        <tr class="text-center">
                            <th rowspan="2">Employee ID</th>
                            <th rowspan="2">Timecard</th>
                            <th rowspan="2">Name</th>
                            <th rowspan="2">Project/Department</th>
                            <th rowspan="2">Date</th>
                            <th colspan="2">Regular Time</th>
                            <th rowspan="2">Outlier</th>
                            <th rowspan="2">Auth</th>
                            {{-- <th colspan="5">Overtime</th> --}}
                            <th rowspan="2">OT Appr</th>
                            <th rowspan="2">Remarks/Comments</th>
                            <th rowspan="2">Wrn</th>
                        </tr>
                        <tr>
                            <th>In</th>
                            <th>Out</th>
                            {{-- <th>ROT</th>
                            <th>SOT</th>
                            <th>XSOT</th>
                            <th>LHOT</th>
                            <th>XLOT</th> --}}
                        </tr>
                    </thead>
                    <tbody>
                        @for ($i = 0; $i < sizeof($records); $i++)
                        <?php
                            if ($records[$i] === null)
                                continue;
                        ?>
                        <tr class="{{ isset($records[$i]['invalid']) ? 'warning' : '' }}">
                            <td>{{ $records[$i]['employeeid'] }}</td>
                            <td>{{ $records[$i]['timecard'] }}</td>
                            <td>{{ $records[$i]['name'] }}</td>
                            <td>{{ $records[$i]['department'] != null ?$records[$i]['department']['displayName'] : '' }}</td>
                            <td>{{ isset($records[$i]['date']) ? $records[$i]['date'] : '' }}</td>
                            <td>{{ $records[$i]['timein'] }}</td>
                            <td>{{ $records[$i]['timeout'] }}</td>
                            <td>{{ isset($records[$i]['outlier']) ? $records[$i]['outlier']->value : '' }}</td>
                            <td>{{ isset($records[$i]['authorized']) ? $records[$i]['authorized'] ? 'Yes' : 'No' : '' }}</td>
                            {{-- <td>{{ isset($records[$i]['rot']) ? $records[$i]['rot'] : '' }}</td>
                            <td>{{ isset($records[$i]['sot']) ? $records[$i]['sot'] : '' }}</td>
                            <td>{{ isset($records[$i]['xsot']) ? $records[$i]['xsot'] : '' }}</td>
                            <td>{{ isset($records[$i]['lhot']) ? $records[$i]['lhot'] : '' }}</td>
                            <td>{{ isset($records[$i]['xlot']) ? $records[$i]['xlot'] : '' }}</td> --}}
                            <td>{{ isset($records[$i]['otapproval']) ? ($records[$i]['otapproval'] ? 'Approved' : 'Denied') : '' }}</td>
                            <td>{{ isset($records[$i]['remarks']) ? $records[$i]['remarks'] : '' }}</td>
                            <td>{{ isset($records[$i]['warning']) ? $records[$i]['warning'] : '' }}</td>
                        </tr>
                        <input type="hidden" name="records[{{$i}}][timecard]" value="{{ $records[$i]['timecard'] }}" />
                        <input type="hidden" name="records[{{$i}}][employee_id]" value="{{ $records[$i]['employee_id'] }}" />
                        <input type="hidden" name="records[{{$i}}][employeeid]" value="{{ $records[$i]['employeeid'] }}" />
                        <input type="hidden" name="records[{{$i}}][name]" value="{{ $records[$i]['name'] }}" />
                        <input type="hidden" name="records[{{$i}}][department]" value="{{ $records[$i]['department'] != null ? $records[$i]['department']['value'] : '' }}" />
                        <input type="hidden" name="records[{{$i}}][date]" value="{{ $records[$i]['date'] }}" />
                        <input type="hidden" name="records[{{$i}}][timein]" value="{{ $records[$i]['timein'] }}" />
                        <input type="hidden" name="records[{{$i}}][timeout]" value="{{ $records[$i]['timeout'] }}" />
                        <input type="hidden" name="records[{{$i}}][outlier]" value="{{ isset($records[$i]['outlier']) ? $records[$i]['outlier']->id : '' }}" />
                        <input type="hidden" name="records[{{$i}}][authorized]" value="{{ isset($records[$i]['authorized']) ? $records[$i]['authorized'] : '' }}" />
                        <input type="hidden" name="records[{{$i}}][otapproval]" value="{{ isset($records[$i]['otapproval']) ? ($records[$i]['otapproval'] ? '1' : '0' ) : '' }}" />
                        <input type="hidden" name="records[{{$i}}][rot]" value="{{ isset($records[$i]['rot']) ? $records[$i]['rot'] : '' }}" />
                        <input type="hidden" name="records[{{$i}}][sot]" value="{{ isset($records[$i]['sot']) ? $records[$i]['sot'] : '' }}" />
                        <input type="hidden" name="records[{{$i}}][xsot]" value="{{ isset($records[$i]['xsot']) ? $records[$i]['xsot'] : '' }}" />
                        <input type="hidden" name="records[{{$i}}][lhot]" value="{{ isset($records[$i]['lhot']) ? $records[$i]['lhot'] : '' }}" />
                        <input type="hidden" name="records[{{$i}}][xlot]" value="{{ isset($records[$i]['xlot']) ? $records[$i]['xlot'] : '' }}" />
                        <input type="hidden" name="records[{{$i}}][nd]" value="{{ isset($records[$i]['nd']) ? $records[$i]['nd'] : '' }}" />
                        <input type="hidden" name="records[{{$i}}][remarks]" value="{{ isset($records[$i]['remarks']) ? $records[$i]['remarks'] : '' }}" />

                        @if (isset($records[$i]['invalid']))
                        <input type="hidden" name="records[{{$i}}][invalid]" value="{{ isset($records[$i]['invalid']) ? $records[$i]['invalid'] : '' }}" />
                        @endif

                        @endfor
                    </tbody>
                </table>
                @endif
            </div>
        </div>
    </div>
    <div class="m-4">&nbsp;</div>
    <div class="fixed-bottom btn-container m-4">
        <div class="float-right">
            <div class="btn-group">
                <a class="btn btn-light" href="{{ action('ManhourController@index') }}">Back to List</a>
                <input class="btn btn-primary" value="Save" type="submit" data-confirm="save" />
            </div>
        </div>
    </div>
</form>
@endif
<div class="m-4">&nbsp;</div>

@stop

@section('script')
<script src="{{ asset('js/attendancePage.js') }}"></script>
@stop
