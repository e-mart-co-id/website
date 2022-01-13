<script src="<?= base_url("views/skin/backend/plugins/select2/select2.full.min.js") ?>"></script>

<script>


    $("#payouts .deletePayout").on('click',function () {

        let id = parseInt($(this).attr('data-id'));

        NSAlertManager.alert.request = function (modal) {
            $.ajax({
                url:"<?=site_url("nsorder/ajax/delete_payout")?>",
                data: {
                    "id":id
                },
                dataType: 'json',
                type: 'POST',
                beforeSend: function (xhr) {

                    modal("beforeSend",xhr);

                }, error: function (request, status, error) {

                    modal("error",request);
                    console.log(request);

                },
                success: function (data, textStatus, jqXHR) {

                    modal("success",data,function (success) {
                        document.location.href="<?=admin_url("delivery/payouts")?>";
                    });

                }
            });
        };

        return false;
    });

</script>


