<?php

    $image = "";

    if(isset($user['images'][0]['200_200']['url'])){
        $image = $user['images'][0]['200_200']['url'];
    }else{
        $image= base_url("views/skin/backend/images/profile_placeholder.png");
    }



?>

<!-- Message to the right -->
<div class="direct-chat-msg right">
    <div class="direct-chat-info clearfix">
        <span class="direct-chat-name pull-right"><?=ucfirst($user['name'])?></span>
        <span class="direct-chat-timestamp pull-left">
             <?=MyDateUtils::convert($object['created_at'],"UTC",TimeZoneManager::getTimeZone(),"Y-m-d H:i")?>
        </span>
    </div>


    <div class="image-container-40"  style="background-image: url('<?=$image?>');">
        <img class="direct-chat-img invisible" src="<?=$image?>" alt="Message User Image" >
    </div>

    <span>
        <div class="direct-chat-text">
            <?=Text::echo_output($object['content'])?>
        </div>
    </span>
    <!-- /.direct-chat-text -->
</div>