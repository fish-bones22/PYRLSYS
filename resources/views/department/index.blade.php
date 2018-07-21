@extends('layout.master')

@section('title')
Departments
@stop

@section('content')

<div class="">
    {{ session('success') }}
    {{ session('error') }}
</div>
<button class="btn btn-link" type="button" data-toggle="modal" data-target="#addModal">New Department</button>
<table class="table table-responsive">
    @foreach($departments as $department)
    <tr>
        <td>{{ $department->name }}</td>
        <td><button type="button" class="btn btn-link" data-toggle="modal" data-target="#editModal" data-id="{{ $department->id }}" onclick="getDetails(this)">Edit</button></td>
        <td>
            <form action="{{ route('department.destroy', $department->id) }}" method="POST">
                @csrf
                @method('delete')
                <input type="submit" class="btn btn-link" value="Delete" />
            </form>
        </td>
    </tr>
    @endforeach
</table>

<div id="addModal" class="modal fade" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create New</h5>
                <button class="close" type="button" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                @include('department.add')
            </div>
        </div>
    </div>
</div>

<div id="editModal" class="modal fade" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"></h5>
                <button class="close" type="button" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                @include('department.edit')
            </div>
        </div>
    </div>
</div>

@stop

@section('script')
<script src="{{ asset('js/department.js') }}"></script>
@stop
