<script>


    $('.campaign-block .form-group .select2').select2();


    $('.campaign-block .form-group .colorpicker1').colorpicker();

    $(".content .btnSaveCampaignConfig").on('click', function () {

        var selector = $(this);

        let dataSet = {};

        $( ".campaign-block .form-control" ).each(function( index ) {

            let id = $(this).attr('id');
            dataSet[id] = $(this).val();

        }).promise().done( function(){
            console.log(dataSet);
            saveConfigData(dataSet,selector);
        } );

        return false;
    });


</script>

