<form action="{{ action('CategoryController@update', 0) }}" method="POST">
    @csrf
    @method('put')
    <input type="hidden" id="idEdit" name="id" />
    <input type="hidden" id="key" name="key" value="{{ $key }}" />

    <div class="row">
        <div class="col-12 form-paper">
            <div class="form-group">
                <label for="nameEdit" class="form-paper-label">Name:</label>
                <input type="text" id="nameEdit" class="form-control display-toggle" name="name" style="display:none" required  />
                <div id="nameDisplay" class="form-paper-display display-toggle">Retrieving information...</div>
            </div>
        </div>
        <div class="col-12 form-paper">
            <div class="form-group">
                <label for="descriptionEdit" class="form-paper-label">Description:</label>
                <textarea id="descriptionEdit" class="form-control description  display-toggle" name="description" style="display:none"></textarea>
                <div id="descriptionDisplay" class="form-paper-display display-toggle">Retrieving information...</div>
            </div>
        </div>

        @if ($key == 'department')

        <div class="col-12 form-paper section-divider"></div>
        <div class="col-12 form-paper section-title">Shedule</div>
        <div class="col-sm-4 col-6 form-paper">
            <div class="form-group">
                <label for="subValue1Edit" class="form-paper-label">Time In:</label>
                <input id="subValue1Edit" class="form-control description  display-toggle" type="time" name="subvalue1" style="display:none" />
                <div id="subValue1Display" class="form-paper-display display-toggle">Retrieving information...</div>
            </div>
        </div>
        <div class="col-sm-4 col-6 form-paper">
            <div class="form-group">
                <label for="subValue2Edit" class="form-paper-label">Time Out:</label>
                <input id="subValue2Edit" class="form-control description display-toggle" type="time" name="subvalue2" style="display:none" />
                <div id="subValue2Display" class="form-paper-display display-toggle">Retrieving information...</div>
            </div>
        </div>
        <div class="col-sm-4 col-12 form-paper">
            <div class="form-group">
                <label for="subValue3Edit" class="form-paper-label">Break (hrs):</label>
                <input id="subValue3Edit" class="form-control description display-toggle" type="number" name="subvalue3" style="display:none" />
                <div id="subValue3Display" class="form-paper-display display-toggle">Retrieving information...</div>
            </div>
        </div>
        <div class="col-sm-6 col-6 form-paper">
            <div class="form-group">
                <label for="subValue4Edit" class="form-paper-label">Date Effective:</label>
                <input id="subValue4Edit" class="form-control description display-toggle" type="date" name="subvalue4" style="display:none"  value="{{ date_format(now(), 'Y-m-d') }}" />
                <div id="subValue4Display" class="form-paper-display display-toggle">Retrieving information...</div>
            </div>
        </div>
        <div class="col-sm-6 col-6 form-paper">
            <div class="form-group">
                <label for="subValue5Edit" class="form-paper-label">Until:</label>
                <input id="subValue5Edit" class="form-control description display-toggle" type="date" name="subvalue5" style="display:none" />
                <div id="subValue5Display" class="form-paper-display display-toggle">Retrieving information...</div>
            </div>
        </div>
        <span class="col-12 form-paper description display-toggle" style="display:none" >
            <div class="form-check mt-2">
                <input id="checkbox1Edit" class="form-check-input" type="checkbox" name="checkbox1"/>
                <label for="checkbox1Edit">Cascade changes to employees under department</label>
            </div>
        </span>

        @endif

        <div class="col-12 form-paper">
            <div class="form-group">
                <div class="float-right">
                    <div class="btn-group">
                        <button class="btn btn-light" data-dismiss="modal">Back to List</button>
                        <button type="reset" class="btn btn-secondary edit-toggle" onclick="toggleEdit()">Edit</button>
                        <button type="reset" class="btn btn-secondary view-toggle" style="display:none">Reset</button>
                        <input type="submit" data-confirm="save" class="btn btn-primary view-toggle" value="Save" style="display:none"/>
                    </div>
                </div>
            </div>
            <div class="mb-2">&nbsp;</div>
        </div>

    </div>
</form>
