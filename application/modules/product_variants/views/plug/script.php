<script src="<?= base_url("views/skin/backend/plugins/jQueryUI/jquery-ui.js") ?>"></script>


<script>


    $('#grp-variants-container').sortable({
        start: function(e, ui) {

        },
        stop: function() {
            reload_variants_data()
        }
    });


    $('#grp-variants-container .group tbody').sortable({
        start: function(e, ui) {
            $(this).addClass('dashed-border');
        },
        stop: function() {
            reload_variants_data();
            $(this).removeClass('dashed-border');
        }
    });



    $('.product-variants .create-new-grp-variant').on('click',function () {
        $("#modal-create-group").modal('show');
        return false;
    });

    $('.pv-options-selector').select2();




    $('#modal-create-group #create').on('click',function () {

        let selector = $(this);

        $.ajax({
            url: "<?=  site_url("ajax/product_variants/createGroup")?>",
            data: {
                "product_id": <?=$id?>,
                "label": $('#modal-create-group #label').val(),
                "option_type": $('#modal-create-group #option_type').val(),
            },
            dataType: 'json',
            type: 'POST',
            beforeSend: function (xhr) {
                NSTemplateUIAnimation.button.loading = selector;
            },
            error: function (request, status, error) {
                console.log(request);
                NSTemplateUIAnimation.button.default = selector;
            },
            success: function (data, textStatus, jqXHR) {

                if (data.success === 1) {

                    $("#modal-create-group").modal('hide');
                    $('#grp-variants-container').append(data.result);

                    reload_variants_data();

                    NSTemplateUIAnimation.button.default = selector;

                    $('#modal-create-group #label').val('');

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

        return false;
    });


    $('body').delegate('.product-variants .add-option','click',function(){

            $("#modal-create-option").modal('show');

            let id = parseInt($(this).attr('data-id'));
            $('#modal-create-option #create').attr('data-id',id);

        return false;
    });



    $('#modal-create-option #create').on('click',function () {

        let variant_id = parseInt($(this).attr('data-id'));
        let selector = $(this);

        $.ajax({
            url: "<?=  site_url("ajax/product_variants/createOption")?>",
            data: {
                "product_id": <?=$id?>,
                "option_name": $('#modal-create-option #option_name').val(),
                "option_price": $('#modal-create-option #option_price').val(),
                "variant_id": variant_id,
            },
            dataType: 'json',
            type: 'POST',
            beforeSend: function (xhr) {
                NSTemplateUIAnimation.button.loading = selector;
            },
            error: function (request, status, error) {
                console.log(request);
                NSTemplateUIAnimation.button.default = selector;
            },
            success: function (data, textStatus, jqXHR) {

                console.log(data);

                if (data.success === 1) {

                    $("#modal-create-option").modal('hide');
                    $('#grp-variants-container .group-'+variant_id+" tbody").append(data.result);

                    reload_variants_data();

                    NSTemplateUIAnimation.button.default = selector;

                    $('#modal-create-option #option_name').val('');
                    $('#modal-create-option #option_price').val('');

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

        return false;
    });


    $('body').delegate('.product-variants .remove-grp','click',function(){

        let id = parseInt($(this).attr('data-id'));

        $("#fmodal-default").modal("show");
        $("#fmodal-default #apply_confirm").attr("data-id",id).attr("data-type","grp");

        return false;
    });

    $('body').delegate('.product-variants .remove-opt','click',function(){

        let id = parseInt($(this).attr('data-id'));

        $("#fmodal-default").modal("show");
        $("#fmodal-default #apply_confirm").attr("data-id",id).attr("data-type","opt");



        return false;
    });




    $('body').delegate("#fmodal-default #apply_confirm","click",function () {

        let selector = $(this);
        let id = parseInt($(this).attr('data-id'));
        let type = $(this).attr('data-type');



        $.ajax({
            url:"<?=site_url("product_variants/ajax/removeVariant")?>",
            data: {
                "variant_id":id
            },
            dataType: 'json',
            type: 'POST',
            beforeSend: function (xhr) {

                NSTemplateUIAnimation.button.loading = selector;

            }, error: function (request, status, error) {

                NSTemplateUIAnimation.button.default = selector;

                console.log(request);

            },
            success: function (data, textStatus, jqXHR) {

                console.log(data);

                NSTemplateUIAnimation.button.default = selector;


                if(data.success === 1)
                if(type === "grp"){

                    $("#fmodal-default").modal("hide");
                    $("#fmodal-default #apply_confirm").attr("data-id",0).attr("data-type","");
                    $('.product-variants .group-'+id).attr('removed-id',id).hide();


                }else if(type === "opt"){

                    $("#fmodal-default").modal("hide");
                    $("#fmodal-default #apply_confirm").attr("data-id",0).attr("data-type","");
                    $('.product-variants tr.opt-'+id).attr('removed-id',id).hide();

                }

            }
        });

        return false;
    });




   function reload_variants_data() {

       let variants_data = [];
       let order = 0;

       /*
       * Start getting group orders
        */
       $( ".variants-list .group" ).each(function( index ) {

               let grp_id = $(this).attr('data-id');

               variants_data.push({
                   'variant_id': grp_id,
                   'order': order,
                   'parent_id': 0,
               });

           /*
          * Start getting option orders
           */


               $( ".variants-list .group-"+grp_id+" tbody .opt" ).each(function( index ) {

                   let opt_id = $(this).attr('data-id');

                   order++;

                   variants_data.push({
                       'variant_id': opt_id,
                       'order': order,
                       'parent_id': grp_id,
                   });




               }).promise().done(function () {

                   console.log("opt finished");
                   console.log(variants_data);
               });


           order++;


       }).promise().done(function () {

           console.log("grp finished");
           console.log(variants_data);

           upload_new_orders_list(variants_data);
       });

   }
   
   function upload_new_orders_list(list) {

       $.ajax({
           url:"<?=site_url("product_variants/ajax/re_order_list")?>",
           data: {
               "product_id":<?=$id?>,
               "list":list
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

   }

</script>