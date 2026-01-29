
<div class="form-group row bs-note-row" id="bs-note-row-{{ $id }}">
    <div class="col-12">
        <label for="notes">Inserimento note importanti</label>
    </div>

    <div class="col-9">
        <input type="text" class="form-control" name="note_title[]" value="@isset($noteTitle){{ $noteTitle }}@endisset" placeholder="Inserisci un titolo per questa nota" />
    </div>

    <div class="col-2">
        <input type="text" class="form-control bs-note-date" name="note_date[]" value="@isset($noteDate){{ $noteDate }}@endisset" placeholder="Data" />
    </div>

    <div class="col-1 pb-5 pull-right">
        @if($addButton)
            <button type="button" class="btn btn-primary" id="add-bs-note">+</button>
        @else
            <button type="button" class="btn btn-danger remove-bs-note" data-row-id="bs-note-row-{{ $id }}">-</button>
        @endif
    </div>

    <div class="col-12">
        <textarea class="form-control notes-editor" id="notes{{ $id }}" name="note_body[]" rows="9">@isset($noteBody){{ $noteBody }}@endisset</textarea>
    </div>
</div>