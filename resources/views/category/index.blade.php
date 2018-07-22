@extends('layout.master')

@section('title')
{{ $categories[0]->displayName }}
@stop

@section('content')

<div class="">
    {{ session('success') }}
    {{ session('error') }}
</div>
<button class="btn btn-link" type="button" data-toggle="modal" data-target="#addModal">New {{ $categories[0]->displayName }}</button>
<table class="table table-responsive">
    @foreach($categories as $category)
    <tr>
        <td>{{ $category->value }}</td>
        <td><button type="button" class="btn btn-link" data-toggle="modal" data-target="#editModal" data-id="{{ $category->id }}" onclick="getDetails(this)">Edit</button></td>
        <td>
            <form action="{{ route('category.destroy', $category->id) }}" method="POST">
                @csrf
                @method('delete')
                <input type="hidden" name="key" value="{{ $category->key }}" />
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
                @include('category.add', ['key' => $categories[0]->key])
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
                @include('category.edit', ['key' => $categories[0]->key])
            </div>
        </div>
    </div>
</div>

@stop

@section('script')
<script src="{{ asset('js/categories.js') }}"></script>
@stop
