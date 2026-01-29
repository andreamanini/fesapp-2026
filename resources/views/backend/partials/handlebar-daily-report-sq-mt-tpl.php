<script id="add-new-sqmt" type="text/x-handlebars-template">
    <tr class="mq-row">
        <input type="hidden"  value="0" name="id_row[]" />
        <td data-title="TIPO LAVORO">
            <select id="work-type-{{idRow}}" name="work_type[]" class="form-control">
                <option value="">Seleziona tipologia</option>
                <option value="V">Verniciatura</option>
                <option value="S">Sabbiatura</option>
                <option value="L">Lavaggio</option>
                <option value="SOFF">Soffiatura</option>
                <option value="I">Intonaco</option>
                <option value="II">Intonaci intumescenti</option>
                <option value="VIM">Verniciatura impregnante</option>
                <option value="VIN">Verniciatura intumescente</option>
                <option value="VA">Verniciatura anticorrosiva</option>
                <option value="VC">Verniciatura da carrozzeria</option>
                <option value="I">Intonaco</option>
                <option value="ALTRO">Altro</option>
            </select>
        </td>
        <td data-title="STRUTTURA">
            <input type="text"
                   class="form-control strutt-type strutt-type-{{idRow}}"
                   id="struttura{{idRow}}"
                   data-uniqueid="{{idRow}}"
                   name="struttura[]"
                   value=""
                   maxlength="255"
                   placeholder="Compila il dettaglio della struttura" />
        </td>
        <td data-title="MATERIALE">
            <input type="text" class="form-control" id="materiale-{{idRow}}" name="materiale[]" maxlength="255" />
        </td>
        <td data-title="QUANTITÀ">
            <input type="number" class="form-control row-qty" id="qty-{{idRow}}" name="qty[]" min="1" />
        </td>
        <td data-title="MISURA (in metri)">
            <input type="number" class="form-control mts-comp" id="mq-x-01-{{idRow}}" name="mq_lavorati_x[]"
                   value="" step=".01" min="0.01" />
            X
            <input type="number" class="form-control mts-comp" id="mq-y-02-{{idRow}}" name="mq_lavorati_y[]"
                   value="" step=".01" min="0.01" />
            X
            <input type="number" class="form-control mts-comp" id="mq-z-03-{{idRow}}" name="mq_lavorati_z[]"
                   value="" step=".01" min="0.01" />
        </td>
        <td data-title="TOTALE">
            <input type="text" class="form-control row-total" id="mq-tot{{idRow}}" name="mq_lavorati_tot[]" data-uniqueid="{{idRow}}" readonly />
        </td>
        <td data-title="AGGIUNGI">
            <div class="btn btn-danger remove-mq-row">
                <i class="fa fa-minus-circle"></i>
            </div>
        </td>
    </tr>
</script>