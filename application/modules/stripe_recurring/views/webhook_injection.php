<script>

    $('.tab-content #stripe_config .row').append('<div class="col-sm-6 margin">' +
        '<div class="form-group">\n' +
        '<label><?=_lang("Endpoint secret")?></label>\n' +
        '<input type="text" class="form-control" placeholder="Enter ..." id="STRIPE_ENDPOINT_SECRET" value="<?=ConfigManager::getValue("STRIPE_ENDPOINT_SECRET")?>">\n' +
        '</div>' +
        '<div class="form-group">\n' +
        '<label><?=_lang("Webhook Url")?></label>\n' +
        '<input type="text" class="form-control" placeholder="Enter ..." value="<?=site_url("stripe_recurring/webhook?id=".ConfigManager::getValue("STRIPE_WEBHOOK_ID"))?>" disabled>\n' +
        '</div>' +
        '</div>');


    $('#stripe_config #btnSave').on('click',function () {

        let dataSet = {
            "STRIPE_ENDPOINT_SECRET": $('#stripe_config #STRIPE_ENDPOINT_SECRET').val(),
        };

        $.ajax({
            url: "<?=  site_url("ajax/setting/saveAppConfig")?>",
            data: dataSet,
            dataType: 'json',
            type: 'POST',
            beforeSend: function (xhr) {
            }, error: function (request, status, error) {
                alert(request.responseText);
                console.log(request);
            },
            success: function (data, textStatus, jqXHR) {

                console.log(data);

                selector.attr("disabled", false);
                if (data.success === 1) {

                } else if (data.success === 0) {

                }
            }
        });



    });

</script>