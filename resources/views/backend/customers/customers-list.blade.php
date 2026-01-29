@extends('layouts.fes-app')

@section('content')
    <div class="row utenti-dipendenti">
        <div class="col-lg-12">
            <!-- Bootstrap Register -->
            <div class="block block-themed">
                <div class="block-header fes-gr-colr">
                    <h3 class="block-title font-w700">Lista Clienti</h3>
                </div>
                <div class="block-content">
                    <!-- Form di ricerca -->
                    <form method="GET" action="{{ route('customers.index') }}" class="mb-4">
                        <div class="form-group row">
                            <label for="search" class="col-sm-2 col-form-label">Cerca per Ragione Sociale</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="Inserisci Ragione Sociale">
                            </div>
                            <div class="col-sm-2">
                                <button type="submit" class="btn btn-primary">Cerca</button>
                            </div>
                        </div>
                    </form>

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

                    <div class="form-group row">
                        <div class="col-12 pull-right">
                            <nav aria-label="Page navigation" class=" pull-right">
                                <ul class="pagination">
                                    @for($p=1; $p<=$pagination->pages; $p++)
                                        <li class="page-item @if($p == $pagination->currentPage){{ 'active' }}@endif">
                                            @if($p == $pagination->currentPage)
                                                <span class="page-link">{{ $p }}</span>
                                            @else
                                                <a class="page-link" href="{{ route('customers.index', ['page' => $p, 'search' => request('search')]) }}">{{ $p }}</a>
                                            @endif
                                        </li>
                                    @endfor
                                </ul>
                            </nav>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection

@section('footer')
    @component('backend.components.delete-modal', [
        'recordName' => 'cliente'
    ])@endcomponent
@endsection
