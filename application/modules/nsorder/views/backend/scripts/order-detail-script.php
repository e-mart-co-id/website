<script src="<?= base_url("views/skin/backend/plugins/locationpicker/locationpicker.jquery.min.js") ?>"></script>
<script type="text/javascript"
        src='https://maps.googleapis.com/maps/api/js?key=<?= MAPS_API_KEY ?>&libraries=places'></script>
<script>


    $('.loc-detail').on('click', function () {

        let address = $(this).attr('data-address');
        let lat = $(this).attr('data-lat');
        let lng = $(this).attr('data-lng');

        $('#modal-location-detail').modal('show');


        $('#loc-address span').text(address);

        $('#loc-maps').locationpicker({
            location: {latitude: lat, longitude: lng}
        });
        return false;
    });


    $("#edit-status").on('click', function () {

        $('#modal-edit-status').modal('show');

        return false;
    });

    $("#select2-order-status").select2();

    $("#select2-order-status").on('change', function () {
        $('.message_container').removeClass('hidden');
    });

    $("#update-status").on('click', function () {

        updateOrderStatus(this,$("#select2-order-status").val(),$("#c_message").val());

        return false;
    });


    $("#edit-status-confirm-order").on('click', function () {
        updateOrderStatus(this,"2","");
        return false;
    });

    $("#edit-status-unlock-order").on('click', function () {
        updateOrderStatus(this,"1","");
        return false;
    });


    function updateOrderStatus(action, orderstatus, message) {
        let selector = $(action);

        //$("#select2-order-status").val()
        //$("#c_message").val()

        $.ajax({
            url: "<?=  site_url("ajax/nsorder/order_update_status")?>",
            data: {
                'order_id': <?=$order_id?>,
                'order_status': orderstatus,
                'message': message,
            },
            dataType: 'json',
            type: 'POST',
            beforeSend: function (xhr) {

                NSTemplateUIAnimation.button.loading = selector;

            }, error: function (request, status, error) {
                NSAlertManager.simple_alert.request = "<?=Translate::sprint("Input invalid")?>";

                NSTemplateUIAnimation.button.default = selector;
                console.log(request)
            },
            success: function (data, textStatus, jqXHR) {

                console.log(data);

                if (data.success === 1) {

                    $('#modal-edit-status').modal('hide');

                    NSTemplateUIAnimation.button.success = selector;
                    document.location.reload();
                } else if (data.success === 0) {
                    NSTemplateUIAnimation.button.default = selector;
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
    }



    <?php if(ModulesChecker::isEnabled("order_payment") && ModulesChecker::isEnabled("payment")
    && GroupAccess::isGranted("nsorder", MANAGE_ORDER_CONFIG_ADMIN)): ?>
    $("#select2-payment-status").select2();

    $("#edit-payment-status").on('click', function () {
        $("#modal-edit--payment-status").modal('show');
        return false;
    });


    $("#update-payment-status").on('click', function () {

        let selector = $(this);

        $.ajax({
            url: "<?=  site_url("ajax/nsorder/payment_update_status")?>",
            data: {
                'order_id': <?=$order_id?>,
                'payment_status': $("#select2-payment-status").val(),
            },
            dataType: 'json',
            type: 'POST',
            beforeSend: function (xhr) {

                NSTemplateUIAnimation.button.loading = selector;

            }, error: function (request, status, error) {
                NSAlertManager.simple_alert.request = "<?=Translate::sprint("Input invalid")?>";

                NSTemplateUIAnimation.button.default = selector;
                console.log(request)
            },
            success: function (data, textStatus, jqXHR) {


                if (data.success === 1) {

                    $("#modal-edit--payment-status").modal('hide');

                    NSTemplateUIAnimation.button.success = selector;
                    document.location.reload();
                } else if (data.success === 0) {
                    NSTemplateUIAnimation.button.default = selector;
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


    <?php endif; ?>


</script>