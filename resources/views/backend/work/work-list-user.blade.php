@extends('layouts.fes-app')

@section('content')
    <div class="row utenti-dipendenti">
        <div class="col-lg-12">
            <!-- Bootstrap Register -->
            <div class="block block-themed">
                <div class="block-header fes-gr-colr">
                    <h3 class="block-title font-w700">Programma Lavori Dipendenti</h3>
                </div>
                <div class="block-content">
                    <div>
    <h2>
    {{ $employee->name }} {{ $employee->surname }}
    </h2>
    <div class="form-group row">
        <div class="col-2">
            <label for="date">Data di inizio lavoro</label>
            <input type="text" class="form-control" id="date"
                   name="{{ $employee->table_user_id }}_date"
                   value="@if ($employee->date != '') {{ \Carbon\Carbon::parse($employee->date)->format('d-m-Y') }}@endif"
                   placeholder="Inserisci la data..." readonly>
        </div>
        <div class="col-2">
            <label for="time">Orario di inizio lavoro</label>
            <input type="text" class="js-masked-time form-control" id="time"
                   name="{{ $employee->table_user_id }}_time"
                   value="@if ($employee->time != '') {{ \Carbon\Carbon::parse($employee->time)->format('H:i') }}@endif"
                   placeholder="00:00" readonly>
        </div>
        <div class="col-2">
            <label for="truck_no">N. Camion</label>
            <input type="text" class="form-control" id="truck_no"
                   name="{{ $employee->table_user_id }}_truck_no"
                   value="{{ $employee->truck_no }}"
                   placeholder="Inserisci il numero del camion..." readonly>
        </div>
    </div>
    <div class="form-group row">
        
        <div class="col-6">
            <label for="time">Cantiere</label>
            <select type="text" class="form-control" id="{{ $employee->table_user_id }}_building_site_id" name="{{ $employee->table_user_id}}_building_site_id" readonly>
            @foreach($buildingSites as $buildingSite)
                @if($buildingSite->id == $employee->building_site_id)
                <option>{{$buildingSite->site_name}}</option>
                @endif>
            @endforeach
            </select>
        </div>
    </div>
    <div class="form-group row">
        <div class="col-12">
            <label for="work_description">Descrizione del lavoro</label>
            <textarea name="{{ $employee->table_user_id }}_work_description" class="form-control" readonly>{{ $employee->work_description }}</textarea>
        </div>
    </div>
</div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('footer')

@endsection