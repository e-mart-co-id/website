<div class="box-body campaign-block">
    <div class="row">

        <div class="col-sm-6">

            <div class="form-group">
                <label><?php echo Translate::sprint("Target_raduis", "Target raduis"); ?>
                    <sup>KM</sup></label>
                <input type="number" min="0" max="100" class="form-control"
                       placeholder="<?= Translate::sprint("Enter") ?> ..." name="RADUIS_TRAGET"
                       id="RADUIS_TRAGET" value="<?= ConfigManager::getValue('RADUIS_TRAGET') ?>">
            </div>

            <div class="form-group">
                <label> <?= Translate::sprint("Number max pushes per campaign", ""); ?> </label>
                <input type="number" min="0" max="1000" class="form-control"
                       placeholder="<?= Translate::sprint("Enter") ?> ..."
                       name="LIMIT_PUSHED_GUESTS_PER_CAMPAIGN" id="LIMIT_PUSHED_GUESTS_PER_CAMPAIGN"
                       value="<?= ConfigManager::getValue('LIMIT_PUSHED_GUESTS_PER_CAMPAIGN') ?>">
            </div>

            <div class="form-group">
                <label>

                    <?php

                    echo Translate::sprint("Use_campaign_with_crontab", "Use campaign with crontab"); ?>
                    <span style="color: grey;font-size: 11px;">(Ex: <?php echo Translate::sprint("you_can_push_10_campaigns_for_every_10_minutes", "you can push 10 campaigns for every 10 minutes"); ?>
                                            )</span>

                </label>


                <select id="PUSH_CAMPAIGNS_WITH_CRON" name="PUSH_CAMPAIGNS_WITH_CRON"
                        class="form-control select2 PUSH_CAMPAIGNS_WITH_CRON">
                    <?php

                    if (ConfigManager::getValue('PUSH_CAMPAIGNS_WITH_CRON')) {
                        echo '<option value="true" selected>true</option>';
                        echo '<option value="false" >false</option>';
                    } else {
                        echo '<option value="true"  >true</option>';
                        echo '<option value="false"  selected>false</option>';
                    }

                    ?>
                </select>

                <BR>
                <label>
                                       <span style="color: grey;font-size: 11px;">
                                  <?php
                                  echo Translate::sprint("set this command in your cronjob") . " <BR><CODE> /usr/bin/php -q " . FCPATH . "cronjob.php</CODE>";
                                  echo '<br><i><u><a target="_blank" href="https://www.youtube.com/watch?v=EW5KRkeFBvE">Tutorial Video</a></u></i>'
                                  ?>
                                      </span>
                </label>
            </div>

            <div class="form-group">
                <label><?php echo Translate::sprint("Number_max_pushes_per_campaign_with_cronjob_in_every_execute", "Maximum number of pushes  per campaign with crontab in each run"); ?> </label>
                <input type="number" min="0" max="100" class="form-control"
                       placeholder="<?= Translate::sprint("Enter") ?> ..."
                       name="NBR_PUSHS_FOR_EVERY_TIME" id="NBR_PUSHS_FOR_EVERY_TIME"
                       value="<?= ConfigManager::getValue('NBR_PUSHS_FOR_EVERY_TIME')?>">
            </div>

            <div class="form-group">
                <label>
                    <?= Translate::sprint("Notification agreement")?><br>
                    <span style="color: grey;font-size: 11px;"><?=_lang("By using this option, will be able to ask users if they want receive notification")?></span>
                </label>

                <select id="_NOTIFICATION_AGREEMENT_USE" name="PUSH_CAMPAIGNS_WITH_CRON"
                        class="form-control select2 _NOTIFICATION_AGREEMENT_USE">
                    <?php

                    if (ConfigManager::getValue('_NOTIFICATION_AGREEMENT_USE')) {
                        echo '<option value="true" selected>true</option>';
                        echo '<option value="false" >false</option>';
                    } else {
                        echo '<option value="true"  >true</option>';
                        echo '<option value="false"  selected>false</option>';
                    }

                    ?>
                </select>
            </div>


        </div>

        <div class="col-sm-6">
            <div class="form-group">
                <label><?php echo Translate::sprint("Firebase key"); ?> (FCM)
                    <sup>*</sup></label>
                <textarea type="text" class="form-control"
                          placeholder="<?= Translate::sprint("Enter") ?> ..." name="FCM_KEY"
                          id="FCM_KEY"><?= ConfigManager::getValue('FCM_KEY') ?></textarea>
            </div>
        </div>

    </div>
</div>

<div class="box-footer">
    <div class="pull-right">
        <button type="button" class="btn  btn-primary btnSaveCampaignConfig"><span
                    class="glyphicon glyphicon-check"></span>&nbsp;<?php echo Translate::sprint("Save"); ?>
        </button>
    </div>
</div>


<?php


$script = $this->load->view('campaign/setting_viewer/scripts/script', NULL, TRUE);
TemplateManager::addScript($script);

?>
