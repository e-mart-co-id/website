

<div class="box-body product-block">
    <div class="row">
        <div class="col-sm-6">

            <div class="form-group">
                <label> <?php echo _lang("Enable auto hidden products"); ?> </label>
                <select id="ENABLE_AUTO_HIDDEN_PRODUCTS" name="ENABLE_AUTO_HIDDEN_PRODUCTS"
                        class="form-control select2 ENABLE_AUTO_HIDDEN_PRODUCTS">
                    <?php
                    if ($config['ENABLE_AUTO_HIDDEN_PRODUCTS']) {
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
                <label> <?php echo Translate::sprint("Show only products starting at the current day", ""); ?> </label>
                <select id="PRODUCTS_IN_DATE" name="PRODUCTS_IN_DATE"
                        class="form-control select2 PRODUCTS_IN_DATE">
                    <?php
                    if ($config['PRODUCTS_IN_DATE']) {
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
                <label> <?php echo _lang("Enable Product"); ?>
                    <span
                            style="color: grey;font-size: 11px;">( <?php echo Translate::sprint("Customer_can_publish_own_store_auto", "Customer can publish own product auto"); ?>
                                            )</span></label>
                <select id="ENABLE_PRODUCT_AUTO" name="ENABLE_PRODUCT_AUTO"
                        class="form-control select2 ENABLE_PRODUCT_AUTO">
                    <?php
                    if ($config['ENABLE_PRODUCT_AUTO']) {
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
        <button type="button" class="btn  btn-primary btnSaveProductConfig"><span
                    class="glyphicon glyphicon-check"></span>&nbsp;<?php echo Translate::sprint("Save"); ?>
        </button>
    </div>
</div>


<?php


$script = $this->load->view('product/setting_viewer/scripts/script', NULL, TRUE);
TemplateManager::addScript($script);

?>


