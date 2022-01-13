<script src="<?=  base_url("views/skin/backend/plugins/iCheck/icheck.min.js")?>"></script>
<script>

    //iCheck
    $('input[type="checkbox"].minimal, input[type="radio"].minimal').iCheck({
        checkboxClass: 'icheckbox_square-blue',
        radioClass: 'iradio_square',
        increaseArea: '20%' // optional
    });

</script>

<script>

    var opening_time = false;

    $('#opening_time').on('ifChecked', function (event) {
        opening_time = true;
        $("#_h").removeClass('hidden');
    });

    $('#opening_time').on('ifUnchecked', function (event) {
        opening_time = false;
        $("#_h").addClass('hidden');
    });


</script>

<script src="<?= TemplateManager::assets('store',"plugins/timepicker/jquery.timepicker.js")?>"></script>
<script>

    var times = {};

    $('.form-group .date-picker').timepicker({ 'timeFormat': 'h:i A' });

    <?php foreach ($days as $day): ?>

            times.<?=$day?> = {
                opening: "",
                closing: "",
            };

        $('#_checked_d_<?=$day?>').on('change',function () {

            var result = $(this).prop('checked');
            if(result){

                $("#_o_d_<?=$day?>").attr('disabled',false);
                $("#_c_d_<?=$day?>").attr('disabled',false);

            }else{

                $("#_o_d_<?=$day?>").attr('disabled',true);
                $("#_c_d_<?=$day?>").attr('disabled',true);

                $("#_o_d_<?=$day?>").val("");
                $("#_c_d_<?=$day?>").val("");

                times.<?=$day?>.opening = "";
                times.<?=$day?>.closing = "";

            }

        });

        //opening event
        $("#_o_d_<?=$day?>").on('changeTime', function() {

            times.<?=$day?>.opening = $(this).val();

        });

        //closeing event
        $("#_c_d_<?=$day?>").on('changeTime', function() {

            times.<?=$day?>.closing = $(this).val();

        });


    <?php endforeach; ?>



    <?php

        $opening_time_is_enabled = FALSE;

        foreach ($times as $time){

            if($time['enabled']==1){

                $opening_time_is_enabled = TRUE;
                echo "$('#_checked_d_".$time['day']."').attr('checked',true);";

                //enable fields
                echo " $(\"#_o_d_".$time['day']."\").attr('disabled',false);";
                echo "$(\"#_c_d_".$time['day']."\").attr('disabled',false);";

                //put time into the fields
                echo " $(\"#_o_d_".$time['day']."\").val('".$time['opening']."');";
                echo "$(\"#_c_d_".$time['day']."\").val('".$time['closing']."');";

                echo "  times.".$time['day'].".opening = '".$time['opening']."';";
                echo "  times.".$time['day'].".closing = '".$time['closing']."';";

            }else{

                //disable fields
                echo " $(\"#_o_d_".$time['day']."\").attr('disabled',true);";
                echo "$(\"#_c_d_".$time['day']."\").attr('disabled',true);";

            }

        }

        echo '$(\'.form-group .date-picker\').timepicker({ \'timeFormat\': \'h:i A\' });';

        if($opening_time_is_enabled)
            echo "$('#opening_time').iCheck('check')";


    ?>

</script>