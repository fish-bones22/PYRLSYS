@extends('layout.master')

@section('title')
Applicants
@stop

@section('content')

@if (session('error') != null)
<div class="alert alert-danger">{{ session('error') }}<button type="button" class="close" data-dismiss="alert">&times;</button></div>
@endif
@if (session('success') != null)
<div class="alert alert-success">{{ session('success') }}<button type="button" class="close" data-dismiss="alert">&times;</button></div>
@endif


<div class="row">
    <div class="col-md-5 offset-md-2 form-paper">
        <div class="form-group">
            <div class="text-centered">
                <div class="btn-group mt-2 mb-2">
                    <button class="btn btn-light" type="button" onclick="searchStatus('')">All</button>
                    <button class="btn btn-light" type="button" onclick="searchStatus('Pending')">Pending</button>
                    <button class="btn btn-light" type="button" onclick="searchStatus('Processing')">Processing</button>
                    <button class="btn btn-light" type="button" onclick="searchStatus('Hired')">Hired</button>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 form-paper">
        <div class="form-group">
            <label for="searchBox" class="form-paper-label">Search</label>
            <div class="input-group">
                <input id="searchBox" type="search" class="form-control form-control-sm" />
                <button type="button" class="btn btn-secondary btn-sm">Search</button>
            </div>
        </div>
    </div>
    <div class="col-md-8 offset-md-2 form-paper" >

        <table class="table table-sm table-responsive-sm mt-4" id="dataTable">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Applying for</th>
                    <th>Date Applied</th>
                    <th>Status</th>
                    <th>Action</th>
                    <th>&nbsp;</th>
                </tr>
            </thead>
            <tbody>
                @foreach($applicants as $app)
                <tr>
                    <td><a href="{{ action('ApplicantController@show', ['id' => $app->id ]) }}" class="btn btn-link">{{ $app->fullName }}</a></td>
                    <td>{{ key_exists('position', $app->details) ? $app->details['position']['value'] : 'No position' }}</td>
                    <td>{{ date_format($app->dateUpdated, 'M j, y') }}</td>
                    <td>{{ key_exists('applicationstatus', $app->details) ? $app->details['applicationstatus']['value']  : 'No Status' }}</td>

                    <td>

                        @if (key_exists('applicationstatus', $app->details) && $app->details['applicationstatus']['value'] === "Pending")

                        <a href="{{ action('ApplicantController@process', ['id' => $app->id ]) }}" class="btn btn-sm btn-secondary">Process</a>

                        @elseif (key_exists('applicationstatus', $app->details) && $app->details['applicationstatus']['value'] === "Processing")

                        <a href="{{ action('ApplicantController@hire', ['id' => $app->id ]) }}" class="btn btn-sm btn-secondary">Hire</a>

                        @else

                        @endif
                    </td>
                    <td>
                        <form action="{{ route('applicant.destroy', $app->id) }}" method="POST">
                            @csrf
                            @method('delete')
                            <button type="submit" class="close">&times;</button>
                        </form>

                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

    </div>
</div>

@stop
@section('script')

<script>

    var table;
    $(document).ready(function() {
        table = $("#dataTable").DataTable({
            "lengthChange": false,
            "info": false,
            "dom": "<t<'float-right'p>>"
        });
    });

    function searchStatus(term) {
        table.column(3).search(term);
        table.column(3).draw();
    }

</script>

@stop
