<?php if (GroupAccess::isGranted('product', DELETE_PRODUCT)): ?>

    <script>


        $("div .remove").on('click', function () {

            let id = parseInt($(this).attr('data-id'));

            NSAlertManager.alert.request = function (modal) {
                $.ajax({
                    url: "<?=site_url("ajax/product/delete")?>",
                    data: {
                        "id": id
                    },
                    dataType: 'json',
                    type: 'POST',
                    beforeSend: function (xhr) {
                        modal("beforeSend", xhr);
                    }, error: function (request, status, error) {
                        modal("error", request);
                        console.log(request);
                    },
                    success: function (data, textStatus, jqXHR) {

                        modal("success", data, function (success) {
                            document.location.reload();
                        });

                    }
                });
            };

            return false;
        });


    </script>
<?php endif; ?>