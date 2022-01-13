<?php
$categories = $categories[Tags::RESULT];

$store = $dataStores[Tags::RESULT][0];


$disabled = "";
if ($store['user_id'] != $this->mUserBrowser->getData("id_user")) {
    $disabled = "disabled='true'";
}

/*
if ($store['user_id'] != $this->mUserBrowser->getData("id_user") AND $this->mUserBrowser->getData("typeAuth") != "admin") {
    redirect(admin_url("error404"));
}
*/

$active = $this->input->get("active");

?>
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

            <form id="form" role="form">


                <div class="col-sm-12">
                    <div class="nav-tabs-custom">
                        <ul class="nav nav-tabs">
                            <li class="<?=$active==""?"active":""?>">
                                <a href="#detail" class="title uppercase" data-toggle="tab"
                                   aria-expanded="false"><?= Translate::sprint("Detail") ?></a></li>

                            <li class="">
                                <a href="#images" class="title uppercase" data-toggle="tab"
                                   aria-expanded="false"><?= Translate::sprint("Images & Gallery") ?></a></li>

                            <li class="">
                                <a href="#location" class="title uppercase" data-toggle="tab"
                                   aria-expanded="false"><?= Translate::sprint("Location") ?></a></li>

                            <?php if(ModulesChecker::isEnabled("service")): ?>
                                <li class="<?=$active=="service"?"active":""?>"><a href="#service" class="title uppercase" data-toggle="tab"
                                                aria-expanded="true"><?= Translate::sprint("Services") ?></a></li>
                            <?php endif; ?>


                            <li class="">
                                <a href="#more" class="title uppercase" data-toggle="tab"
                                   aria-expanded="false"><?= Translate::sprint("More") ?></a></li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane <?=$active==""?"active":""?>" id="detail">
                                <div class="box-body">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <input type="hidden" id="id" value="<?= $store['id_store'] ?>">
                                            <div class="form-group">
                                                <label><?= Translate::sprint("Name", "") ?> : </label>
                                                <input <?= $disabled ?> type="text" class="form-control"
                                                                        placeholder="<?= Translate::sprint("Enter") ?> ..."
                                                                        value="<?= $store['name'] ?>" name="name"
                                                                        id="name">
                                            </div>
                                            <div class="form-group">
                                                <label><?= Translate::sprint("Category", "") ?> :</label>
                                                <select id="cat" name="cat"
                                                        class="form-control select2 selectCat" <?= $disabled ?> >
                                                    <?php if (!empty($categories)) { ?>

                                                        <?php foreach ($categories AS $cat) {
                                                            if ($cat['id_category'] == $store['category_id']) {
                                                                ?>

                                                                <option selected
                                                                        value="<?= $cat['id_category'] ?>"><?= $cat['name'] ?></option>

                                                            <?php } else {
                                                                ?>
                                                                <option value="<?= $cat['id_category'] ?>"><?= $cat['name'] ?></option>


                                                            <?php }
                                                        } ?>
                                                    <?php } ?>

                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label><?= Translate::sprint("Detail", "") ?> :</label>
                                                <textarea <?= $disabled ?> id="editable-textarea" class="form-control"
                                                                           style="height: 300px"><?= $store['detail'] ?></textarea>

                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label><?= Translate::sprint("Phone Number", "") ?> :</label>
                                                <input <?= $disabled ?> type="text" class="form-control"
                                                                        placeholder="<?= Translate::sprint("Enter") ?> ..."
                                                                        value='<?= $store['telephone'] ?>' name="tel"
                                                                        id="tel">
                                            </div>
                                            <div class="form-group">
                                                <label><?= Translate::sprint("WebSite", "") ?></label>
                                                <br>
                                                <sup><span><?= Translate::sprint("Enter a valid URL with http or https") ?>
                                                </sup>
                                                <input <?= $disabled ?> type="text" class="form-control"
                                                                        value="<?= $store['website'] ?>"
                                                                        placeholder="<?= Translate::sprint("Enter") ?> ..."
                                                                        name="web" id="web">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane" id="images">
                                <div class="box-body">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="box box-solid">
                                                <div class="box-header with-border">
                                                    <h3 class="box-title">
                                                        <b> <?= Translate::sprint("Store photos") ?></b></h3>
                                                </div>
                                                <!-- /.box-header -->
                                                <div class="box-body">
                                                    <!-- text input -->
                                                    <div class="form-group required">


                                                        <!-- text input -->

                                                        <?php

                                                        $images = $store['images'];
                                                        if ($images != "" AND !is_array($images)) {
                                                            $images = json_decode($images);
                                                        }

                                                        ?>

                                                        <div class="form-group required">

                                                            <?php

                                                            $upload_plug = $this->uploader->plugin(array(
                                                                "limit_key" => "editFiles",
                                                                "token_key" => "SzYjESA-4555",
                                                                "limit" => MAX_STORE_IMAGES,
                                                                "cache" => $images
                                                            ));

                                                            echo $upload_plug['html'];
                                                            TemplateManager::addScript($upload_plug['script']);

                                                            ?>

                                                        </div>


                                                    </div>
                                                </div>
                                                <!-- /.box-body -->
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <?php
                                            if (ModulesChecker::isRegistred("gallery") and isset($gallery[Tags::RESULT])) {
                                                //load view
                                                $gallery_variable = $this->mGalleryModel->setup("store-gallery", $gallery[Tags::RESULT], $store['user_id']);
                                                $data['gallery_variable'] = $gallery_variable;
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane" id="location">
                                <div class="box-body">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="box box-solid">
                                                <div class="box-header with-border">
                                                    <h3 class="box-title"><b>
                                                            <?= Translate::sprint("Drag the marker to get the exact position", "") ?>
                                                            :</b></h3>
                                                </div>

                                                <div class="box-body">

                                                    <?php
                                                    $map = LocationPickerManager::plug_pick_location(array(
                                                        'lat' => $store['latitude'],
                                                        'lng' => $store['longitude'],
                                                        'address' => $store['address'],
                                                        'custom_address' => $store['address'],
                                                        'city' => "",
                                                        'country' => "",
                                                    ), array(
                                                        'lat' => TRUE,
                                                        'lng' => TRUE,
                                                        'address' => TRUE,
                                                        'custom_address' => TRUE
                                                    ));

                                                    echo $map['html'];
                                                    TemplateManager::addScript($map['script']);
                                                    $data['location_fields_id'] = $map['fields_id'];

                                                    ?>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php if(ModulesChecker::isEnabled("service")): ?>
                                <div class="tab-pane <?=$active=="service"?"active":""?>" id="service">
                                    <div class="box-body">
                                        <?php

                                        $service = $this->service->plug(array(
                                            'id' => $store['id_store'],
                                            'title' => _lang("Services"),
                                        ));
                                        echo $service['html'];
                                        TemplateManager::addScript($service['script']);

                                        ?>
                                    </div>

                                </div>
                            <?php endif; ?>
                            <div class="tab-pane" id="more">
                                <div class="box-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <?php if (OPENING_TIME_ENABLED): ?>
                                                <div class="box box-solid ">
                                                    <div class="box-header with-border">
                                                        <h3 class="box-title"><b><i
                                                                        class="mdi mdi-calendar-clock"></i>&nbsp;&nbsp;<?= Translate::sprint("Opening time") ?>
                                                            </b></h3>
                                                        <div class="box-tools pull-right">
                                                            <button type="button" class="btn btn-box-tool"
                                                                    data-widget="collapse"><i
                                                                        class="fa fa-plus"></i></button>
                                                        </div>
                                                    </div>
                                                    <!-- /.box-header -->
                                                    <div class="box-body">
                                                        <?php
                                                        $days = array(
                                                            'monday',
                                                            'tuesday',
                                                            'wednesday',
                                                            'thursday',
                                                            'friday',
                                                            'saturday',
                                                            'sunday',
                                                        );
                                                        ?>

                                                        <!-- text input -->
                                                        <div class="form-group">
                                                            <label id="opening_time">
                                                                <input type="checkbox" id="_opening_time"
                                                                       class="minimal">
                                                                &nbsp;&nbsp;<strong><?= Translate::sprint("Enable") ?></strong>
                                                                &nbsp;&nbsp;
                                                            </label>

                                                            <div id="_h" class="hidden margin-top15px">

                                                                <?php foreach ($days as $day): ?>
                                                                    <div class="form-group">
                                                                        <label><?= ucfirst(Translate::sprint($day)) ?></label>
                                                                        <div class="row">
                                                                            <div class="col-sm-6">
                                                                                <div class="input-group">
                                                                                    <div class="input-group-addon">
                                                                                        <input type="checkbox"
                                                                                               id="_checked_d_<?= $day ?>"/>
                                                                                    </div>
                                                                                    <input placeholder="<?= Translate::sprint("Opening time") ?>"
                                                                                           type="text"
                                                                                           class="form-control date-picker"
                                                                                           id="_o_d_<?= $day ?>"
                                                                                           disabled/>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-sm-6">
                                                                                <input placeholder="<?= Translate::sprint("Closing time") ?>"
                                                                                       type="text"
                                                                                       class="form-control date-picker"
                                                                                       id="_c_d_<?= $day ?>" disabled/>
                                                                            </div>

                                                                        </div>

                                                                    </div>
                                                                <?php endforeach; ?>

                                                            </div>

                                                            <?php

                                                            $data['days'] = $days;
                                                            $data['times'] = $this->mStoreModel->getOpeningTimes($store['id_store']);

                                                            $ot_script = $this->load->view('store/backend/html/scripts/edit-opening-time-script', $data, TRUE);
                                                            TemplateManager::addScript($ot_script);

                                                            ?>
                                                        </div>

                                                    </div>
                                                    <!-- /.box-body -->
                                                </div>
                                            <?php endif; ?>
                                            <div class="box box-solid">
                                                <div class="box-header with-border">
                                                    <h3 class="box-title">
                                                        <b><?= Translate::sprint("Store Options") ?></b></h3>
                                                </div>

                                                <div class="box-body">
                                                    <div class="form-group">
                                                        <label> <?php echo Translate::sprint("Enable chat feature for this store"); ?> </label>
                                                        <br>
                                                        <label>
                                                            <input class="form-check-input" name="canChat"
                                                                   type="checkbox" <?php if (isset($store["canChat"]) and $store["canChat"] == 1) echo "checked"; ?>
                                                                   id="canChat"/>
                                                            <?= Translate::sprint("Enable chat") ?>
                                                        </label>

                                                    </div>

                                                    <?php if(ModulesChecker::isEnabled("nsorder")): ?>
                                                    <div class="form-group">
                                                        <label> <?php echo Translate::sprint("Enable order feature for this store"); ?> </label>
                                                        <br>
                                                        <label>
                                                            <input class="form-check-input" name="order-system"
                                                                   type="checkbox"  <?=$store['config_order_enabled']==1?"checked":""?>
                                                                   id="order-system"/>
                                                            <?= Translate::sprint("Enable order system") ?>
                                                        </label>

                                                        <div class="order-options <?=$store['config_order_enabled']==0?"hidden":""?>" style="padding-left: 10px">
                                                            <label>
                                                                <input class="form-check-input" name="order-b-op"
                                                                       type="checkbox"
                                                                       id="order-b-op" <?=$store['config_order_based_op']==1?"checked":""?>/>
                                                                <?= Translate::sprint("Order based on opening time") ?>
                                                            </label>
                                                            <br>
                                                            <sup class="text-blue"><i class="mdi mdi-information-outline"></i>
                                                                <?=_lang("Make sure that you enabled opening time for this store")?>
                                                            </sup>
                                                        </div>
                                                    </div>
                                                    <?php endif; ?>

                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <?php if (GroupAccess::isGranted('store', MANAGE_STORES)): ?>

                                                <div class="box box-solid">
                                                    <div class="box-header with-border">
                                                        <h3 class="box-title"><b>
                                                                <?= Translate::sprint("Options") ?></b></h3>
                                                    </div>

                                                    <div class="box-body">

                                                        <?php

                                                        $checked0 = "";
                                                        if (intval($store['featured']) == 0)
                                                            $checked0 = " checked='checked'";

                                                        $checked = "";
                                                        if (intval($store['featured']) == 1)
                                                            $checked = " checked='checked'";

                                                        ?>

                                                        <div class="form-group">
                                                            <label style="cursor: pointer;">
                                                                <input name="featured" type="radio"
                                                                       id="featured_item0" <?= $checked0 ?>>&nbsp;&nbsp;
                                                                <?= Translate::sprint("Disabled Featured") ?>
                                                            </label><br>
                                                            <label style="cursor: pointer;">
                                                                <input name="featured" type="radio"
                                                                       id="featured_item1" <?= $checked ?>>&nbsp;&nbsp;
                                                                <?= Translate::sprint("Make it as featured") ?>
                                                            </label>
                                                        </div>


                                                    </div>

                                                </div>

                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="box-footer">

                                <?php if ($store['user_id'] == $this->mUserBrowser->getData("id_user")) { ?>
                                    <div class="form-group">
                                        <button type="button" class="btn  btn-primary" id="btnUpdate"><span
                                                    class="glyphicon glyphicon-check"></span>
                                            <?= Translate::sprint("Update", "") ?> </button>
                                        <button type="reset" class="btn  btn-default"><span
                                                    class="glyphicon glyphicon-remove"></span>
                                            <?= Translate::sprint("Clear", "") ?> </button>
                                    </div>
                                    <?php

                                } ?>
                            </div>
                        </div>
                    </div>
                </div>

            </form>

    </section>

</div>

<?php


$data['store'] = $store;

if (isset($gallery[Tags::RESULT]))
    $data['gallery_variable'] = $gallery_variable;

$data['uploader_variable'] = $upload_plug['var'];

$script = $this->load->view('store/backend/html/scripts/edit-script', $data, TRUE);
TemplateManager::addScript($script);

?>




