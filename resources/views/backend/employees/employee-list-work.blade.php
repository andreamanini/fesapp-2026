@extends('layouts.fes-app')

@section('content')
    <div class="row utenti-dipendenti">
        <div class="col-lg-12">
            <!-- Bootstrap Register -->
            <div class="block block-themed">
                <div class="block-header fes-gr-colr">
                    <h3 class="block-title font-w700">Cantieri per {{ $employee->name }} {{ $employee->surname }}</h3>
                </div>
                <div class="block-content">
                    <div class="block">

                        <div class="row">
                            <div class="col-md-6">
                                <p>Stai visualizzando tutti i cantieri associati a {{ $employee->name }}
                                    {{ $employee->surname }}.</p>
                            </div>
                        </div>

                        <div class="block-content block-content-full">
                            <table class="table table-bordered table-striped table-vcenter js-dataTable-full">
                                <thead>
                                    <tr>
                                        <th class="text-center"></th>
                                        <th>Cantiere</th>
                                        <th>Cliente</th>
                                        <th class="d-none d-sm-table-cell">Indirizzo</th>
                                        <th class="d-none d-sm-table-cell" style="width: 15%;">Tipologia</th>
                                        <th class="text-center" style="width: 15%;">Azioni</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if ($buildingSites->count() > 0)
                                        @foreach ($buildingSites as $bs)
                                            <tr>
                                                <td class="text-center">{{ $loop->iteration }}</td>
                                                <td>{{ $bs->site_name }}</td>
                                                <td>{{ $bs->company_name ?? 'Cliente non disponibile' }}</td>
                                                <td>
                                                    @php
                                                        $address = json_decode($bs->address, true);
                                                        $formattedAddress = '';
                                                        if (!empty($address['route'])) {
                                                            $formattedAddress .= $address['route'];
                                                        }
                                                        if (!empty($address['street_number'])) {
                                                            $formattedAddress .= ' ' . $address['street_number'];
                                                        }
                                                        if (!empty($address['locality'])) {
                                                            $formattedAddress .=
                                                                (!empty($formattedAddress) ? ', ' : '') .
                                                                $address['locality'];
                                                        }
                                                        if (!empty($address['postal_code'])) {
                                                            $formattedAddress .=
                                                                (!empty($formattedAddress) ? ' ' : '') .
                                                                $address['postal_code'];
                                                        }
                                                    @endphp
                                                    {{ $formattedAddress ?: 'Indirizzo non disponibile' }}
                                                </td>
                                                <td>
                                                    @if (isset($bs->site_type))
                                                        @foreach (json_decode($bs->site_type) as $type)
                                                            <span class="badge badge-info">{{ $type }}</span>
                                                        @endforeach
                                                    @else
                                                        <span class="badge badge-secondary">Tipologia non disponibile</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    <a href="{{ route('building-sites.show', $bs->id) }}"
                                                        class="btn btn-sm btn-primary">
                                                        <i class="fa fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('building-sites.edit', $bs->id) }}"
                                                        class="btn btn-sm btn-warning">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                    {{-- Rimosso il bottone di eliminazione --}}
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="6" class="text-center">Nessun cantiere assegnato a questo
                                                dipendente.</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection

@section('footer')
    @component('backend.components.delete-modal', [
        'recordName' => 'cantiere',
    ])
    @endcomponent
@endsection
