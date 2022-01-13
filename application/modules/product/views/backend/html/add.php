<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <!-- Message Error -->
            <div class="col-sm-12">
                <?php $this->load->view("backend/include/messages"); ?>
            </div>

        </div>

        <div class="row" id="form">
            <div class="col-md-12">
                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs">

                        <li class="active">
                            <a href="#product_detail" class="title uppercase" data-toggle="tab"
                               aria-expanded="false"><?= Translate::sprint("Detail") ?></a></li>

                        <li class=""><a href="#product_options" class="title uppercase" data-toggle="tab"
                                        aria-expanded="true"><?= Translate::sprint("Pricing") ?></a></li>

                        <?php if (ModulesChecker::isEnabled("nsorder")): ?>
                            <li class="product_order"><a href="#product_order" class="title uppercase" data-toggle="tab"
                                                         aria-expanded="true"><?= Translate::sprint("Stock", "") ?></a>
                            </li>
                        <?php endif; ?>

                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="product_detail">
                            <div class="box-body">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label><?= Translate::sprint("Store") ?></label>
                                            <select id="selectStore" class="form-control select2 selectStore"
                                                    style="width: 100%;">
                                                <option selected="selected" value="0">
                                                    <?= Translate::sprint("Select store", "") ?></option>
                                                <?php

                                                if (isset($myStores[Tags::RESULT])) {
                                                    foreach ($myStores[Tags::RESULT] as $st) {
                                                        echo '<option adr="' . $st['address'] . '" 
                                            lat="' . $st['latitude'] . '" lng="' . $st['longitude'] . '" 
                                            value="' . $st['id_store'] . '">' . $st['name'] . '</option>';
                                                    }
                                                }

                                                ?>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label><?= Translate::sprint("Name", "") ?></label>
                                            <input type="text" class="form-control" name="name" id="name"
                                                   placeholder="Ex: black friday">
                                        </div>
                                        <div class="form-group">
                                            <label><?= Translate::sprint("Description", "") ?></label>
                                            <textarea class="form-control" rows="7" id="editable-textarea"
                                                      placeholder="<?= Translate::sprint("Enter") ?> ..."></textarea>
                                        </div>

                                    </div>
                                    <div class="col-sm-6">
                                        <!-- text input -->
                                        <div class="form-group required">

                                            <?php

                                            $upload_plug = $this->uploader->plugin(array(
                                                "limit_key" => "aOhFiles",
                                                "token_key" => "SzYjEsS-4555",
                                                "limit" => MAX_PRODUCT_IMAGES,
                                            ));

                                            echo $upload_plug['html'];
                                            TemplateManager::addScript($upload_plug['script']);

                                            ?>


                                        </div>

                                    </div>
                                </div>
                            </div>
                            <div class="box-footer">
                                <?php

                                $usr_id = $this->mUserBrowser->getData('id_user');
                                $nbr_products = UserSettingSubscribe::getUDBSetting($usr_id, KS_NBR_PRODUCTS_MONTHLY);

                                ?>

                                <?php if ($nbr_products > 0 or $nbr_products == -1): ?>
                                    <button type="button" class="btn  btn-primary btnCreate"><span
                                                class="glyphicon glyphicon-check"></span>
                                        <?= Translate::sprint("Create") ?> </button>
                                <?php else: ?>
                                    <button type="button" class="btn btn-primary btnCreate" disabled><span
                                                class="glyphicon glyphicon-check"></span>
                                        <?= Translate::sprint("Create") ?> </button>
                                    &nbsp;&nbsp;
                                    <span class="text-red font-size12px"><i class="mdi mdi-information-outline"></i>&nbsp;<?= Translate::sprint(Messages::EXCEEDED_MAX_NBR_PRODUCTS) . $nbr_products ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="tab-pane" id="product_options">
                            <div class="box-body">
                                <div class="row">
                                    <!-- text input -->
                                    <div class="col-sm-6 pricing">

                                        <h3 class="box-title"><b>
                                                <?= Translate::sprint("Pricing") ?></b></h3>

                                        <div class="form-group form-price">
                                            <div class="row">
                                                <div class="col-sm-12 no-margin">
                                                    <?php

                                                    $currency = $this->mCurrencyModel->getCurrency(DEFAULT_CURRENCY);

                                                    ?>

                                                    <?php if (ConfigManager::getValue('ORDER_COMMISSION_ENABLED') == TRUE): ?>

                                                        <div class="form-group">
                                                            <label><?= _lang("Original Price") ?> <?= DEFAULT_CURRENCY ?>
                                                                , <?= $currency['symbol'] ?></label>
                                                            <input type="number" class="form-control" id="price"
                                                                   placeholder="<?= Translate::sprint("Enter price...") ?>">
                                                        </div>

                                                        <div class="form-group">
                                                            <label><?= _lang("Price with commission") ?></label> /
                                                            <label><?= _lang("Price") ?>
                                                                + <?= ConfigManager::getValue('ORDER_COMMISSION_VALUE') ?>
                                                                %</label>
                                                            <input type="number" class="form-control"
                                                                   id="priceCommission"
                                                                   placeholder="<?= Translate::sprint("Price with commission...") ?>"
                                                                   disabled>
                                                        </div>

                                                        <input type="hidden" class="form-control" id="priceInput">
                                                        <input type="hidden" class="form-control" id="commission"
                                                               value="<?= ConfigManager::getValue('ORDER_COMMISSION_VALUE') ?>">

                                                    <?php else: ?>

                                                        <div class="form-group">
                                                            <label><?= _lang("Price") ?> <?= DEFAULT_CURRENCY ?>
                                                                , <?= $currency['symbol'] ?></label>
                                                            <input type="number" class="form-control" id="price"
                                                                   placeholder="<?= Translate::sprint("Enter price of your product") ?>">
                                                        </div>

                                                    <?php endif; ?>

                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <div class="box-footer ">
                                <?php

                                $usr_id = $this->mUserBrowser->getData('id_user');
                                $nbr_products = UserSettingSubscribe::getUDBSetting($usr_id, KS_NBR_PRODUCTS_MONTHLY);

                                ?>

                                <?php if ($nbr_products > 0 or $nbr_products == -1): ?>
                                    <button type="button" class="btn  btn-primary btnCreate"><span
                                                class="glyphicon glyphicon-check"></span>
                                        <?= Translate::sprint("Create") ?> </button>
                                <?php else: ?>
                                    <button type="button" class="btn btn-primary btnCreate" disabled><span
                                                class="glyphicon glyphicon-check"></span>
                                        <?= Translate::sprint("Create") ?> </button>
                                    &nbsp;&nbsp;
                                    <span class="text-red font-size12px"><i class="mdi mdi-information-outline"></i>&nbsp;<?= Translate::sprint(Messages::EXCEEDED_MAX_NBR_PRODUCTS) . $nbr_products ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php if (ModulesChecker::isEnabled("nsorder")): ?>
                            <div class="tab-pane" id="product_order">
                                <div class="box-body">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="box box-solid">
                                                <div class="box-header">
                                                    <div class="box-title">
                                                        <b><?= Translate::sprint("Order Option") ?></b>
                                                    </div>
                                                </div>
                                                <div class="box-body">

                                                    <div class="order-customization">

                                                        <?php

                                                        $pdc_cf = intval(ConfigManager::getValue("product_default_checkout_cf"));

                                                        ?>

                                                        <?php if (GroupAccess::isGranted("cf_manager") && $pdc_cf == 0): ?>
                                                            <div class="form-group">
                                                                <label><?= Translate::sprint("Checkout fields") ?></label>
                                                                <select id="cf_id" class="select2">
                                                                    <option value="<?= $pdc_cf ?>"><?= Translate::sprint('Default checkout fields') ?></option>
                                                                    <?php foreach ($cf_list as $cf): ?>
                                                                        <option value="<?= $cf['id'] ?>"><?= $cf['label'] ?></option>
                                                                    <?php endforeach; ?>
                                                                </select>
                                                            </div>
                                                            <p class="text-blue"><i
                                                                        class="mdi mdi-information-outline"></i>&nbsp;&nbsp;<?= _lang("These fields will appear to the customer when ordering the product.") ?>
                                                            </p>
                                                        <?php else: ?>
                                                            <input type="hidden" id="cf_id" value="<?= $pdc_cf ?>"/>
                                                        <?php endif; ?>


                                                        <div class="form-group">
                                                            <label><input type="checkbox"
                                                                          id="stock"/>&nbsp;&nbsp;<?= _lang("Enable Stock for this item") ?>
                                                            </label>
                                                        </div>

                                                        <div class="form-group order-quantity-value hidden">
                                                            <input type="number" class="form-control"
                                                                   placeholder="<?= _lang("Enter quantity") ?>"
                                                                   value="-1"/>
                                                            <p class="text-blue">
                                                                <i class="mdi mdi-information-outline"></i>&nbsp;&nbsp;<?= _lang("Set -1 for unlimited quantity") ?>
                                                            </p>
                                                        </div>

                                                    </div>


                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6"></div>
                                    </div>
                                </div>
                                <div class="box-footer">
                                    <?php

                                    $usr_id = $this->mUserBrowser->getData('id_user');
                                    $nbr_products = UserSettingSubscribe::getUDBSetting($usr_id, KS_NBR_PRODUCTS_MONTHLY);

                                    ?>

                                    <?php if ($nbr_products > 0 or $nbr_products == -1): ?>
                                        <button type="button" class="btn  btn-primary btnCreate"><span
                                                    class="glyphicon glyphicon-check"></span>
                                            <?= Translate::sprint("Create") ?> </button>
                                    <?php else: ?>
                                        <button type="button" class="btn btn-primary btnCreate" disabled><span
                                                    class="glyphicon glyphicon-check"></span>
                                            <?= Translate::sprint("Create") ?> </button>
                                        &nbsp;&nbsp;
                                        <span class="text-red font-size12px"><i class="mdi mdi-information-outline"></i>&nbsp;<?= Translate::sprint(Messages::EXCEEDED_MAX_NBR_PRODUCTS) . $nbr_products ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

        </div>
        <!-- /.row -->
    </section>
    <!-- /.content -->
</div>
<?php


$data['uploader_variable'] = $upload_plug['var'];

$script = $this->load->view('product/backend/html/scripts/add-script', $data, TRUE);
TemplateManager::addScript($script);

$data0 = array();
$html = $this->load->view('product/backend/html/modal-order-multi-language', $data0, TRUE);
TemplateManager::addHtml($html);


?>
