<?php

$params = array();

?>

<div class="box-body store-block">
    <div class="row">
        <div class="col-sm-6">

            <div class="form-group">
                <label><?php echo Translate::sprint("Maps api key"); ?> <sup>*</sup></label>
                <input type="text" class="form-control"
                       placeholder="<?= Translate::sprint("Enter") ?> ..."
                       name="MAPS_API_KEY"
                       id="MAPS_API_KEY" value="<?= $config['MAPS_API_KEY'] ?>">
            </div>


            <?php
            $map = LocationManager::plug_pick_location(array(
                'lat'=>ConfigManager::getValue('MAP_DEFAULT_LATITUDE'),
                'lng'=>ConfigManager::getValue('MAP_DEFAULT_LONGITUDE'),
                'address'=>''
            ),array(
                'lat'=>TRUE,
                'lng'=>TRUE,
                'address'=>FALSE
            ));

            echo $map['html'];
            TemplateManager::addScript($map['script']);
            $params['location_fields_id'] = $map['fields_id'];

            ?>



        </div>

        <div class="col-sm-6">


            <div class="form-group">
                <label> <?php echo Translate::sprint("Enable_store"); ?> <span
                            style="color: grey;font-size: 11px;">( <?php echo Translate::sprint("Customer_can_publish_own_store_auto", "Customer can publish own store auto"); ?>
                                            )</span></label>
                <select id="ENABLE_STORE_AUTO" name="ENABLE_STORE_AUTO"
                        class="form-control select2 ENABLE_STORE_AUTO">
                    <?php
                    if ($config['ENABLE_STORE_AUTO']) {
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
        <button type="button" class="btn  btn-primary btnSaveStoreConfig"><span
                    class="glyphicon glyphicon-check"></span>&nbsp;<?php echo Translate::sprint("Save", "Save"); ?>
        </button>
    </div>
</div>


<?php


$script = $this->load->view('store/setting_viewer/scripts/script', $params, TRUE);
TemplateManager::addScript($script);

?>

