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
                    <strong>Seleziona la data per tutti i dipendenti</strong>
                    <input type="text" class="form-control col-2" id="date"
                   name="all_users"
                   value=""
                   placeholder="Inserisci la data..." required>
                </div>
                <form method="POST" action="{{ route('work_store') }}">
                @csrf 
                <div class="block-content">
                    @foreach($employees as $employee)
                        @component('backend.work.work-list-row', [
                            'employee' => $employee,
                            'buildingSites' => $buildingSites
                        ])@endcomponent
                    @endforeach
                    
                    <div class="block">
                    </div>

                    <div class="form-group row">
                        <div class="col-6 btn-right">
                            <a href="{{ route('dashboard') }}" class="btn btn-alt-secondary">
                                <i class="fa fa-remove mr-5"></i> Annulla
                            </a>

                            <button type="submit" class="btn btn-success fes-btn-w">
                                <i class="fa fa-save mr-5"></i> Salva
                            </button>
                        </div>
                    </div>
                    
                </div>
                </form>
                
            </div>
        </div>
    </div>
@endsection

@section('footer')
    <script src="{{ asset('backend/js/plugins/masked-inputs/jquery.maskedinput.min.js') }}"></script>
    <script>
    
    jQuery(function(){ Codebase.helpers(['masked-inputs']); });
    
    $(document).ready(function() {
        $("input[name*=_date]").datepicker({
            minDate: 0,
            defaultDate: "+1d",
            dateFormat: 'dd-mm-yy'
        });
        $("input[name=all_users]").datepicker({
            minDate: 0,
            defaultDate: "+1d",
            dateFormat: 'dd-mm-yy',
            onSelect: function (date, datepicker) {
                if (date != "") {
                    $("input[name*=_date]").datepicker("setDate", date );
                }
            }
        });
    });


    </script>
@endsection