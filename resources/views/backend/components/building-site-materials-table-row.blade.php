<div class="input-group material-list">
    <input type="text" class="form-control mat-name"
           value="@isset($materialName){{ $materialName }}@endif"
           name="materials[]"
           placeholder="Inserisci materiale" />

    <div class="input-group-append">
        <input type="text" class="form-control mat-qty"
               value="@isset($materialQty){{ $materialQty }}@endif"
               placeholder="Quantità"
               name="material_qty[]"  style="max-width: 200px !important;" />

        <select name="base[]" class="form-control" style="max-width: 150px !important;">
            <option value="" @if(!isset($materialBase)){{ 'selected' }}@endif>Base</option>
            <option value="acquosa" @if(isset($materialBase) and 'acquosa' == $materialBase){{ 'selected' }}@endif>Acquosa</option>
            <option value="solvente" @if(isset($materialBase) and 'solvente' == $materialBase){{ 'selected' }}@endif>Solvente</option>
        </select>
    </div>
    <div class="input-group-append">
        <span class="input-group-text remove-materials">
            <i class="fa fa-minus-circle"></i>
        </span>
    </div>
</div>