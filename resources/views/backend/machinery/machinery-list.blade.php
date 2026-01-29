@extends('layouts.fes-app')

@section('content')
    <div class="row utenti-dipendenti">
        <div class="col-lg-12">
            <!-- Bootstrap Register -->
            <div class="block block-themed">
                <div class="block-header fes-gr-colr">
                    <h3 class="block-title font-w700">Lista Macchinari</h3>
                </div>
                <div class="block-content">
                    <table class="table table-bordered table-striped table-vcenter">
                        <thead>
                        <tr>
                            <th>Numero Mezzo</th>
                            <th>Nome Mezzo</th>
                            <th>Descrizione</th>
                            <th class="text-center" style="width: 15%;">Azioni</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($machinery as $machine)
                            @component('backend.components.machine-table-row', [
                                'machine' => $machine
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
        'recordName' => 'macchinario'
    ])@endcomponent

@endsection