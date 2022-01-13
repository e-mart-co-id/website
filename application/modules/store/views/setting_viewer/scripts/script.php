<script>

    $('.store-block .form-group .select2').select2();
    $('.store-block .form-group .colorpicker1').colorpicker();

    $(".content .btnSaveStoreConfig").on('click', function () {

        var selector = $(this);

        let dataSet = {};


        dataSet.MAP_DEFAULT_LATITUDE = $(".store-block  #<?=$location_fields_id['lat']?>").val();
        dataSet.MAP_DEFAULT_LONGITUDE = $(".store-block  #<?=$location_fields_id['lng']?>").val();

        $( ".store-block .form-control" ).each(function( index ) {

            let id = $(this).attr('id');
            dataSet[id] = $(this).val();

        }).promise().done( function(){
            console.log(dataSet);
            saveConfigData(dataSet,selector);
        } );

        return false;
    });
    

</script>

