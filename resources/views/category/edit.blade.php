<form action="{{ action('CategoryController@update', 0) }}" method="POST">
    @csrf
    @method('put')
    <input type="hidden" id="idEdit" name="id" />
    <input type="hidden" id="key" name="key" value="{{ $key }}" />

    <div class="row">
        <div class="col-12 form-paper">
            <div class="form-group">
                <label for="nameEdit">Name:</label>
                <input type="text" id="nameEdit" class="form-control display-toggle" name="name" style="display:none" required  />
                <div id="nameDisplay" class="form-paper-display display-toggle">Retrieving information...</div>
            </div>
        </div>
        <div class="col-12 form-paper">
            <div class="form-group">
                <label for="descriptionEdit">Description:</label>
                <textarea id="descriptionEdit" class="form-control description  display-toggle" name="description" style="display:none"></textarea>
                <div id="descriptionDisplay" class="form-paper-display display-toggle">Retrieving information...</div>
            </div>
        </div>

        @if ($key == 'department')

        <div class="col-6 form-paper">
            <div class="form-group">
                <label for="subValue1Edit">Time In:</label>
                <input id="subValue1Edit" class="form-control description  display-toggle" type="time" name="subvalue1" style="display:none" />
                <div id="subValue1Display" class="form-paper-display display-toggle">Retrieving information...</div>
            </div>
        </div>
        <div class="col-6 form-paper">
            <div class="form-group">
                <label for="subValue2Edit">Time Out:</label>
                <input id="subValue2Edit" class="form-control description  display-toggle" type="time" name="subvalue2" style="display:none" />
                <div id="subValue2Display" class="form-paper-display display-toggle">Retrieving information...</div>
            </div>
        </div>

        @endif

        <div class="col-12 form-paper">
            <div class="form-group">
                <div class="float-right">
                    <div class="btn-group">
                        <button class="btn btn-light" data-dismiss="modal">Back to List</button>
                        <button type="reset" class="btn btn-secondary edit-toggle" onclick="toggleEdit()">Edit</button>
                        <button type="reset" class="btn btn-secondary view-toggle" style="display:none">Reset</button>
                        <input type="submit" class="btn btn-primary view-toggle" value="Save" style="display:none"/>
                    </div>
                </div>
            </div>
            <div class="mb-2">&nbsp;</div>
        </div>

    </div>
</form>
