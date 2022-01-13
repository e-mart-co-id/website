<script src="<?= base_url("views/skin/backend/plugins/select2/select2.full.min.js") ?>"></script>

<script>

    let enabled_payments = [];


    $( ".payment_methods .payment_method").on('click',function () {

        enabled_payments = [];

        $( ".payment_methods .payment_method").each(function( index ) {

            if($(this).is(":checked")){
                enabled_payments.push($(this).val());
            }

        }).promise().done( function(){
            enabled_payments.push(0);
            console.log(enabled_payments);
        } );

    });



    $('.PAYMENT_CURRENCY').select2();
    $('.PAYPAL_CONFIG_DEV_MODE').select2();
    $('.STRIPE_CONFIG_DEV_MODE').select2();
    $('.RAZORPAY_CONFIG_DEV_MODE').select2();

    $(".content #btnSave").on('click', function () {

        var selector = $(this);

        var dataSet = {

            "METHOD_PAYMENTS_ENABLED_LIST": enabled_payments,
            "PAYPAL_CONFIG_CLIENT_ID": $("#PAYPAL_CONFIG_CLIENT_ID").val(),
            "PAYPAL_CONFIG_SECRET_ID": $("#PAYPAL_CONFIG_SECRET_ID").val(),
            "PAYPAL_CONFIG_DEV_MODE": $("#PAYPAL_CONFIG_DEV_MODE").val(),

            "STRIPE_PUBLISHABLE_KEY": $("#STRIPE_PUBLISHABLE_KEY").val(),
            "STRIPE_SECRET_KEY": $("#STRIPE_SECRET_KEY").val(),
            "STRIPE_CONFIG_DEV_MODE": $("#STRIPE_CONFIG_DEV_MODE").val(),

            "RAZORPAY_CONFIG_DEV_MODE": $("#RAZORPAY_CONFIG_DEV_MODE").val(),
            "RAZORPAY_KEY_ID": $("#RAZORPAY_KEY_ID").val(),
            "RAZORPAY_SECRET_KEY": $("#RAZORPAY_SECRET_KEY").val(),

            "FLUTTERWAVE_CONFIG_DEV_MODE": $("#FLUTTERWAVE_CONFIG_DEV_MODE").val(),
            "FLUTTERWAVE_KEY_ID": $("#FLUTTERWAVE_KEY_ID").val(),
            "FLUTTERWAVE_SECRET_KEY": $("#FLUTTERWAVE_SECRET_KEY").val(),

            "PAYMENT_CURRENCY": $("#PAYMENT_CURRENCY").val(),
            "token": ""
        };

        $.ajax({
            url: "<?=  site_url("ajax/setting/saveAppConfig")?>",
            data: dataSet,
            dataType: 'json',
            type: 'POST',
            beforeSend: function (xhr) {
                NSTemplateUIAnimation.button.loading = selector;
            }, error: function (request, status, error) {
                alert(request.responseText);
                NSTemplateUIAnimation.button.default = selector;
                console.log(request);
            },
            success: function (data, textStatus, jqXHR) {

                console.log(data);

                selector.attr("disabled", false);
                if (data.success === 1) {

                    NSTemplateUIAnimation.button.success = selector;

                    document.location.reload();
                } else if (data.success === 0) {

                    NSTemplateUIAnimation.button.default = selector;

                    var errorMsg = "";
                    for (var key in data.errors) {
                        errorMsg = errorMsg + data.errors[key] + "\n";
                    }
                    if (errorMsg !== "") {
                        alert(errorMsg);
                    }
                }
            }
        });


        return false;
    });




</script>
