<?php

$offer = $offer[Tags::RESULT][0];
$adminAccess = "";
if ($offer['user_id'] != $this->mUserBrowser->getData("id_user")) {
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
                            <a href="#offer_detail" class="title uppercase" data-toggle="tab"
                               aria-expanded="false"><?= Translate::sprint("Detail") ?></a></li>

                        <li class=""><a href="#offer_options" class="title uppercase" data-toggle="tab"
                                        aria-expanded="true"><?= Translate::sprint("Deal", "") ?></a></li>

                        <?php if(ModulesChecker::isEnabled("product")): ?>
                            <li class="offer_products"><a href="#offer_products" class="title uppercase" data-toggle="tab"
                                                          aria-expanded="true"><?= Translate::sprint("Products") ?></a>
                            </li>
                        <?php endif; ?>

                        <li class=""><a href="#offer_more_options" class="title uppercase" data-toggle="tab"
                                        aria-expanded="true"><?= Translate::sprint("More") ?></a></li>

                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="offer_detail">
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
                                                   placeholder="Ex: black friday" value="<?= $offer['name'] ?>">
                                        </div>
                                        <div class="form-group">
                                            <label><?= Translate::sprint("Description", "") ?></label>
                                            <textarea class="form-control" rows="7" id="editable-textarea"
                                                      placeholder="<?= Translate::sprint("Enter") ?> ..."><?= $offer['description'] ?></textarea>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group required">
                                            <?php

                                            $images = $offer['images'];
                                            if ($images != "" AND !is_array($images)) {
                                                $images = json_decode($images);
                                            }

                                            ?>

                                            <?php

                                            $upload_plug = $this->uploader->plugin(array(
                                                "limit_key" => "aOhFiles",
                                                "token_key" => "SzYjEsS-4555",
                                                "limit" => MAX_OFFER_IMAGES,
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

                        <div class="tab-pane" id="offer_options">
                            <div class="box-body">

                                <div class="row">
                                    <!-- text input -->
                                    <div class="col-sm-6 pricing">

                                        <h3 class="box-title"><b>
                                                <?= Translate::sprint("Discount") ?></b></h3>

                                        <div class="form-group form-percent">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label><?= Translate::sprint("Offer percent", "") ?> </label>
                                                    <div class="form-group">
                                                        <input <?= $adminAccess ?> type="number" class="form-control"
                                                                                   id="percent"
                                                                                   placeholder="Exemple : -50 %"
                                                                                   value="<?php if ($offer['product_type'] == "percent") echo $offer['product_value'] ?>">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="clearfix"></div>
                                    <div class="col-md-6">

                                        <h3 class="box-title"><b>
                                                <?= Translate::sprint("Deal") ?></b></h3>

                                        <div class="form-group">
                                            <label><input type="checkbox"
                                                          id="make_as_deal" <?= $offer['is_deal'] == 1 ? "checked" : "" ?>/>&nbsp;&nbsp;<?= _lang("Make as a deal") ?>
                                            </label>
                                        </div>
                                        <div class="form-group deal-data <?=$offer['is_deal']==1?"":"hidden"?>">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <p class="text-blue">
                                                        <i class="mdi mdi-information-outline"></i>&nbsp;&nbsp;<?= _lang("Offer will be disappeared after Date End") ?>
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label> <?= Translate::sprint("Date Begin") ?>  </label>
                                                    <div class="input-group">
                                                        <div class="input-group-addon">
                                                            <i class="mdi mdi-calendar"></i>
                                                        </div>
                                                        <?php

                                                        $date_start = "";
                                                        if ($offer['date_start'] != "")
                                                            $date_start = date("Y-m-d", strtotime($offer['date_start']));

                                                        ?>
                                                        <input <?= $adminAccess ?> class="form-control"
                                                                                   data-provide="datepicker"
                                                                                   placeholder="YYYY-MM-DD" type="text"
                                                                                   name="date_b"
                                                                                   id="date_b"
                                                                                   value="<?= $date_start ?>" disabled/>
                                                    </div>

                                                </div>
                                                <div class="col-md-6">
                                                    <label><?= Translate::sprint("Date End") ?> </label>

                                                    <div class="input-group">
                                                        <div class="input-group-addon">
                                                            <i class="mdi mdi-calendar"></i>
                                                        </div>

                                                        <?php

                                                        $date_end = "";
                                                        if ($offer['date_end'] != "")
                                                            $date_end = date("Y-m-d", strtotime($offer['date_end']));

                                                        ?>
                                                        <input <?= $adminAccess ?> class="form-control"
                                                                                   data-provide="datepicker" type="text"
                                                                                   placeholder="YYYY-MM-DD"
                                                                                   name="date_e" id="date_e"
                                                                                   value="<?= $date_end ?>" disabled/>


                                                    </div>
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

                        <?php if(ModulesChecker::isEnabled("product")): ?>
                            <div class="tab-pane" id="offer_products">
                                <div class="box-body">
                                    <div class="row">
                                        <div class="col-sm-8">
                                            <div class="box box-solid">
                                                <div class="box-header">
                                                    <div class="box-title">
                                                        <b><?= Translate::sprint("The offer will be applied on all products mentioned below") ?></b>
                                                    </div>
                                                </div>
                                                <div class="box-body">
                                                    <div class="form-group">
                                                        <?php
                                                        $linked_products = $this->mOfferModel->getLinkedProducts($offer['id_product']);
                                                        ?>
                                                        <label><?=_lang("Select one or many products")?></label>
                                                        <select type="text" class="form-control select2" multiple="multiple" id="select_products" placeholder="<?=_lang("Write product name...")?>">

                                                            <?php foreach ($linked_products as $product) : ?>
                                                                <option value="<?=$product['id_product']?>" selected><?=$product['name']?></option>
                                                            <?php endforeach; ?>

                                                        </select>
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
                        <?php endif; ?>

                        <div class="tab-pane" id="offer_more_options">
                            <div class="box-body">
                                <div class="col-sm-6">
                                    <?php if (GroupAccess::isGranted('offer', MANAGE_OFFERS)): ?>
                                        <h3 class="box-title"><b>
                                                <?= Translate::sprint("Featured Options") ?></b></h3>
                                        <?php

                                        $checked0 = "";
                                        if (intval($offer['featured']) == 0)
                                            $checked0 = " checked='checked'";

                                        $checked = "";
                                        if (intval($offer['featured']) == 1)
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

                        <?php if($offer['product_type']=='price'): ?>
                        <div class="tab-pane" id="offer_variants">

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
<!-- /.content-wrapper -->
<?php


$data['offer'] = $offer;
$data['uploader_variable'] = $upload_plug['var'];

$script = $this->load->view('offer/backend/html/scripts/edit-script', $data, TRUE);
TemplateManager::addScript($script);


$data0 = array();
$html = $this->load->view('offer/backend/html/modal-order-multi-language', $data0, TRUE);
TemplateManager::addHtml($html);

?>



