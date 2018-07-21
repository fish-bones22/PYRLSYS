<form action="{{ action('DepartmentController@update', 0) }}" method="POST">
    @csrf
    @method('put')
    <input type="hidden" id="idEdit" name="id" />
    <div class="form-group">
        <label for="nameEdit">Department Name:</label>
        <input type="text" id="nameEdit" class="form-control display-toggle" name="name" style="display:none" required  />
        <div id="nameDisplay" class="form-control display-toggle">Retrieving information...</div>
    </div>
    <div class="form-group">
        <label for="descriptionEdit">Description:</label>
        <textarea id="descriptionEdit" class="form-control description  display-toggle" name="description" style="display:none"></textarea>
        <div id="descriptionDisplay" class="display-toggle">Retrieving information...</div>
    </div>

    <div class="form-group">
        <div class="float-right">
            <div class="btn-group">
                <button class="btn btn-light" data-dismiss="modal">Back to List</button>
                <button type="reset" class="btn btn-secondary btn-toggle" onclick="toggleEdit()">Edit</button>
                <button type="reset" class="btn btn-secondary btn-toggle" style="display:none">Reset</button>
                <input type="submit" class="btn btn-primary btn-toggle" value="Save" style="display:none"/>
            </div>
        </div>
    </div>
</form>
