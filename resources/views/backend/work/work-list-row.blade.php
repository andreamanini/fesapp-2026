<div>
    <h2><a href="javascript:void(0);" onclick="$(this).parent().parent().remove();" class="btn btn-danger"><i class="fa fa-remove mr-5"></i> Rimuovi</a>
    {{ $employee->name }} {{ $employee->surname }}
    </h2>
    <div class="form-group row">
        <div class="col-2">
            <label for="date">Data di inizio lavoro</label>
            <input type="text" class="form-control" id="date"
                   name="{{ $employee->table_user_id }}_date"
                   value="@if ($employee->date != '') {{ \Carbon\Carbon::parse($employee->date)->format('d-m-Y') }}@endif"
                   placeholder="Inserisci la data..." required>
        </div>
        <div class="col-2">
            <label for="time">Orario di inizio lavoro</label>
            <input type="text" class="js-masked-time form-control" id="time"
                   name="{{ $employee->table_user_id }}_time"
                   value="@if ($employee->time != '') {{ \Carbon\Carbon::parse($employee->time)->format('H:i') }}@endif"
                   placeholder="00:00" required>
        </div>
        <div class="col-2">
            <label for="truck_no">N. Camion</label>
            <input type="text" class="form-control" id="truck_no"
                   name="{{ $employee->table_user_id }}_truck_no"
                   value="{{ $employee->truck_no }}"
                   placeholder="Inserisci il numero del camion...">
        </div>
    </div>
    <div class="form-group row">
        
        <div class="col-6">
            <label for="time">Cantiere</label>
            <select type="text" class="form-control" id="{{ $employee->table_user_id }}_building_site_id" name="{{ $employee->table_user_id}}_building_site_id" required>
            <option value="">Seleziona il cantiere...</option>    
            @foreach($buildingSites as $buildingSite)
            <option value="{{ $buildingSite->id }}" @if($buildingSite->id == $employee->building_site_id){{ 'selected' }}@endif>{{ $buildingSite->site_name }}</option>
            @endforeach
            </select>
        </div>
    </div>
    <div class="form-group row">
        <div class="col-12">
            <label for="work_description">Descrizione del lavoro</label>
            <textarea name="{{ $employee->table_user_id }}_work_description" class="form-control">{{ $employee->work_description }}</textarea>
        </div>
    </div>
    <input type="hidden" name="users_id[]" value="{{ $employee->table_user_id }}" />
</div>
<hr />