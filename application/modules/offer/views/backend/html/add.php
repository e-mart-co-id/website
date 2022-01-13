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
                                        aria-expanded="true"><?= Translate::sprint("Deal") ?></a></li>

                        <?php if (ModulesChecker::isEnabled("product")): ?>
                            <li class="offer_products"><a href="#offer_products" class="title uppercase"
                                                          data-toggle="tab"
                                                          aria-expanded="true"><?= Translate::sprint("Products") ?></a>
                            </li>
                        <?php endif; ?>

                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="offer_detail">
                            <div class="box-body">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label><?= Translate::sprint("Select Store") ?></label>
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
                                                "limit" => MAX_OFFER_IMAGES
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
                                $nbr_offers = UserSettingSubscribe::getUDBSetting($usr_id, KS_NBR_OFFERS_MONTHLY);

                                ?>

                                <?php if ($nbr_offers > 0 or $nbr_offers == -1): ?>
                                    <button type="button" class="btn  btn-primary btnCreate"><span
                                                class="glyphicon glyphicon-check"></span>
                                        <?= Translate::sprint("Create") ?> </button>
                                <?php else: ?>
                                    <button type="button" class="btn btn-primary btnCreate" disabled><span
                                                class="glyphicon glyphicon-check"></span>
                                        <?= Translate::sprint("Create") ?> </button>
                                    &nbsp;&nbsp;
                                    <span class="text-red font-size12px"><i class="mdi mdi-information-outline"></i>&nbsp;<?= Translate::sprint(Messages::EXCEEDED_MAX_NBR_OFFERS) . $nbr_offers ?></span>
                                <?php endif; ?>
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
                                                    <label><?= Translate::sprint("Percent") ?> </label>
                                                    <div class="form-group">
                                                        <input type="number" class="form-control" id="percent"
                                                               placeholder="Exemple : -50 %">
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                    <div class="clearfix"></div>
                                    <div class="col-sm-6">

                                        <h3 class="box-title"><b>
                                                <?= Translate::sprint("Deal") ?></b></h3>

                                        <div class="form-group">
                                            <label><input type="checkbox"
                                                          id="make_as_deal"/>&nbsp;&nbsp;<?= _lang("Make as a deal") ?>
                                            </label>
                                        </div>

                                        <div class="form-group deal-data hidden">
                                            <div class="row">

                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label> <?= Translate::sprint("Date Begin", "") ?> </label>
                                                    <div class="input-group">
                                                        <div class="input-group-addon"><i class="mdi mdi-calendar"></i>
                                                        </div>
                                                        <input class="form-control" data-provide="datepicker"
                                                               placeholder="YYYY-MM-DD" type="text"
                                                               name="date_b"
                                                               id="date_b"
                                                               value="<?= date("Y-m-d", time()) ?>" disabled/>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <label><?= Translate::sprint("Date End") ?> </label>
                                                    <div class="input-group">
                                                        <div class="input-group-addon"><i class="mdi mdi-calendar"></i>
                                                        </div>
                                                        <input class="form-control" data-provide="datepicker"
                                                               type="text" placeholder="YYYY-MM-DD"
                                                               name="date_e"
                                                               id="date_e" disabled/>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <sub class="text-blue">
                                                        <i class="mdi mdi-information-outline"></i>&nbsp;&nbsp;<?= _lang("Offer will be disappeared after Date End") ?>
                                                    </sub>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <div class="box-footer ">
                                <?php

                                $usr_id = $this->mUserBrowser->getData('id_user');
                                $nbr_offers = UserSettingSubscribe::getUDBSetting($usr_id, KS_NBR_OFFERS_MONTHLY);

                                ?>

                                <?php if ($nbr_offers > 0 or $nbr_offers == -1): ?>
                                    <button type="button" class="btn  btn-primary btnCreate"><span
                                                class="glyphicon glyphicon-check"></span>
                                        <?= Translate::sprint("Create") ?> </button>
                                <?php else: ?>
                                    <button type="button" class="btn btn-primary btnCreate" disabled><span
                                                class="glyphicon glyphicon-check"></span>
                                        <?= Translate::sprint("Create") ?> </button>
                                    &nbsp;&nbsp;
                                    <span class="text-red font-size12px"><i class="mdi mdi-information-outline"></i>&nbsp;<?= Translate::sprint(Messages::EXCEEDED_MAX_NBR_OFFERS) . $nbr_offers ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php if (ModulesChecker::isEnabled("product")): ?>
                            <div class="tab-pane" id="offer_products">
                                <div class="box-body">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="box box-solid">
                                                <div class="box-header">
                                                    <div class="box-title">
                                                        <b><?= Translate::sprint("The offer will be applied on all products mentioned below") ?></b>
                                                    </div>
                                                </div>
                                                <div class="box-body">
                                                    <div class="form-group">
                                                        <label><?= _lang("Select one or many products") ?></label>
                                                        <select type="text" class="form-control select2"
                                                                multiple="multiple" id="select_products"
                                                                placeholder="<?= _lang("Write product name...") ?>">

                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="box-footer">
                                    <?php

                                    $usr_id = $this->mUserBrowser->getData('id_user');
                                    $nbr_offers = UserSettingSubscribe::getUDBSetting($usr_id, KS_NBR_OFFERS_MONTHLY);

                                    ?>

                                    <?php if ($nbr_offers > 0 or $nbr_offers == -1): ?>
                                        <button type="button" class="btn  btn-primary btnCreate"><span
                                                    class="glyphicon glyphicon-check"></span>
                                            <?= Translate::sprint("Create") ?> </button>
                                    <?php else: ?>
                                        <button type="button" class="btn btn-primary btnCreate" disabled><span
                                                    class="glyphicon glyphicon-check"></span>
                                            <?= Translate::sprint("Create") ?> </button>
                                        &nbsp;&nbsp;
                                        <span class="text-red font-size12px"><i class="mdi mdi-information-outline"></i>&nbsp;<?= Translate::sprint(Messages::EXCEEDED_MAX_NBR_OFFERS) . $nbr_offers ?></span>
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

$script = $this->load->view('offer/backend/html/scripts/add-script', $data, TRUE);
TemplateManager::addScript($script);

$data0 = array();
$html = $this->load->view('offer/backend/html/modal-order-multi-language', $data0, TRUE);
TemplateManager::addHtml($html);


?>
