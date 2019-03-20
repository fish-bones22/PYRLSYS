<form action="{{ action('CategoryController@store') }}" method="POST">
    @csrf
    @method('post')
    <div class="row">
        <div class="col-12 form-paper">
            <input type="hidden" name="key" value="{{ $key }}" />
            <div class="form-group">
                <label for="nameAdd">{{$displayName}} Name:</label>
                <input type="text" id="nameAdd" class="form-control" name="name" required   />
            </div>
        </div>
        <div class="col-12 form-paper">
            <div class="form-group">
                <label for="descriptionAdd">Description:</label>
                <textarea id="descriptionAdd" class="form-control" name="description"></textarea>
            </div>
        </div>
        @if ($key == 'department')
        <div class="col-12 form-paper section-divider"></div>
        <div class="col-12 form-paper section-title">Shedule</div>
        <div class="col-md-4 col-6 form-paper">
            <div class="form-group">
                <label for="subvalue1Add">Time In:</label>
                <input id="subvalue1Add" class="form-control" name="subvalue1"  type="time" value="{{ date('H:i') }}" />
            </div>
        </div>
        <div class="col-md-4 col-6 form-paper">
            <div class="form-group">
                <label for="subvalue2Add">Time Out:</label>
                <input id="subvalue2Add" class="form-control" name="subvalue2"  type="time" value="{{ date('H:i') }}" />
            </div>
        </div>
        <div class="col-md-4 form-paper">
            <div class="form-group">
                <label for="subvalue3Add">Break (hrs):</label>
                <input id="subvalue3Add" class="form-control" name="subvalue3"  type="number" value="0" />
            </div>
        </div>
        <div class="col-sm-6 col-6 form-paper">
            <div class="form-group">
                <label for="subValue4Add" class="form-paper-label">Date Effective:</label>
                <input id="subValue4Add" class="form-control" type="date" name="subvalue4" value="{{ date_format(now(), 'Y-m-d') }}" />
            </div>
        </div>
        <div class="col-sm-6 col-6 form-paper">
            <div class="form-group">
                <label for="subValue5Add" class="form-paper-label">Until:</label>
                <input id="subValue5Add" class="form-control" type="date" name="subvalue5" />
            </div>
        </div>
        @endif
        <div class="col-12 form-paper">
            <div class="form-group">
                <div class="float-right">
                    <div class="btn-group">
                        <button class="btn btn-light" data-dismiss="modal">Back to List</button>
                        <button type="reset" class="btn btn-secondary">Reset</button>
                        <input type="submit" class="btn btn-primary" value="Save"/>
                    </div>
                </div>
            </div>
            <div class="mb-2">&nbsp;</div>
        </div>
    </div>

</form>
