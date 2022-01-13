<script>

    $('.product-block .form-group .select2').select2();
    $('.product-block .form-group .colorpicker1').colorpicker();

    $(".content .btnSaveProductConfig").on('click', function () {

        var selector = $(this);

        let dataSet = {};

        $( ".product-block .form-control" ).each(function( index ) {

            let id = $(this).attr('id');
            dataSet[id] = $(this).val();

        }).promise().done( function(){
            console.log(dataSet);
            saveConfigData(dataSet,selector);
        } );

        return false;
    });
    

</script>

