<script src="<?= base_url("views/skin/backend/plugins/select2/select2.full.min.js") ?>"></script>
<script>

    var owner_id = parseInt($("#select_owner").val());

    $('#select_owner').on('change', function () {
        var id = $("#form #id").val();

        console.log(owner_id+' '+id);


        $.ajax({
            url: "<?=  site_url("ajax/store/changeOwnership")?>",
            data: {
                'id': id,
                "owner_id": owner_id,
            },
            dataType: 'json',
            type: 'POST',
            beforeSend: function (xhr) {
            }, error: function (request, status, error) {
                console.log(request.responseText);
            },
            success: function (data, textStatus, jqXHR) {
                console.log(data);

                if (data.success === 1) {
                    var sucessMsg = "<?=Translate::sprint("Successfully assigned")?>"
                    if (sucessMsg !== "") {
                        NSAlertManager.simple_alert.request = sucessMsg;
                    }
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

    let order_enabled = 1;
    let order_based_on_op = 0;

    $("input[name='order-system']").on('click',function () {
        if($(this).is(":checked")){
            $('.order-options').removeClass('hidden');
            order_enabled = 1;
        }else{
            $('.order-options').addClass('hidden');
            order_enabled = 0;
            order_based_on_op = 0;
        }
    });

    $("input[name='order-b-op']").on('click',function () {
        if($(this).is(':checked')){
            order_based_on_op = 1;
        }else{
            order_based_on_op = 0;
        }
    });


    order_enabled = <?=intval($store['config_order_enabled'])?>;
    order_based_on_op = <?=intval($store['config_order_based_op'])?>;



    $("#btnUpdate").on('click', function () {

        var selector = $(this);

        var id = $("#form #id").val();
        var name = $("#form #name").val();
        var detail = $("#editable-textarea").val();
        var tel = $("#form #tel").val();
        var cat = $("#form #cat").val();
        var website = $("#form #web").val();
        var address = $("#form #<?=$location_fields_id['address']?>").val();
        var lat = $("#form #<?=$location_fields_id['lat']?>").val();
        var lng = $("#form #<?=$location_fields_id['lng']?>").val();
        var canChat = $("input[name='canChat']:checked").val();
        var book = $("input[name='book']:checked").val();

        console.log(<?=$uploader_variable?>);

        var dataSet = {

            'id': id,
            "name": name,
            "address": address,
            "detail": detail,
            "website": website,
            "tel": tel,
            "cat": cat,
            "lat": lat,
            "lng": lng,
            "canChat": canChat,
            "owner_id": owner_id,
            "order_enabled": order_enabled,
            "order_based_on_op": order_based_on_op,
            "images": JSON.stringify(<?=$uploader_variable?>),
            <?php if(ModulesChecker::isRegistred("gallery") and isset($gallery_variable)){ ?>
            "gallery": JSON.stringify(<?=$gallery_variable?>)
            <?php } ?>
        }


        if ('undefined' !== typeof times) {
            dataSet.times = times;
        }


        $.ajax({
            url: "<?=  site_url("ajax/store/edit")?>",
            data: dataSet,
            dataType: 'json',
            type: 'POST',
            beforeSend: function (xhr) {

                NSTemplateUIAnimation.button.loading = selector;

            }, error: function (request, status, error) {
                alert(request.responseText);

                NSTemplateUIAnimation.button.default = selector;

                console.log(request.responseText);
            },
            success: function (data, textStatus, jqXHR) {

                if (data.success === 1) {
                    NSTemplateUIAnimation.button.success = selector;
                    document.location.href = "<?=admin_url("store/my_stores")?>";
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


    //Drop down list of business owners
    $('#select_owner').select2({

        ajax: {
            url: "<?=site_url("ajax/user/getOwners")?>",
            dataType: "json",
            data: function (params) {

                var query = {
                    q: params.term,
                };

                // Query parameters will be ?search=[term]&type=public
                return query;
            },
            processResults: function (data) {
                // Tranforms the top-level key of the response object from 'items' to 'results'
                console.log(data);
                return {
                    results: data
                };
            },
            results: function (data, page) {
                console.log(data);

                return {results: data};
            }
        }
    });

    $('.selectCat').select2();
</script>
<?php if (GroupAccess::isGranted('store', MANAGE_STORES)): ?>
    <script>


        $("#featured_item1").change(function () {

            var featured = 0;

            if (this.checked)
                featured = 1;
            else
                featured = 0;

            //   alert(featured);

            $.ajax({
                url: "<?=  site_url("ajax/store/markAsFeatured")?>",
                data: {
                    "id": "<?=$store['id_store']?>",
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
                            errorMsg = errorMsg + data.errors[key] + "\n";
                        }
                        if (errorMsg !== "") {
                            alert(errorMsg);
                        }
                    }
                }
            });
            return true;
        });


        $("#featured_item0").change(function () {

            var featured = 0;


            $.ajax({
                url: "<?=  site_url("ajax/store/markAsFeatured")?>",
                data: {
                    "id": "<?=$store['id_store']?>",
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
                            errorMsg = errorMsg + data.errors[key] + "\n";
                        }
                        if (errorMsg !== "") {
                            alert(errorMsg);
                        }
                    }
                }
            });
            return true;
        });

    </script>
<?php endif; ?>
