<script src="<?= base_url("views/skin/backend/plugins/select2/select2.full.min.js") ?>"></script>

<script>


    let order_enabled = 1;
    let order_based_on_op = 0;

    $("input[name='order-system']").on('click',function () {
        if($(this).is(":checked")){
            $('.order-options').removeClass('hidden');
            order_enabled = 1;
        }else{
            $('.order-options').addClass('hidden');
            order_enabled = 0;
        }
    });

    $("input[name='order-b-op']").on('click',function () {
        if($(this).is(':checked')){
            order_based_on_op = 1;
        }else{
            order_based_on_op = 0;
        }
    });


    $("#btnCreate").on('click', function () {

        let selector = $(this);

        var name = $("#form #name").val();
        var detail = $("#editable-textarea").val();
        var tel = $("#form #tel").val();
        var cat = $("#form #cat").val();
        var website = $("#form #web").val();
        var address = $("#form #<?=$location_fields_id['address']?>").val();
        var lat = $("#form #<?=$location_fields_id['lat']?>").val();
        var lng = $("#form #<?=$location_fields_id['lng']?>").val();
        var canChat = $("input[name='canChat']:checked").val();

        $.ajax({
            url: "<?=  site_url("ajax/store/createStore")?>",
            data: {
                "times": times,
                "name": name,
                "address": address,
                "detail": detail,
                "tel": tel,
                "website": website,
                "cat": cat,
                "lat": lat,
                "lng": lng,
                "canChat": canChat,
                "order_enabled": order_enabled,
                "order_based_on_op": order_based_on_op,
                "images": JSON.stringify(<?=$uploader_variable?>),
                <?php if(ModulesChecker::isRegistred("gallery")){ ?>
                "gallery": JSON.stringify(<?=$gallery_variable?>)
                <?php } ?>
            },
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

</script>
<script>


    $('.selectCat').select2();


</script>


