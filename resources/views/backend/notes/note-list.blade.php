@extends('layouts.fes-app')

@section('content')
    <div class="row utenti-dipendenti">
        <div class="col-lg-12">
            <!-- Bootstrap Register -->
            <div class="block block-themed">
                <div class="block-header fes-gr-colr">
                    <h3 class="block-title font-w700">Lista Note</h3>
                </div>
                <div class="block-content">
                    <div class="row">
                        <div class="col-md-7">
                            <p>Stai visualizzando il periodo dal <strong>{{ $dateFrom->format('d-m-Y') }}</strong> al <strong>{{ $dateTo->format('d-m-Y') }}</strong></p>
                        </div>


                        <div class="col-5">
                            <label for="df">Data di inizio</label>
                            <input type="text" id="df" value="{{ $dateFrom->format('d-m-Y') }}" readonly style="max-width:100px;margin-right:10px" />

                            <label for="dt">Data di fine</label>
                            <input type="text" id="dt" value="{{ $dateTo->format('d-m-Y') }}" readonly style="max-width:100px" />
                        </div>
                    </div>

                    <table class="table table-bordered table-striped table-vcenter">
                        <thead>
                        <tr>
                            <th class="text-center"></th>
                            <th>Dipendente</th>
                            <th class="d-none d-sm-table-cell">Cantiere</th>
                            <th class="d-none d-sm-table-cell">Data</th>
                            <th class="d-none d-sm-table-cell"><i class="fa fa-paperclip"></i></th>
                            <th class="text-center" style="width: 100px;">Dettagli</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if($notes->count() > 0)
                            @foreach($notes as $note)
                                @component('backend.components.note-table-row', [
                                    'note' => $note,
                                    'loop' => $loop
                                ])@endcomponent
                            @endforeach
                        @else
                            <tr>
                                <td colspan="6">
                                    Non sono presenti note da mostrare associate al mese di ricerca.
                                </td>
                            </tr>
                        @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

@endsection



@section('footer')

    @component('backend.components.delete-modal', [
        'recordName' => 'record nota'
    ])@endcomponent

    <script>
        $(document).ready(function() {
            var dateFormat = "dd-mm-yy",
                from = $("#df")
                    .datepicker({
                        defaultDate: "+1w",
                        changeMonth: true,
                        numberOfMonths: 2,
                        dateFormat: 'dd-mm-yy'
                    })
                    .on("change", function() {
                        to.datepicker( "option", "minDate", getDate( this ) );
                        location.href = '{{ route('notes_list') }}?date_from='+$("#df").val()+'&date_to='+$('#dt').val();
                    }),
                to = $("#dt").datepicker({
                    defaultDate: "+1w",
                    changeMonth: true,
                    numberOfMonths: 2,
                    dateFormat: 'dd-mm-yy'
                })
                    .on("change", function() {
                        from.datepicker( "option", "maxDate", getDate( this ) );
                        location.href = '{{ route('notes_list') }}?date_from='+$("#df").val()+'&date_to='+$('#dt').val();
                    });

            function getDate( element ) {
                var date;
                try {
                    date = $.datepicker.parseDate( dateFormat, element.value );
                } catch( error ) {
                    date = null;
                }

                return date;
            }
        });
    </script>
@endsection