<script>


    $("#DELIVERY_FEES_TYPE").on('change',function () {

        let value = $(this).val();

        if(value === "disabled"){
            $('#DELIVERY_FEES_VALUE').attr("disabled",true);
        }else{
            $('#DELIVERY_FEES_VALUE').attr("disabled",false);
        }

    });


    $(".commission .btnSave").on('click',function () {

        let selector = $(this);

        $.ajax({
            type: 'post',
            url: "<?=  site_url("ajax/delivery/saveDFeesConfig")?>",
            dataType: 'json',
            data:{
                'DELIVERY_FEES_TYPE': $('.commission #DELIVERY_FEES_TYPE').val(),
                'DELIVERY_FEES_VALUE': $('.commission #DELIVERY_FEES_VALUE').val()
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

    $(".delivery-banner .btnSave").on('click',function () {

        let selector = $(this);

        $.ajax({
            type: 'post',
            url: "<?=  site_url("ajax/delivery/saveDBannerConfig")?>",
            dataType: 'json',
            data:{
                'DELIVERY_IOS_LINK': $('.delivery-banner #DELIVERY_IOS_LINK').val(),
                'DELIVERY_ANDROID_LINK': $('.delivery-banner #DELIVERY_ANDROID_LINK').val()
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