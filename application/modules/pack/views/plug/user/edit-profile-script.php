<script>


    $('#typeAuth').select2();
    $('#confirm_pack').select2();

    $('#select_pack').select2();
    var pack_id = 0;
    $('#select_pack').on('select2:select', function (e) {


        var data = e.params.data;
        pack_id = data.id;

        $('#modal-default-pack').modal('show');

        $("#_select").on('click', function () {

            var selector = $(this);
            $.ajax({
                type: 'post',
                url: "<?=site_url("pack/ajax/changeOwnerPack")?>",
                data: {
                    'pack_id': data.id,
                    'pack_duration': $("#confirm_pack").val(),
                    'user_id': "<?=$user->id_user?>"
                },
                dataType: 'json',
                beforeSend: function (xhr) {
                    selector.attr("disabled", true);
                }, error: function (request, status, error) {
                    NSAlertManager.simple_alert.request = "<?=Translate::sprint("Input invalid")?>";
                    selector.attr("disabled", false);
                    $('#modal-default-pack').modal('hide');
                },
                success: function (data, textStatus, jqXHR) {

                    $('#modal-default-pack').modal('hide');
                    selector.attr("disabled", false);
                    if (data.success === 1) {
                        document.location.reload()
                    } else if (data.success === 0) {
                        var errorMsg = "";
                        for (var key in data.errors) {
                            errorMsg = errorMsg + data.errors[key] + "<br/>";
                        }
                        if (errorMsg !== "") {
                            NSAlertManager.simple_alert.request = errorMsg;
                        }
                    }
                }

            });

            return false;
        });


        return true;

    });



</script>