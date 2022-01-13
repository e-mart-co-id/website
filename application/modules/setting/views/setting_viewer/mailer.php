<?php

$smtp_enabled = ConfigManager::getValue('SMTP_SERVER_ENABLED');

?>

<div class="box-body mailer-block">
    <div class="row">

        <div class="col-sm-6">

            <div class="form-group">
                <label><?php echo Translate::sprint("Enable SMTP SERVER"); ?> </label>
                <select id="SMTP_SERVER_ENABLED" name="SMTP_SERVER_ENABLED"
                        class="form-control select2 SMTP_SERVER_ENABLED">
                    <?php
                    if (ConfigManager::getValue('SMTP_SERVER_ENABLED')) {
                        echo '<option value="true" selected>true</option>';
                        echo '<option value="false" >false</option>';
                    } else {
                        echo '<option value="true"  >true</option>';
                        echo '<option value="false"  selected>false</option>';
                    }
                    ?>
                </select>
            </div>

            <div class="form-group">
                <label><?php echo Translate::sprint("SMTP Protocol"); ?></label>
                <input type="text" class="form-control"
                       placeholder="<?= Translate::sprint("Enter") ?> ..."
                       name="SMTP_PROTOCOL"
                       id="SMTP_PROTOCOL" value="<?= ConfigManager::getValue('SMTP_PROTOCOL') ?>" <?=$smtp_enabled==FALSE?"disabled":""?>>
            </div>

            <div class="form-group">
                <label><?php echo Translate::sprint("SMTP Host"); ?></label>
                <input type="text" class="form-control"
                       placeholder="<?= Translate::sprint("Enter") ?> ..." name="SMTP_HOST"
                       id="SMTP_HOST" value="<?= ConfigManager::getValue('SMTP_HOST') ?>" <?=$smtp_enabled==FALSE?"disabled":""?>>
            </div>

            <div class="form-group">
                <label><?php echo Translate::sprint("SMTP Port"); ?></label>
                <input type="text" class="form-control"
                       placeholder="<?= Translate::sprint("Enter") ?> ..." name="SMTP_PORT"
                       id="SMTP_PORT" value="<?= ConfigManager::getValue('SMTP_PORT') ?>" <?=$smtp_enabled==FALSE?"disabled":""?>>
            </div>

            <div class="form-group">
                <label><?php echo Translate::sprint("SMTP user"); ?></label>
                <input type="text" class="form-control"
                       placeholder="<?= Translate::sprint("Enter") ?> ..." name="SMTP_USER"
                       id="SMTP_USER" value="<?= ConfigManager::getValue('SMTP_USER') ?>" <?=$smtp_enabled==FALSE?"disabled":""?>>
            </div>

            <div class="form-group">
                <label><?php echo Translate::sprint("SMTP pass"); ?></label>
                <input type="password" class="form-control"
                       placeholder="<?= Translate::sprint("Enter") ?> ..." name="SMTP_PASS"
                       id="SMTP_PASS" value="<?= ConfigManager::getValue('SMTP_PASS') ?>" <?=$smtp_enabled==FALSE?"disabled":""?>>
            </div>


        </div>


        <div class="col-sm-6">


        </div>
    </div>
</div>


<div class="box-footer">
    <div class="pull-right">
        <button type="button" class="btn  btn-primary btnSaveMailerConfig"><span
                    class="glyphicon glyphicon-check"></span>&nbsp;<?php echo Translate::sprint("Save", "Save"); ?>
        </button>
    </div>
</div>


<?php


$script = $this->load->view('setting/setting_viewer/scripts/mailer-script', NULL, TRUE);
TemplateManager::addScript($script);

?>




