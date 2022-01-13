<script>

    $('.event-block .form-group .select2').select2();
    $('.event-block .form-group .colorpicker1').colorpicker();

    $(".content .btnSaveEventConfig").on('click', function () {

        var selector = $(this);

        let dataSet = {};

        $( ".event-block .form-control" ).each(function( index ) {

            let id = $(this).attr('id');
            dataSet[id] = $(this).val();

        }).promise().done( function(){
            console.log(dataSet);
            saveConfigData(dataSet,selector);
        } );

        return false;
    });
    

</script>

