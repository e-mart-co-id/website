<?php

$product = $product[Tags::RESULT][0];
$adminAccess = "";
if ($product['user_id'] != $this->mUserBrowser->getData("id_user")) {
    $adminAccess = "disabled";
}


?>
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
                                        aria-expanded="true"><?= Translate::sprint("Pricing", "") ?></a></li>

                        <?php if (ModulesChecker::isEnabled("nsorder")): ?>
                            <li class="order-tab"><a href="#product_order" class="title uppercase" data-toggle="tab"
                                            aria-expanded="true"><?= Translate::sprint("Stock", "") ?></a>
                            </li>
                        <?php endif; ?>

                        <?php if(ModulesChecker::isEnabled("product_variants")): ?>
                        <li class=""><a href="#product_variants" class="title uppercase" data-toggle="tab"
                                        aria-expanded="true"><?= Translate::sprint("Variants") ?></a></li>
                        <?php endif; ?>

                        <li class=""><a href="#product_more_options" class="title uppercase" data-toggle="tab"
                          aria-expanded="true"><?= Translate::sprint("More") ?></a></li>


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
                                                   placeholder="Ex: black friday" value="<?= $product['name'] ?>">
                                        </div>
                                        <div class="form-group">
                                            <label><?= Translate::sprint("Description", "") ?></label>
                                            <textarea class="form-control" rows="7" id="editable-textarea"
                                                      placeholder="<?= Translate::sprint("Enter") ?> ..."><?= $product['description'] ?></textarea>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group required">
                                            <?php

                                            $images = $product['images'];
                                            if ($images != "" AND !is_array($images)) {
                                                $images = json_decode($images);
                                            }

                                            ?>

                                            <?php

                                            $upload_plug = $this->uploader->plugin(array(
                                                "limit_key" => "aOhFiles",
                                                "token_key" => "SzYjEsS-4555",
                                                "limit" => MAX_PRODUCT_IMAGES,
                                                "cache" => $images,
                                            ));

                                            echo $upload_plug['html'];
                                            TemplateManager::addScript($upload_plug['script']);

                                            ?>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="box-footer">
                                <button <?= $adminAccess ?> type="button" class="btn  btn-primary btnSave" ><span
                                            class="glyphicon glyphicon-check"></span>
                                    <?= Translate::sprint("Save Changes") ?> </button>
                            </div>
                        </div>

                        <div class="tab-pane" id="product_options">
                            <div class="box-body">

                                <div class="row">
                                    <!-- text input -->
                                    <div class="col-sm-6 pricing">

                                        <h3 class="box-title"><b>
                                                <?= Translate::sprint("Pricing") ?></b></h3>


                                        <?php

                                        $currency = $this->mCurrencyModel->getCurrency(DEFAULT_CURRENCY);

                                        ?>

                                        <div class="form-group form-price">
                                            <div class="row">
                                                <div class="col-sm-12 no-margin">

                                                    <?php if (ConfigManager::getValue('ORDER_COMMISSION_ENABLED') == TRUE): ?>

                                                        <?php

                                                        $pc = 0;

                                                        if ($product['product_type'] == "price") {
                                                            $pc = $product['product_value'] - $product['commission'];
                                                        }

                                                        ?>

                                                        <div class="form-group">
                                                            <label><?= _lang("Original Price") ?> <?= DEFAULT_CURRENCY ?>
                                                                , <?= $currency['symbol'] ?></label>
                                                            <input type="number" class="form-control" id="price"
                                                                   placeholder="<?= Translate::sprint("Enter price...") ?>"
                                                                   value="<?= $pc ?>">
                                                        </div>


                                                        <div class="form-group">
                                                            <label><?= _lang("Price with commission") ?></label> /
                                                            <label><?= _lang("Price") ?>
                                                                + <?= ConfigManager::getValue('ORDER_COMMISSION_VALUE') ?>
                                                                %</label>
                                                            <input type="number" class="form-control"
                                                                   id="priceCommission"
                                                                   placeholder="<?= Translate::sprint("Price with commission...") ?>"
                                                                   value="<?= $product['product_value'] ?>" disabled>
                                                        </div>

                                                        <input type="hidden" class="form-control" id="priceInput"
                                                               value="<?= $pc ?>">
                                                        <input type="hidden" class="form-control" id="commission"
                                                               value="<?= ConfigManager::getValue('ORDER_COMMISSION_VALUE') ?>">

                                                    <?php else: ?>

                                                        <div class="form-group">
                                                            <label><?= _lang("Price") ?> <?= DEFAULT_CURRENCY ?>
                                                                , <?= $currency['symbol'] ?></label>
                                                            <input type="number" class="form-control" id="price"
                                                                   placeholder="<?= Translate::sprint("Enter price of your product") ?>"
                                                                   value="<?=$product['product_value']?>"/>
                                                        </div>

                                                    <?php endif; ?>
                                                </div>
                                            </div>


                                        </div>

                                    </div>
                                </div>
                            </div>

                            <div class="box-footer">
                                <button <?= $adminAccess ?> type="button" class="btn  btn-primary btnSave"><span
                                            class="glyphicon glyphicon-check"></span>
                                    <?= Translate::sprint("Save Changes") ?> </button>
                            </div>
                        </div>

                        <?php if (ModulesChecker::isEnabled("nsorder")): ?>
                        <div class="tab-pane" id="product_order">
                            <div class="box-body">

                                <div class="row">
                                    <div class="col-md-6">

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
                                                            <select id="cf_id" class="select2">
                                                                <label><?=Translate::sprint("Checkout fields")?></label>
                                                                <option value="<?=$pdc_cf?>"><?= Translate::sprint('Default checkout fields') ?>/option>
                                                                    <?php foreach ($cf_list as $cf): ?>
                                                                <option value="<?= $cf['id'] ?>" <?= $product['cf_id'] == $cf['id'] ? "selected" : "" ?>><?= $cf['label'] ?></option>
                                                                <?php endforeach; ?>
                                                            </select>
                                                        </div>
                                                    <?php else: ?>
                                                        <input type="hidden" id="cf_id" value="<?=$pdc_cf?>"/>
                                                    <?php endif; ?>


                                                    <div class="form-group">

                                                        <div class="input-group hidden">
                                                            <input class="form-control" type="text" id="custom-button-text"
                                                                   placeholder="<?= _lang("Enter...") ?>">
                                                            <div class="input-group-addon cursor-pointer text-blue"
                                                                 id="open-oml"><i class="mdi mdi-translate"></i></div>
                                                        </div>

                                                    </div>


                                                    <div class="form-group">
                                                        <label><input type="checkbox"
                                                                      id="stock" <?= ($product['stock'] >= 0) ? "checked" : "" ?>/>&nbsp;&nbsp;<?= _lang("Enable Stock for this item") ?>
                                                        </label>
                                                    </div>

                                                    <div class="form-group order-quantity-value <?= $product['stock'] > 1 ? "" : "hidden" ?>">
                                                        <input type="number" class="form-control" value="<?= $product['stock'] ?>"   placeholder="<?=_lang("Enter quantity")?>"/>
                                                        <p class="text-blue">
                                                            <i class="mdi mdi-information-outline"></i>&nbsp;&nbsp;<?=_lang("Set -1 for unlimited quantity")?>
                                                        </p>
                                                    </div>

                                                </div>
                                            </div>

                                        </div>


                                    </div>
                                </div>

                            </div>
                            <div class="box-footer">
                                <button <?= $adminAccess ?> type="button" class="btn  btn-primary btnSave" ><span
                                            class="glyphicon glyphicon-check"></span>
                                    <?= Translate::sprint("Save Changes") ?> </button>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php if(ModulesChecker::isEnabled("product_variants")): ?>
                        <div class="tab-pane" id="product_variants">
                            <div class="box-body">
                                <?php

                                $product_variants = $this->product_variants->plug(array(
                                        'id' => $product['id_product'],
                                        'title' => _lang("Product variants"),
                                ));
                                echo $product_variants['html'];
                                TemplateManager::addScript($product_variants['script']);

                                ?>
                            </div>

                            <div class="box-footer">
                                <button <?= $adminAccess ?> type="button" class="btn  btn-primary btnSave" ><span
                                            class="glyphicon glyphicon-check"></span>
                                    <?= Translate::sprint("Save Changes") ?> </button>
                            </div>
                        </div>
                        <?php endif; ?>

                        <div class="tab-pane" id="product_more_options">
                            <div class="box-body">
                                <div class="col-sm-6">
                                    <?php if (GroupAccess::isGranted('product', MANAGE_PRODUCTS)): ?>
                                        <h3 class="box-title"><b>
                                                <?= Translate::sprint("Featured Options") ?></b></h3>
                                        <?php

                                        $checked0 = "";
                                        if (intval($product['featured']) == 0)
                                            $checked0 = " checked='checked'";

                                        $checked = "";
                                        if (intval($product['featured']) == 1)
                                            $checked = " checked='checked'";

                                        ?>
                                        <div class="form-group">
                                            <label style="cursor: pointer;">
                                                <input name="featured" type="radio"
                                                       id="featured_item0" <?= $checked0 ?>>&nbsp;&nbsp;
                                                <?= Translate::sprint("Disabled Featured") ?>
                                            </label><br>
                                            <label style="cursor: pointer;">
                                                <input name="featured" type="radio" id="featured_item1" <?= $checked ?>>&nbsp;&nbsp;
                                                <?= Translate::sprint("Make it as featured") ?>
                                            </label>
                                        </div>
                                    <?php endif; ?>
                                </div>

                            </div>

                            <div class="box-footer">
                                <button <?= $adminAccess ?> type="button" class="btn  btn-primary btnSave" ><span
                                            class="glyphicon glyphicon-check"></span>
                                    <?= Translate::sprint("Save Changes") ?> </button>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <!-- /.row -->
    </section>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->
<?php


$data['product'] = $product;
$data['uploader_variable'] = $upload_plug['var'];

$script = $this->load->view('product/backend/html/scripts/edit-script', $data, TRUE);
TemplateManager::addScript($script);


$data0 = array();
$html = $this->load->view('product/backend/html/modal-order-multi-language', $data0, TRUE);
TemplateManager::addHtml($html);

?>



