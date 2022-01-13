<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">

    <section class="content commission">

        <div class="row">
            <!-- Message Error -->
            <div class="col-sm-12">
                <?php $this->load->view("backend/include/messages"); ?>
            </div>

        </div>

        <div class="row">
            <div class="col-sm-12">

                <div class="box box-solid ">
                    <div class="box-header with-border">
                        <h3 class="box-title"><b><?= Translate::sprint("Manage delivery fees") ?></b></h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">

                        <div class="row">
                            <div class="col-sm-8">

                                <div class="callout callout-info">
                                    <p>
                                        <i class="mdi mdi-information-outline"></i>&nbsp;&nbsp;<?= Translate::sprint("Shipping costs can be as follows") ?>
                                        : </p>
                                    <ul>
                                        <li><?= Translate::sprint("Fixed price: the delivery price mentioned only once, it will be visible on all orders") ?></li>
                                        <li><?= Translate::sprint("Commission: the delivery price is calculated from the percentage of the order") ?></li>
                                    </ul>


                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label><?php echo Translate::sprint("Fees type"); ?></label>
                                    <select id="DELIVERY_FEES_TYPE" name="DELIVERY_FEES_TYPE"
                                            class="form-control select2 DELIVERY_FEES_ENABLED">
                                        <option value="disabled" <?= ConfigManager::getValue('DELIVERY_FEES_TYPE') == "disabled" ? "selected" : "" ?>><?= _lang("Disabled") ?></option>
                                        <option value="fixed" <?= ConfigManager::getValue('DELIVERY_FEES_TYPE') == "fixed" ? "selected" : "" ?>><?= _lang("Fixed price") ?></option>
                                        <option value="commission" <?= ConfigManager::getValue('DELIVERY_FEES_TYPE') == "commission" ? "selected" : "" ?>><?= _lang("Commission by percent") ?></option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label><?php echo Translate::sprint("Value"); ?></label>&nbsp;&nbsp;<sub>E.g 10,
                                        20...</sub>
                                    <input type="number" min="0" max="100" class="form-control"
                                           placeholder="<?= Translate::sprint("Enter percent") ?> ..."
                                           name="DELIVERY_FEES_VALUE"
                                           id="DELIVERY_FEES_VALUE"
                                           value="<?= ConfigManager::getValue('DELIVERY_FEES_VALUE') ?>" <?= ConfigManager::getValue('DELIVERY_FEES_TYPE') == "disabled" ? "disabled" : "" ?>>
                                </div>
                            </div>
                        </div>

                    </div>
                    <!-- /.box-body -->
                    <div class="box-footer">
                        <button type="button" class="btn  btn-primary btnSave"><span
                                    class="glyphicon glyphicon-check"></span><?php echo Translate::sprint("Save", "Save"); ?>
                        </button>
                    </div>
                </div>

                <div class="box box-solid delivery-banner">
                    <div class="box-header with-border">
                        <h3 class="box-title"><b><?= Translate::sprint("Delivery Banner") ?></b></h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">

                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label><?php echo Translate::sprint("Delivery AppStore link"); ?></label>
                                    <input type="text" class="form-control"
                                           placeholder="<?= Translate::sprint("Enter link") ?> ..."
                                           name="DELIVERY_IOS_LINK"
                                           id="DELIVERY_IOS_LINK"
                                           value="<?= ConfigManager::getValue('DELIVERY_IOS_LINK') ?>">
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label><?php echo Translate::sprint("Delivery PlayStore link"); ?></label>
                                    <input type="text" class="form-control"
                                           placeholder="<?= Translate::sprint("Enter link") ?> ..."
                                           name="DELIVERY_ANDROID_LINK"
                                           id="DELIVERY_ANDROID_LINK"
                                           value="<?= ConfigManager::getValue('DELIVERY_ANDROID_LINK') ?>">
                                </div>
                            </div>
                        </div>

                    </div>
                    <!-- /.box-body -->
                    <div class="box-footer">
                        <button type="button" class="btn  btn-primary btnSave"><span
                                    class="glyphicon glyphicon-check"></span><?php echo Translate::sprint("Save", "Save"); ?>
                        </button>
                    </div>
                </div>

            </div>
    </section>

</div>


<?php

$script = $this->load->view('delivery/backend/delivery-script', NULL, TRUE);
TemplateManager::addScript($script);

?>




