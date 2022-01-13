<script>


    $('.mailer-block .form-group .select2').select2();

    $(".content .btnSaveMailerConfig").on('click', function () {

        var selector = $(this);

        let dataSet = {};

        $( ".mailer-block .form-control" ).each(function( index ) {

            let id = $(this).attr('id');
            dataSet[id] = $(this).val();

        }).promise().done( function(){
            console.log(dataSet);
            saveConfigData(dataSet,selector);
        } );

        return false;
    });


    $("#SMTP_SERVER_ENABLED").on('change',function () {

        let val = $(this).val();

        if(val === "true"){
            $( ".mailer-block .form-control" ).attr('disabled',false);
        }else{
            $( ".mailer-block .form-control" ).attr('disabled',true);
        }

        $(this).attr('disabled',false);

    });


</script>

