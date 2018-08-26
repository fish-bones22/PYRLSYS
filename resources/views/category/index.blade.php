@extends('layout.master')

@section('title')
<?php
    // $displayName =  is_array($categories) && sizeof($categories) != 0 ? $categories['key'] : 'Category';
    $displayName = $details['displayName'];
    $key =  $details['key'];
    //$key = $categories['key'];
?>
{{ $displayName }}
@stop

@section('content')

@if (session('error') != null)
<div class="alert alert-danger">{{ session('error') }}<button type="button" class="close" data-dismiss="alert">&times;</button></div>
@endif
@if (session('success') != null)
<div class="alert alert-success">{{ session('success') }}<button type="button" class="close" data-dismiss="alert">&times;</button></div>
@endif

<div class="row">
    <div class="col-md-6 offset-md-3 form-paper section-title">{{ $displayName }}</div>
    <div class="col-md-6 offset-md-3 form-paper section-divider"></div>
</div>
<div class="row">
    <div class="col-md-2 offset-md-3 form-paper">
        <div class="form-group mt-3">
            <button class="btn btn-light btn-sm btn-block" type="button" data-toggle="modal" data-target="#addModal">New {{ $displayName }}</button>
        </div>
    </div>
    <div class="col-md-4 form-paper">
        <div class="form-group">
            <label for="searchBox" class="form-paper-label">Search</label>
            <input id="searchBox" class="form-control form-control-sm" type="search" onkeyup="filterDepartment()" />
        </div>
    </div>
    <div class="col-md-6 offset-md-3 form-paper section-divider"></div>
</div>
<div class="row">
    <div class="col-md-6 offset-md-3 form-paper">
        <table class="table table-sm" id="departmentsTable">
            <thead>
                <tr>
                    <th>{{$displayName}} Name</th>
                    <th>&nbsp;</th>
                </tr>
            </thead>
            <tbody>
                @foreach($categories as $category)
                <tr>
                    <td>{{ $category->value }}</td>
                    <td>
                        <span class="btn-group float-right">
                            <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#editModal" data-id="{{ $category->id }}" onclick="getDetails(this)">Edit</button>
                            <form action="{{ route('category.destroy', $category->id) }}" method="POST">
                                @csrf
                                @method('delete')
                                <input type="hidden" name="key" value="{{ $category->key }}" />
                                <input type="submit" class="btn btn-sm btn-light" data-confirm="Delete" value="Delete" />
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

    </div>
</div>

<div id="addModal" class="modal fade" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="row">
                <div class="col-12 form-paper section-title">
                    New {{$displayName}}
                    <button class="close" type="button" data-dismiss="modal">&times;</button>
                </div>
            </div>
            @include('category.add', ['key' => $key, 'displayName' => $displayName])
        </div>
    </div>
</div>

<div id="editModal" class="modal fade" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="row">
                <div class="col-12 form-paper section-title">
                    Edit {{$displayName}}
                    <button class="close" type="button" data-dismiss="modal">&times;</button>
                </div>
            </div>
            @include('category.edit', ['key' => $key, 'displayName' => $displayName])
        </div>
    </div>
</div>

@stop

@section('script')
<script src="{{ asset('js/categories.js') }}"></script>
@stop
