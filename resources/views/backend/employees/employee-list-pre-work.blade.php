@extends('layouts.fes-app')

@section('content')
<div class="row utenti-dipendenti">
    <div class="col-lg-12">
        <!-- Bootstrap Register -->
        <div class="block block-themed">
            <div class="block-header fes-gr-colr">
                <h3 class="block-title font-w700">Lista Dipendenti</h3>
            </div>
            <div class="block-content">
                <table class="table table-bordered table-striped table-vcenter">
                    <thead>
                    <tr>
                        <th class="text-center"></th>
                        <th><i class="si si-user"></i></th>
                        <th class="d-none d-sm-table-cell">Telefono</th>
                        <th class="d-none d-sm-table-cell">Email</th>
                        <th class="text-center" style="width: 100px;">Cantieri</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($employees as $employee)
                        @component('backend.components.employee-table-row-pre-work', [
                            'employee' => $employee,
                            'loop' => $loop
                        ])@endcomponent
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection

@section('footer')

    @component('backend.components.delete-modal', [
        'recordName' => 'utente'
    ])@endcomponent

@endsection
