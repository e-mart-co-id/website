<script src="<?=  base_url("views/skin/backend/plugins/select2/select2.full.min.js")?>"></script>
<script>

    $('.commission select.select2').select2();

    $(".commission .btnSave").on('click',function () {

        let selector = $(this);

        $.ajax({
            type: 'post',
            url: "<?=  site_url("ajax/nsorder/saveCommissionConfig")?>",
            dataType: 'json',
            data:{
                'ORDER_COMMISSION_ENABLED': $('.commission #ORDER_COMMISSION_ENABLED').val(),
                'ORDER_COMMISSION_VALUE': $('.commission #ORDER_COMMISSION_VALUE').val()
            },
            beforeSend: function (xhr) {

                NSTemplateUIAnimation.button.loading = selector;

            }, error: function (request, status, error) {
                NSAlertManager.simple_alert.request = "<?=Translate::sprint("Input invalid")?>";
                console.log(request);

                NSTemplateUIAnimation.button.default = selector;
            },
            success: function (data, textStatus, jqXHR) {

                NSTemplateUIAnimation.button.success = selector;

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


</script>