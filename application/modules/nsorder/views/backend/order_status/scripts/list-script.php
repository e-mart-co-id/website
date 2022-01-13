<script src="<?= base_url("views/skin/backend/plugins/jQueryUI/jquery-ui.js") ?>"></script>
<script>


    $( "#list" ).sortable({
        start: function(e, ui) {

        },
        stop: function() {
            update_order();
        }
    });

    function update_order(){

        var orders = [];

        $( "#list .line").each(function( index ) {

            let selector_id = $(this).attr("data-id");

            orders.push(selector_id);

        }).promise().done(function () {

            $.ajax({
                url:"<?=site_url("nsorder/ajax/order_status_re_order")?>",
                data: {
                    "re_orders":orders
                },
                dataType: 'json',
                type: 'POST',
                beforeSend: function (xhr) {

                }, error: function (request, status, error) {

                    console.log(request);

                },
                success: function (data, textStatus, jqXHR) {

                   console.log(data);

                }
            });

        });


    }

    $(".remove").on('click',function () {

        let id = parseInt($(this).attr('data-id'));

        NSAlertManager.alert.request = function (modal) {
            $.ajax({
                url:"<?=site_url("nsorder/ajax/order_status_remove")?>",
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
                        document.location.href="<?=admin_url("nsorder/order_status")?>";
                    });

                }
            });
        };

        return false;
    });



</script>