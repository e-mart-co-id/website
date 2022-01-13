<div class="box-body event-block">
    <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
                <label> <?php echo Translate::sprint("Enable auto hidden events", "Enable auto hidden events"); ?> </label>
                <select id="ENABLE_AUTO_HIDDEN_EVENTS" name="ENABLE_AUTO_HIDDEN_EVENTS"
                        class="form-control select2 ENABLE_AUTO_HIDDEN_EVENTS">
                    <?php
                    if ($config['ENABLE_AUTO_HIDDEN_EVENTS']) {
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
                <label> <?php echo Translate::sprint("Enable Event", "Enable event"); ?>
                    <span
                            style="color: grey;font-size: 11px;">( <?php echo Translate::sprint("Customer_can_publish_own_event_auto", "Customer can publish own event auto"); ?>
                                            )</span></label>
                <select id="ENABLE_EVENT_AUTO" name="ENABLE_EVENT_AUTO"
                        class="form-control select2 ENABLE_EVENT_AUTO">
                    <?php
                    if ($config['ENABLE_EVENT_AUTO']) {
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
    </div>
</div>


<div class="box-footer">
    <div class="pull-right">
        <button type="button" class="btn  btn-primary btnSaveEventConfig"><span
                    class="glyphicon glyphicon-check"></span>&nbsp;<?php echo Translate::sprint("Save"); ?>
        </button>
    </div>
</div>


<?php

$script = $this->load->view('event/setting_viewer/scripts/script', NULL, TRUE);
TemplateManager::addScript($script);

?>
