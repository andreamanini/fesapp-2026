<script id="add-new-bs-note" type="text/x-handlebars-template">
    <div class="form-group row bs-note-row" id="bs-note-row-{{id}}">
        <div class="col-12">
            <label for="notes">Inserimento note importanti</label>
        </div>

        <div class="col-9">
            <input type="text" class="form-control" name="note_title[]" placeholder="Inserisci un titolo per questa nota" />
        </div>

        <div class="col-2">
            <input type="text" class="form-control bs-note-date" name="note_date[]" placeholder="Data" />
        </div>

        <div class="col-1 pb-5 pull-right">
            <button type="button" class="btn btn-danger remove-bs-note" data-row-id="bs-note-row-{{id}}">-</button>
        </div>

        <div class="col-12">
            <textarea class="form-control notes-editor" id="notes{{id}}" name="note_body[]" rows="9" required></textarea>
        </div>
    </div>
</script>