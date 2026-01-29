@extends('layouts.fes-app')

@section('content')

    <div class="content-heading">
        <div class="dropdown float-right mr-5">
            <button type="button" class="btn btn-primary" data-target="#search-modal" data-toggle="modal">Modifica ricerca</button>
        </div>

        Risultati per la ricerca: {{ $searchWord }}
    </div>

    @if(null !== $buildingSites and null !== $customers and null !== $employees and null !== $machinery)
    <!-- Full Table -->
    <div class="block">
        <div class="block-header block-header-default">
            <h3 class="block-title">Cantieri</h3>
        </div>
        <div class="block-content">
            @if($buildingSites->count() > 0)
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-vcenter">
                    <thead>
                    <tr>
                        <th class="text-center"></th>
                        <th>Cantiere</th>
                        <th>Cliente</th>
                        <th class="d-none d-sm-table-cell">Indirizzo</th>
                        <th class="d-none d-sm-table-cell" style="width: 15%;">Tipologia</th>
                        <th class="text-center" style="width: 15%;">Dipendenti</th>
                        <th class="text-center" style="width: 15%;">Azioni</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($buildingSites as $bs)
                        @component('backend.components.building-site-table-row', [
                            'bs' => $bs,
                            'loop' => $loop
                        ])@endcomponent
                    @endforeach
                    </tbody>
                </table>
            </div>
            @else
                <p class="lead">Non sono disponibili cantieri relativi a questi criteri di ricerca.</p>
            @endif
        </div>
    </div>
    @if(Auth::user()->isAdmin())
    <!-- Full Table -->
    <div class="block">
        <div class="block-header block-header-default">
            <h3 class="block-title">Clienti</h3>
        </div>
        <div class="block-content">
            @if($customers->count() > 0)
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-vcenter">
                    <thead>
                    <tr>
                        <th class="text-center"></th>
                        <th>Ragione Sociale</th>
                        <th>Email</th>
                        <th class="d-none d-sm-table-cell">Telefono</th>
                        <th class="text-center" style="width: 15%;">Azioni</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($customers as $customer)
                        @component('backend.components.customer-table-row', [
                            'customer' => $customer,
                            'loop' => $loop
                        ])@endcomponent
                    @endforeach
                    </tbody>
                </table>
            </div>
            @else
                <p class="lead">Non sono disponibili clienti relativi a questi criteri di ricerca.</p>
            @endif
        </div>
    </div>
    <!-- END Full Table -->

    <!-- Full Table -->
    <div class="block">
        <div class="block-header block-header-default">
            <h3 class="block-title">Dipendenti</h3>
        </div>
        <div class="block-content">
            @if($employees->count() > 0)
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-vcenter">
                    <thead>
                    <tr>
                        <th class="text-center"></th>
                        <th><i class="si si-user"></i></th>
                        <th class="d-none d-sm-table-cell">Telefono</th>
                        <th class="d-none d-sm-table-cell">Email</th>
                        <th class="d-none d-md-table-cell" style="width: 15%;">Ruolo</th>
                        <th class="text-center" style="width: 100px;">Azioni</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($employees as $employee)
                        @component('backend.components.employee-table-row', [
                            'employee' => $employee,
                            'loop' => $loop
                        ])@endcomponent
                    @endforeach
                    </tbody>
                </table>
            </div>
            @else
                <p class="lead">Non sono disponibili dipendenti relativi a questi criteri di ricerca.</p>
            @endif
        </div>
    </div>
    <!-- END Full Table -->

    <!-- Full Table -->
    <div class="block">
        <div class="block-header block-header-default">
            <h3 class="block-title">Macchinari</h3>
        </div>
        <div class="block-content">
            @if($machinery->count() > 0)
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-vcenter">
                    <thead>
                    <tr>
                        <th class="text-center"></th>
                        <th>Nome Mezzo</th>
                        <th>Numero Mezzo</th>
                        <th>Descrizione</th>
                        <th class="text-center" style="width: 15%;">Azioni</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($machinery as $machine)
                        @component('backend.components.machine-table-row', [
                            'machine' => $machine,
                            'loop' => $loop
                        ])@endcomponent
                    @endforeach
                    </tbody>
                </table>
            </div>
            @else
                <p class="lead">Non sono disponibili macchinari relativi a questi criteri di ricerca.</p>
            @endif
        </div>
    </div>
    @endif
    <!-- END Full Table -->
    @endif
@endsection