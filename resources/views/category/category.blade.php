@extends('layout.master')

@section('title')
Container
@stop

@section('content')
<div class="col-md-4 offset-md-4">
    <form action="{{ action('DepartmentController@setCategory') }}" method="POST">
        @csrf
        @method('post')
        <select class="form-control" name="category">
            @foreach ($categories as $category)
                <option value="{{ $category['key'] }}">{{ $category['displayName'] }}</option>
            @endforeach
        </select>
        <div class="form-group">
            <button type="submit" class="btn btn-primary btn-block">Submit</button>
        </div>
    </form>
</div>
@stop

@section('script')

@stop
