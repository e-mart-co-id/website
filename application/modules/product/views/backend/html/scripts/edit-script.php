<!-- page script -->
<script src="<?= base_url("views/skin/backend/plugins/datepicker/bootstrap-datepicker.js") ?>"></script>
<script src="<?= base_url("views/skin/backend/plugins/select2/select2.full.min.js") ?>"></script>

<script>


    $('#button_template').select2();
    $('#cf_id.select2').select2();


    $("#open-oml").on("click",function () {
        $("#modal-order-multi-language").modal("show");
        $(".order-button").val($("#custom-button-text").val());
    });


    var stock = -1;


    if($('#stock').is(":checked")){
        stock = 1;
        $('.order-quantity-value').removeClass("hidden");
    }else {
        stock = -1;
        $('.order-quantity-value').addClass("hidden");
    }

    $("#stock").on('change',function () {

        if($(this).is(":checked")){
            stock = 1;
        }else {
            stock = -1;
        }

        if(stock === 1){
            $('.order-quantity-value').removeClass("hidden");
        }else{
            $('.order-quantity-value').addClass("hidden");
        }

        return false;
    });


    $.fn.datepicker.defaults.format = "yyyy-mm-dd";
    $('.datepicker').datepicker({
        startDate: '-3d'
    });

    <?php
    $token = $this->mUserBrowser->setToken("SU74aQ55");
    ?>

    $("#form .btnSave").on('click', function () {

        let selector = $(this);

        var description = $("#form #editable-textarea").val();
        var price = parseFloat($("#form #price").val());
        var name = $("#form #name").val();
        var store_id = $("#form #selectStore").val();
        var qty_value = $("#form .order-quantity-value input[type=number]").val();

        var dataSet0 = {

            "product_id": <?=$product['id_product']?>,

            "token": "<?=$token?>",
            "store_id": store_id,
            "images": <?=$uploader_variable?>,
            "name": name,
            "description": description,
            "price": price,
            "order_cf_id": $("#cf_id").val(),
            "button_template": $("#button_template").val(),
            "stock": stock,
            "qty_value": qty_value,
        };


        let order_button = {};

        $( ".order-button" ).each(function( index ) {

            let lang = $(this).attr("lang-data");
            order_button[lang] = $(this).val();

        }).promise().done(function () {

            order_button["default"] = $("#custom-button-text").val();
            dataSet0[order_button] = order_button;
            send_data(dataSet0,selector);

        });

        return false;

    });

    function send_data(dataSet0,selector){

        $.ajax({
            url: "<?=  site_url("ajax/product/edit")?>",
            data: dataSet0,
            dataType: 'json',
            type: 'POST',
            beforeSend: function (xhr) {

                NSTemplateUIAnimation.button.loading = selector;

            }, error: function (request, status, error) {

                NSAlertManager.simple_alert.request = "<?=Translate::sprint("Input invalid")?>";
                NSTemplateUIAnimation.button.default = selector;
                console.log(request);

            },
            success: function (data, textStatus, jqXHR) {

                console.log(data);

                if (data.success === 1) {
                    NSTemplateUIAnimation.button.success = selector;
                    document.location.href = "<?=admin_url("product/my_products")?>";
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

    $('#selectStore').select2();
    $('#selectStore').val(<?=$product['store_id']?>).trigger('change');


    $("#price").on('keyup',function () {

        let commission  = parseFloat($("#commission").val()) / 100;
        let price  = parseFloat($(this).val());
        let calculated  = (price * commission)+price;

        $("#priceInput").val( price );
        $("#priceCommission").val( calculated );

        return false;
    });

</script>


<?php if (GroupAccess::isGranted('product',MANAGE_PRODUCTS)): ?>

    <script>

        $("#featured_item1").change(function () {

            var featured = 0;

            if (this.checked)
                featured = 1;
            else
                featured = 0;

            //   alert(featured);

            $.ajax({
                url: "<?=  site_url("ajax/product/markAsFeatured")?>",
                data: {
                    "id": "<?=$product['id_product']?>",
                    "featured": featured,
                    "type": "store"
                },
                dataType: 'json',
                type: 'POST',
                beforeSend: function (xhr) {

                },
                error: function (request, status, error) {
                    console.log(request);
                },
                success: function (data, textStatus, jqXHR) {

                    if (data.success === 1) {

                        document.location.reload();

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
            return true;
        });


        $("#featured_item0").change(function () {

            var featured = 0;


            $.ajax({
                url: "<?=  site_url("ajax/product/markAsFeatured")?>",
                data: {
                    "id": "<?=$product['id_product']?>",
                    "featured": featured,
                    "type": "store"
                },
                dataType: 'json',
                type: 'POST',
                beforeSend: function (xhr) {

                },
                error: function (request, status, error) {
                    console.log(request);
                },
                success: function (data, textStatus, jqXHR) {

                    if (data.success === 1) {

                        document.location.reload();

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
            return true;
        });

    </script>

<?php endif; ?>


