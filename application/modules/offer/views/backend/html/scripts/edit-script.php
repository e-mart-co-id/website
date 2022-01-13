<!-- page script -->
<script src="<?= base_url("views/skin/backend/plugins/datepicker/bootstrap-datepicker.js") ?>"></script>
<script src="<?= base_url("views/skin/backend/plugins/select2/select2.full.min.js") ?>"></script>
<script>


    $("#open-oml").on("click",function () {
        $("#modal-order-multi-language").modal("show");
        $(".order-button").val($("#custom-button-text").val());
    });

    let is_deal = 0;

    if($('#make_as_deal').is(":checked")){

        is_deal = 1;
        $('.deal-data #date_b').attr("disabled",false);
        $('.deal-data #date_e').attr("disabled",false);

    }else {

        is_deal = 0;
        $('.deal-data #date_b').attr("disabled",true);
        $('.deal-data #date_e').attr("disabled",true);

    }

    $("#make_as_deal").on('change',function () {

        if($(this).is(":checked")){
            is_deal = 1;
        }else {
            is_deal = 0;
        }

        if(is_deal===1){

            $('.deal-data #date_b').attr("disabled",false);
            $('.deal-data #date_e').attr("disabled",false);

        }else {

            $('.deal-data #date_b').attr("disabled",true);
            $('.deal-data #date_e').attr("disabled",true);
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

        var selector = $(this);
        var description = $("#form #editable-textarea").val();
        var percent = parseFloat($("#form #percent").val());
        var date_b = $("#form #date_b").val();
        var date_e = $("#form #date_e").val();
        var name = $("#form #name").val();
        var store_id = $("#form #selectStore").val();

        var dataSet0 = {

            "offer_id": <?=$offer['id_product']?>,
            "token": "<?=$token?>",
            "store_id": store_id,
            "images": <?=$uploader_variable?>,
            "name": name,
            "description": description,
            "percent": percent,
            "date_start": date_b,
            "date_end": date_e,
            "is_deal": is_deal,
            "products": $("#select_products").val(),
        };

        send_data(dataSet0,selector);

        return false;

    });

    function send_data(dataSet0,selector){

        $.ajax({
            url: "<?=  site_url("ajax/offer/edit")?>",
            data: dataSet0,
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
                    document.location.href = "<?=admin_url("offer/my_offers")?>";
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

    $('#selectStore').val(<?=$offer['store_id']?>).trigger('change');
    $('#selectStore').select2().on('change',function () {
        $("#select_products").val("").trigger('change')
    });


    $('#select_products').select2({
        tags: true,
        ajax: {
            url: '<?=site_url("product/ajax/getProductsAjax")?>',
            dataType: 'json',
            delay: 250,
            type: 'GET',
            data: function (params) {

                console.log(params);

                var query = {
                    search: params.term,
                    store_id: $("#form #selectStore").val(),
                    page: params.page || 1
                };

                console.log("query");
                console.log(query);

                // Query parameters will be ?search=[term]&page=[page]
                return query;
            },
            processResults: function (data, params) {
                // parse the results into the format expected by Select2
                // since we are using custom formatting functions we do not need to
                // alter the remote JSON data, except to indicate that infinite
                // scrolling can be used
                console.log("result");
                console.log(data);

                //results_list = data;

                params.page = params.page || 1;

                return {
                    results: data.results
                }

            },
            cache: true
        }
    });


</script>


<?php if (GroupAccess::isGranted('offer',MANAGE_OFFERS)): ?>
    <script>

        $("#featured_item1").change(function () {

            var featured = 0;

            if (this.checked)
                featured = 1;
            else
                featured = 0;

            //   alert(featured);

            $.ajax({
                url: "<?=  site_url("ajax/offer/markAsFeatured")?>",
                data: {
                    "id": "<?=$offer['id_product']?>",
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
                url: "<?=  site_url("ajax/offer/markAsFeatured")?>",
                data: {
                    "id": "<?=$offer['id_product']?>",
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


