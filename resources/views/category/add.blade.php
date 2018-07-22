<form action="{{ action('CategoryController@store') }}" method="POST">
    @csrf
    @method('post')
    <input type="hidden" name="key" value="{{ $key }}" />
    <div class="form-group">
        <label for="nameAdd">Department Name:</label>
        <input type="text" id="nameAdd" class="form-control" name="name" required   />
    </div>
    <div class="form-group">
        <label for="descriptionAdd">Description:</label>
        <textarea id="descriptionAdd" class="form-control" name="description"></textarea>
    </div>

    <div class="form-group">
        <div class="float-right">
            <div class="btn-group">
                <button class="btn btn-light" data-dismiss="modal">Back to List</button>
                <button type="reset" class="btn btn-secondary">Reset</button>
                <input type="submit" class="btn btn-primary" value="Save"/>
            </div>
        </div>
    </div>
</form>
