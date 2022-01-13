<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <!-- Message Error -->
            <div class="col-sm-12">
                <?php $this->load->view("backend/include/messages"); ?>
            </div>
        </div>

        <div class="row" id="form">
            <div class="col-sm-12">
                <div class="box box-solid">
                    <div class="box-header">

                        <div class="box-title">
                            <b><?= Translate::sprint("New payout") ?></b>
                        </div>

                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="row">
                            <div class="col-sm-6">
                                <!-- Businss owner  dropdown list -->
                                <div class="form-group">
                                    <label><?= Translate::sprint("Select owner") ?></label>
                                    <select id="select_owner" name="select_owner" class="form-control select2">
                                        <option selected="" value="0"><?= Translate::sprint("Select") ?></option>
                                    </select>
                                </div>

                                <!-- Payment Method -->
                                <div class="form-group">
                                    <label><?= Translate::sprint("Payment method") ?></label>
                                    <select id="method" class="form-control select2 method"
                                            style="width: 100%;">
                                        <option selected="selected" value="Bank"><?= Translate::sprint("Bank") ?></option>
                                        <option  value="Cash"><?= Translate::sprint("Cash") ?></option>
                                    </select>
                                </div>


                                <!-- Amount -->
                                <div class="form-group">
                                    <label><?= Translate::sprint("Amount") ?></label>
                                    <input type="text" class="form-control" name="amount" id="amount"
                                           placeholder="Ex: 8000.00$">
                                </div>

                            </div>
                            <div class="col-sm-6">
                                <!-- Note -->
                                <div class="form-group">
                                    <label><?= Translate::sprint("Note", "") ?></label>
                                    <textarea class="form-control" rows="7" id="editable-textarea"
                                              placeholder="<?= Translate::sprint("Enter") ?> ..."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="box-footer">
                        <button type="button" class="btn  btn-primary pull-right" id="btnCreate"><span
                                    class="glyphicon glyphicon-check"></span>
                            <?= Translate::sprint("Save Payout") ?>
                        </button>
                    </div>
                </div>
            </div>

        </div>
        <!-- /.row -->
    </section>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -- -->


<?php

$script = $this->load->view('delivery/backend/payouts/scripts/add-payout-script', NULL, TRUE);
TemplateManager::addScript($script);

?>