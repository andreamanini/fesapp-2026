@extends('layouts.fes-app')

@section('content')
    <div class="row utenti-dipendenti">
        <div class="col-lg-12">
            <!-- Bootstrap Register -->
            <div class="block block-themed">
                <div class="block-header fes-gr-colr">
                    <h3 class="block-title font-w700">Lista Attrezzature</h3>
                </div>
                <div class="block-content">
                    <table class="table table-bordered table-striped table-vcenter">
                        <thead>
                        <tr>
                            <th>Numero</th>
                            <th>Nome attrezzatura</th>
                            <th>Descrizione</th>
                            <th class="text-center" style="width: 15%;">Azioni</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($tools as $tool)
                            @component('backend.components.tool-table-row', [
                                'tool' => $tool
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
        'recordName' => 'attrezzatura'
    ])@endcomponent

@endsection