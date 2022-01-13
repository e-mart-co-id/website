

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">

    <section class="content">

        <div class="row">
            <!-- Message Error -->
            <div class="col-sm-12">
                <?php $this->load->view("backend/include/messages"); ?>
            </div>

        </div>

        <div class="row">

            <div class="col-sm-6">
                <div class="box box-solid">


                    <div class="box-header with-border">
                        <h3 class="box-title"><b> <?php echo Translate::sprint("APP Client APIs"); ?></b></h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="col-sm-12">

                            <div class="form-group">
                                <label><?php echo Translate::sprint("BASE_URL"); ?> </label>
                                <input type="text" class="form-control" required="required"
                                       placeholder="<?= Translate::sprint("Enter") ?> ..." value="<?= site_url() ?>"
                                       readonly>
                            </div>

                            <div class="form-group">
                                <label><?php echo Translate::sprint("BASE_URL_API"); ?> </label>
                                <input type="text" class="form-control" required="required"
                                       placeholder="<?= Translate::sprint("Enter") ?> ..."
                                       value="<?= site_url("api") ?>" readonly>
                            </div>
                            <div class="form-group hidden">
                                <label><?php echo Translate::sprint("CRYPTO_KEY"); ?> <span
                                            style="color: grey;font-size: 11px;"><BR>NB: <?php echo Translate::sprint("Copy_this_key_your_android_res", "Copy this key in your android resource file \"app_config.xml\""); ?></span></label>
                                <input type="text" class="form-control"
                                       placeholder="<?= Translate::sprint("Enter") ?> ..." value="<?= (!DEMO)?CRYPTO_KEY:"*** Hidden ***" ?>"
                                       readonly>
                            </div>


                            <?php if (defined("ANDROID_PURCHASE_ID") and defined("ANDROID_API")): ?>
                                <div class="form-group">
                                    <label><?php echo Translate::sprint("ANDROID API"); ?> <span
                                                style="color: grey;font-size: 11px;"><BR>NB: <?php echo Translate::sprint("Copy_this_key_your_android_res", "Copy this key in your android resource file \"app_config.xml\""); ?></span></label>
                                    <input type="text" class="form-control"
                                           placeholder="<?= Translate::sprint("Enter") ?> ..."
                                           value="<?=(!DEMO)?ANDROID_API:"*** Hidden ***"?>" readonly>
                                </div>
                            <?php endif; ?>


                            <?php if (defined("IOS_PURCHASE_ID") and defined("IOS_API")): ?>
                                <div class="form-group">
                                    <label><?php echo Translate::sprint("IOS API"); ?> <span
                                                style="color: grey;font-size: 11px;"><BR>NB: <?php echo Translate::sprint("Copy this key your ios config file \"AppConfig.swift\""); ?></span></label>
                                    <input type="text" class="form-control"
                                           placeholder="<?= Translate::sprint("Enter") ?> ..." value="<?=(!DEMO)?IOS_API:"*** Hidden ***"?>"
                                           readonly>
                                </div>
                            <?php endif; ?>


                        </div>

                    </div>
                    <!-- /.box-body -->
                </div>
            </div>


            <div class="col-sm-6">
                <div class="box box-solid">


                    <div class="box-header with-border">
                        <h3 class="box-title"><b><?= Translate::sprint("App Licences") ?></b></h3>
                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                        class="fa fa-minus"></i>
                            </button>
                            <button type="button" class="btn btn-box-tool" data-widget="remove"><i
                                        class="fa fa-times"></i></button>
                        </div>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">

                        <div class="col-sm-12">

                            <div class="form-group">
                                <label><?=Translate::sprint("Purchase ID for Android")?> <sup>*</sup> </label>
                                <?php if(defined("ANDROID_PURCHASE_ID")): ?>
                                    <input type="text" class="form-control"  required="required"  placeholder="<?=Translate::sprint("Enter")?> ..."  value="<?=(!DEMO)?ANDROID_PURCHASE_ID:"*** Hidden ***"?>" readonly>
                                <?php else: ?>
                                    <input style="width: 80%;display: inline" type="text" class="form-control" id="SPID"  required="required"  placeholder="<?=Translate::sprint("Enter")?> ..."  value="">
                                    <button style="width: 19%;"  class="btn btn-primary" id="second_verify"><?=Translate::sprint("Verify")?></button>
                                    <sub class="text-red"><?=Translate::sprint("Put Purchase Android ID to generate API")?></sub>
                                <?php endif; ?>
                            </div>


                            <div class="form-group">
                                <label><?= Translate::sprint("Purchase ID for iOS") ?> <sup>*</sup> </label>
                                <?php if (defined("IOS_PURCHASE_ID")): ?>
                                    <input type="text" class="form-control" required="required"
                                           placeholder="<?= Translate::sprint("Enter") ?> ..."
                                           value="<?=(!DEMO)?IOS_PURCHASE_ID:"*** Hidden ***"?>" readonly>
                                <?php else: ?>
                                    <input style="width: 80%;display: inline" type="text" class="form-control" id="SPID"
                                           required="required" placeholder="<?= Translate::sprint("Enter") ?> ..."
                                           value="">
                                    <button style="width: 19%;" class="btn btn-primary"
                                            id="second_verify"><?= Translate::sprint("Verify") ?></button>
                                    <sub class="text-red"><?=Translate::sprint("Put Purchase iOS ID to generate API")?></sub>
                                <?php endif; ?>
                            </div>

                        </div>

                    </div>
                    <!-- /.box-body -->
                </div>

            </div>


    </section>

</div>


<?php

$data['config'] = $config;

$script = $this->load->view('setting/backend/html/scripts/api-script', $data, TRUE);
TemplateManager::addScript($script);

?>




