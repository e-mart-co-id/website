<script>

    $("#cancelSubscription").on('click',function () {


        NSAlertManager.alert.request = function (modal) {
            $.ajax({
                url:"<?=site_url("stripe_recurring/ajax/cancelSubscription")?>",
                data: {
                    "cancel":true
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

                        if(data.success === 1){

                            document.location.reload();

                        }else if(data.success === 0){

                            console.log(data);

                            var errorMsg = "";
                            for (var key in data.errors) {
                                errorMsg = errorMsg + "- "+data.errors[key] + "<br/>";
                            }

                            alert(errorMsg);

                        }

                    });


                }
            });
        };

        return false;
    });


</script>