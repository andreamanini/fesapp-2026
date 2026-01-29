<div class="modal fade" id="delete-modal" data-backdrop="static" data-keyboard="false" tabindex="-1"
     aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Sei sicuro di voler eliminare questo {{ $recordName ?? 'record'}}?</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                @csrf
                <p>Questa operazione non può essere annullata, tutti i dati relativi e connessi a questo {{ $recordName ?? 'record'}} verranno eliminati.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Annulla</button>
                <button type="button" id="do-delete"
                        data-url=""
                        class="btn btn-danger">Elimina</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        var deleteBtn = null;

        // Opens the modal
        $('.delete-row-btn').click(function () {
            deleteBtn = $(this);

            if ($(this).data('url')) {
                $('#do-delete').attr('data-url', $(this).data('url'));
                $('#delete-modal').modal('show');
            }
        });

        // If the user confirms the deletion process...
        $('#do-delete').click(function () {
            if ($(this).data('url')) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $.ajax({
                    type: 'DELETE',
                    url: $(this).data('url'),
                    success: function () {
                        $('#delete-modal').modal('hide');

                        if (deleteBtn.data('top-element')) {
                            deleteBtn.closest(''+deleteBtn.data('top-element')).hide('slow');
                        } else {
                            deleteBtn.closest('tr').hide('slow');
                        }
                    },
                    error: function(e, response, a) {
                        $('#delete-modal').modal('hide');

                        if (e.responseJSON && e.responseJSON.response && e.responseJSON.response.message) {
                            alert(e.responseJSON.response.message);
                        }
                    }
                });
            }
            window.location.href = '/redirect-to-building-sites';//forzo ricaricamento pagina
        });
    });
</script>