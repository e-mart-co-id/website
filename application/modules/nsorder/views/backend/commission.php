

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
                        <h3 class="box-title"><b><?= Translate::sprint("Manage Commission") ?></b></h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="row">
                            <div class="col-sm-6">

                                <div class="form-group">
                                    <label><?php echo Translate::sprint("Commission enabled"); ?></label>
                                    <select id="ORDER_COMMISSION_ENABLED" name="ORDER_COMMISSION_ENABLED"
                                            class="form-control select2 ORDER_COMMISSION_ENABLED">
                                        <?php

                                        if (ConfigManager::getValue('ORDER_COMMISSION_ENABLED')==TRUE) {
                                            echo '<option value="true" selected>Enabled</option>';
                                            echo '<option value="false" >Disabled</option>';
                                        } else {
                                            echo '<option value="true"  >Enabled</option>';
                                            echo '<option value="false"  selected>Disabled</option>';
                                        }

                                        ?>
                                    </select>
                                </div>

                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label><?php echo Translate::sprint("Commission value"); ?></label>&nbsp;&nbsp;<sub>E.g 10, 20...</sub>
                                    <input type="number" min="0" max="100" class="form-control"
                                           placeholder="<?= Translate::sprint("Enter percent") ?> ..." name="ORDER_COMMISSION_VALUE"
                                           id="ORDER_COMMISSION_VALUE" value="<?= ConfigManager::getValue('ORDER_COMMISSION_VALUE') ?>" <?=ConfigManager::getValue('ORDER_COMMISSION_ENABLED')==FALSE?"disabled":""?>>
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


$script = $this->load->view('nsorder/backend/scripts/commission-scripts', NULL, TRUE);
TemplateManager::addScript($script);

?>




