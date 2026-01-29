@php
    if (!empty($struttV)) {
        $struttura = $struttV;
    } else if (!empty($struttS)) {
        $struttura = $struttS;
    } else if (!empty($struttLavaggio)) {
        $struttura = $struttLavaggio;
    } else if (!empty($struttSoffiatura)) {
        $struttura = $struttSoffiatura;
    } else if (!empty($struttverniciataAnticorrosiva)) {
        $struttura = $struttverniciataAnticorrosiva;
    } else if (!empty($struttVerniciataCarrozzeria)) {
        $struttura = $struttVerniciataCarrozzeria;
    } else if (!empty($struttVerniciataImpregnante)) {
        $struttura = $struttVerniciataImpregnante;
    } else if (!empty($struttVerniciataIntumescente)) {
        $struttura = $struttVerniciataIntumescente;
    } else if (!empty($struttIntonaciIntumescenti)) {
        $struttura = $struttIntonaciIntumescenti;
    }  else if (!empty($struttAltro)) {
        $struttura = $struttAltro;
    } else {
        $struttura = $struttIntonaco;
    }
@endphp

<tr>
    <td data-title="TIPO LAVORO">
        <input type="hidden"  value="{{ $idRow }}" name="id_row[]" />
        <select id="work-type-{{ $idRow }}" name="work_type[]" class="form-control">
            <option value="" @if(null == $workType){{ 'selected' }}@endif>Seleziona tipologia</option>
            <option value="V" @if('V' == $workType){{ 'selected' }}@endif>Verniciatura</option>
            <option value="S" @if('S' == $workType){{ 'selected' }}@endif>Sabbiatura</option>
            <option value="L" @if('L' == $workType){{ 'selected' }}@endif>Lavaggio</option>
            <option value="SOFF" @if('SOFF' == $workType){{ 'selected' }}@endif>Soffiatura</option>
            <option value="I" @if('I' == $workType){{ 'selected' }}@endif>Intonaco</option>
            <option value="II" @if('II' == $workType){{ 'selected' }}@endif>Intonaci intumescenti</option>
            <option value="VIM" @if('VIM' == $workType){{ 'selected' }}@endif>Verniciatura impregnante</option>
            <option value="VIN" @if('VIN' == $workType){{ 'selected' }}@endif>Verniciatura intumescente</option>
            <option value="VA" @if('VA' == $workType){{ 'selected' }}@endif>Verniciatura anticorrosiva</option>
            <option value="VC" @if('VC' == $workType){{ 'selected' }}@endif>Verniciatura da carrozzeria</option>
            <option value="I" @if('I' == $workType){{ 'selected' }}@endif>Intonaco</option>
            <option value="ALTRO" @if('ALTRO' == $workType){{ 'selected' }}@endif>Altro</option>
            
        </select>
    </td>
    <td data-title="STRUTTURA">
        <input type="text"
               class="form-control strutt-type strutt-type-{{ $idRow }}"
               id="struttura{{ $idRow }}"
               data-uniqueid="{{ $idRow }}"
               name="struttura[]"
               value="{{ $struttura }}"
               maxlength="255"
               placeholder="Compila il dettaglio della struttura" />
    </td>
    <td data-title="MATERIALE">
        <input type="text" class="form-control" id="materiale-{{ $idRow }}" name="materiale[]"
               value="@isset($material){{ $material }}@endisset" />
    </td>
    <td data-title="QUANTITÀ">
        <input type="number" class="form-control row-qty" id="qty-{{ $idRow }}" name="qty[]"
               value="@isset($qty){{ $qty }}@endisset" min="1" />
    </td>
    <td data-title="MISURA (in metri)">
        <input type="number" class="form-control mts-comp" id="mq-x-01-{{ $idRow }}" name="mq_lavorati_x[]"
               value="@isset($mqX){{ $mqX }}@endisset" step=".01" min="0.01" />
        X
        <input type="number" class="form-control mts-comp" id="mq-y-02-{{ $idRow }}" name="mq_lavorati_y[]"
               value="@isset($mqY){{ $mqY }}@endisset" step=".01" min="0.01" />
        X
        <input type="number" class="form-control mts-comp" id="mq-z-03-{{ $idRow }}" name="mq_lavorati_z[]"
               value="@isset($mqZ){{ $mqZ }}@endisset" step=".01" min="0.01" />
    </td>
    <td data-title="TOTALE">
        <input type="text" class="form-control row-total" id="mq-tot{{ $idRow }}" name="mq_lavorati_tot[]"
               data-uniqueid="{{ $idRow }}"
               value="@isset($mqTot){{ $mqTot }}@endisset"
               readonly />
    </td>
    <td data-title="AGGIUNGI">
        @if(empty($showRemoveBtn))
        <div class="btn btn-success add-tables">
            <i class="fa fa-plus-circle"></i>
        </div>
        @else
        <div class="btn btn-danger remove-mq-row">
            <i class="fa fa-minus-circle"></i>
        </div>
        @endif
    </td>
</tr>