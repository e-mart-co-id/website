
<?php if ( (!defined("IOS_PURCHASE_ID") && !defined("IOS_API"))  OR  (!defined("ANDROID_PURCHASE_ID") && !defined("ANDROID_API"))): ?>
    <script>


        $("#second_verify").on("click", function () {

            var selector = $(this);
            var pid = $("#SPID").val();

            if (pid !== "") {

                $.ajax({
                    url: "<?=  site_url("ajax/setting/sverify")?>",
                    data: {
                        pid: pid
                    },
                    dataType: 'json',
                    type: 'POST',
                    beforeSend: function (xhr) {
                        selector.attr("disabled", true);
                    }, error: function (request, status, error) {
                        NSAlertManager.simple_alert.request = "<?=Translate::sprint("Input invalid")?>";
                        selector.attr("disabled", false);
                        console.log(request.responseText);
                    },
                    success: function (data, textStatus, jqXHR) {
                        <?php if(ENVIRONMENT == "development"): ?>
                        console.log(data);
                        <?php endif; ?>
                        selector.attr("disabled", false);
                        if (data.success === 1) {
                            document.location.reload();
                        } else if (data.success === 0) {
                            var errorMsg = "";
                            for (var key in data.errors) {
                                errorMsg = errorMsg + data.errors[key] + "<br/>";
                            }
                            if (errorMsg !== "") {
                                NSAlertManager.simple_alert.request = errorMsg;
                            } else if (data.error) {
                                alert(data.error);
                            }
                        }
                    }
                });


            }

            return true;
        });

    </script>
<?php endif; ?>
