<script src="<?= base_url("views/skin/backend/plugins/colorpicker/bootstrap-colorpicker.js") ?>"></script>

<script>


    $('.colorpicker').colorpicker();


    $("#save").on('click',function () {

        send_data($(this));

        return false;
    });



    function send_data(selector) {


        $.ajax({
            url: "<?=  site_url("ajax/nsorder/order_status_edit")?>",
            data: {
                "label":$("#label").val(),
                "color":$("#color").val(),
                "id":<?=$status['id']?>,
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
                    NSTemplateUIAnimation.button.success = selector;
                    document.location.href = "<?=admin_url("nsorder/order_status")?>";
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



</script>