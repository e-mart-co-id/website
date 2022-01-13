<?php


$list = $offers[Tags::RESULT];
$pagination = $offers["pagination"];

// this fields serve to filter offers by status
$status = $this->input->get("status");
$filterBy = $this->input->get("filterBy");

/*if ($status == 1)
    $statusName = "&nbsp;&nbsp;&nbsp;<span class='badge bg-green'>&nbsp;" . Translate::sprint("My Offers") . "&nbsp;&nbsp;<a style='color:#fff !important;' href='" . admin_url("offer/offers") . "'>x</a>&nbsp;</span>";
else
    $statusName = "";*/


if (isset($filterBy))
    $filerN = "&nbsp;&nbsp;&nbsp;<span class='badge bg-red-active'>&nbsp;" . Translate::sprint("Clear filter") . "&nbsp;&nbsp;<a style='color:#fff !important;' href='" . current_url() . "'>x</a>&nbsp;</span>";
else
    $filerN = "";

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

            <div class="row">
                <div class="col-xs-12">
                    <div class="box box-solid">
                        <div class="box-header">
                            <div class="box-title" style="width : 100%;">
                                <div class="row">
                                    <div class="pull-left col-md-8">
                                        <b><?= Translate::sprint("Offers") ?></b> <?= $filerN ?>
                                    </div>
                                    <div class="pull-right col-md-4">
                                        <?php if (GroupAccess::isGranted('offer', ADD_OFFER)) : ?>
                                            <a href="<?= admin_url("offer/add") ?>">
                                                <button type="button" data-toggle="tooltip"
                                                        title="<?= Translate::sprint("Create new offer", "") ?> "
                                                        class="btn btn-primary btn-sm pull-right"><span
                                                            class="glyphicon glyphicon-plus"></span></button>
                                            </a>
                                        <?php endif; ?>

                                        <form method="get"
                                              action="<?php echo current_url(); ?>">

                                            <div class="input-group input-group-sm">
                                                <input class="form-control" size="30" name="search" type="text"
                                                       placeholder="<?= Translate::sprint("Search") ?>"
                                                       value="<?= htmlspecialchars($this->input->get("search")) ?>">
                                                <span class="input-group-btn">
                                                <button type="submit" class="btn btn-primary btn-flat"><i
                                                            class="mdi mdi-magnify"></i></button>
                                        </span>
                                            </div>
                                        </form>

                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body table-responsive">
                            <table id="example2" class="table table-bordered table-hover">
                                <thead>
                                <tr>
                                    <!--    <th>ID</th>-->
                                    <th><?= Translate::sprint("Image", "") ?></th>
                                    <th><?= Translate::sprint("Name", "") ?></th>
                                    <th><?= Translate::sprint("Owner", "") ?></th>
                                    <th><?= Translate::sprint("Status", "") ?></th>
                                    <th hidden><?= Translate::sprint("Views", "") ?></th>
                                    <th hidden><?= Translate::sprint("Downloads", "") ?></th>
                                    <th><?= Translate::sprint("Offer", "") ?></th>
                                    <th><?= Translate::sprint("Deal", "") ?></th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>

                                <?php if (count($list)) { ?>
                                    <?php foreach ($list as $offer) { ?>

                                        <?php


                                        $current = date("Y-m-d H:i:s", time());
                                        $currentData = $current;
                                        $offer['date_start'] = MyDateUtils::convert($offer['date_start'], "UTC", "UTC", "Y-m-d");
                                        $offer['date_end'] = MyDateUtils::convert($offer['date_end'], "UTC", "UTC", "Y-m-d");

                                        $currentData = date_create($currentData);
                                        $dateStart = date_create($offer['date_start']);
                                        $dateEnd = date_create($offer['date_end']);

                                        $differenceStart = $currentData->diff($dateStart);
                                        $differenceEnd = $currentData->diff($dateEnd);

                                        $diff_millseconds_start = strtotime($offer['date_start']) - strtotime($current);
                                        $diff_millseconds_end = strtotime($offer['date_end']) - strtotime($current);

                                        ?>

                                        <tr>
                                            <td>
                                                <?php

                                                try {

                                                    if (!is_array($offer['images']))
                                                        $images = json_decode($offer['images'], JSON_OBJECT_AS_ARRAY);
                                                    else
                                                        $images = $offer['images'];

                                                    if (isset($images[0])) {
                                                        $images = $images[0];
                                                        if (isset($images['100_100']['url'])) {
                                                            echo '<img src="' . $images['100_100']['url'] . '"width="50" height="50" alt="Offer Image">';
                                                        } else {
                                                            echo '<img src="' . base_url("views/skin/backend/images/def_logo.png") . '"width="50" height="50" alt="Offer Image">';
                                                        }
                                                    } else {
                                                        echo '<img src="' . base_url("views/skin/backend/images/def_logo.png") . '"width="50" height="50" alt="Offer Image">';
                                                    }

                                                } catch (Exception $e) {
                                                    $e->getMessage();
                                                    echo '<img src="' . base_url("views/skin/backend/images/def_logo.png") . '"width="50" height="50" alt="Offer Image">';
                                                }

                                                ?>
                                            </td>
                                            <td>
                                                <span style="font-size: 14px"><?= Text::output($offer['name']) ?></span>
                                                <?php if ($offer['featured'] == 1): ?>
                                                    &nbsp;&nbsp;<span class="badge bg-blue-active"
                                                                      style="font-size: 10px;text-transform: uppercase"><i
                                                                class="mdi mdi-check"></i>&nbsp;<?= Translate::sprint("Featured") ?></span>
                                                <?php endif; ?><br>
                                                <span style="font-size: 12px;">
                                                <?php
                                                echo '<i class="mdi mdi-map-marker"></i>&nbsp;<a href="' . admin_url("store/edit?id=" . $offer['store_id']) . '"> ' . $this->mStoreModel->getStoreName($offer['store_id']) . '</a>';
                                                ?>
                                            </span>
                                            </td>

                                            <td>
                                                <?php if (GroupAccess::isGranted('offer', EDIT_OFFER)): ?>
                                                    <a style="font-size: 11px"
                                                       href="<?= admin_url("user/edit?id=" . $offer['user_id']) ?>"><u><?= ucfirst($this->mUserModel->getUserNameById($offer['user_id'])) ?></u></a>
                                                <?php endif; ?>
                                            </td>
                                            <td>

                                                <?php if ($offer['status'] == 0) : ?>
                                                    <a href="<?php echo current_url() . "?status=" . $offer['status'] . "&filterBy=Unpublished"; ?>">
                                                    <span class="badge bg-yellow" data-toggle="tooltip"
                                                          title="<?= _lang("Must be approved by the admin") ?>"><i
                                                                class="mdi mdi-history"></i> &nbsp; <?php echo Translate::sprint("Unpublished") ?>  &nbsp;&nbsp;</span>
                                                    </a>
                                                <?php elseif ($offer['status'] == 1): ?>

                                                    <a href="<?php echo current_url() . "?status=" . $offer['status'] . "&filterBy=Published"; ?>">
                                                    <span class="badge bg-green"><i
                                                                class="mdi mdi-history"></i> &nbsp;  <?php echo Translate::sprint("Published") ?> &nbsp;&nbsp;</span>
                                                    </a>

                                                <?php endif; ?>

                                            </td>


                                            <td hidden>
                                                <span data-toggle="tooltip" title="<?=$offer['views']?> peoples have watched this item" class="badge bg-light-blue"> <i class="mdi mdi-eye"></i>&nbsp;&nbsp; <?=$offer['views']?> </span>
                                            </td>

                                            <td hidden>
                                                <span data-toggle="tooltip" title="<?=$offer['downloads']?> peoples have downloaded pictures from this item" class="badge bg-yellow-active"> <i class="mdi mdi-download"></i>&nbsp;&nbsp; <?=$offer['downloads']?> </span>
                                            </td>

                                            <td>

                                                <?php

                                                if (is_array($offer['currency']))
                                                    $offer['currency'] = $offer['currency']['code'];

                                                if ($offer['product_type'] == 'price') {
                                                    echo '<span class="badge bg-red">&nbsp;' . Currency::parseCurrencyFormat($offer['product_value'], $offer['currency']) . '&nbsp;&nbsp;</span>';
                                                } else if ($offer['product_type'] == 'percent') {
                                                    echo '<span class="badge bg-red">&nbsp;' . intval($offer['product_value']) . '% &nbsp;&nbsp;</span>';
                                                } else {
                                                    echo '<span class="badge bg-red">&nbsp;' . Translate::sprint("Promotion") . '&nbsp;&nbsp;</span>';
                                                }

                                                ?>


                                            </td>


                                            <td>
                                        <span style="font-size: 12px;">

                                            <?php if ($offer['is_deal'] == 1): ?>


                                                <?php

                                                $title = "";
                                                if ($diff_millseconds_start > 0) {
                                                    $title = Translate::sprint("Start after") . ": " . MyDateUtils::format_interval($differenceStart);
                                                } else if ($diff_millseconds_start < 0 && $diff_millseconds_end > 0) {
                                                    $title = Translate::sprint("End after") . ": " . MyDateUtils::format_interval($differenceEnd);
                                                } elseif ($diff_millseconds_end < 0) {
                                                    $title = Translate::sprintf("Ended at %s", array($offer['date_end']));
                                                }

                                                ?>
                                                <?php if ($diff_millseconds_start > 0): ?>
                                                    <a data-toggle="tooltip" title="<?= $title ?>"
                                                       href="<?php echo current_url() . "?status=" . $offer['status'] . "&filterBy=Started"; ?>">
                                                        <span class="badge bg-blue"><i
                                                                    class="mdi mdi-check"></i> &nbsp;  <?php echo Translate::sprint("Deal not started") ?>  &nbsp;&nbsp;</span>
                                                    </a>
                                                <?php elseif ($diff_millseconds_start < 0 && $diff_millseconds_end > 0) : ?>
                                                    <a data-toggle="tooltip" title="<?= $title ?>"
                                                       href="<?php echo current_url() . "?status=" . $offer['status'] . "&filterBy=Started"; ?>">
                                                        <span class="badge bg-blue"><i
                                                                    class="mdi mdi-check"></i> &nbsp;  <?php echo Translate::sprint("Deal started") ?>  &nbsp;&nbsp;</span>
                                                    </a>
                                                <?php else: ?>
                                                    <a data-toggle="tooltip" title="<?= $title ?>"
                                                       href="<?php echo current_url() . "?status=" . $offer['status'] . "&filterBy=Finished"; ?>">
                                                        <span class="badge bg-red"><i
                                                                    class="mdi mdi-close"></i> &nbsp;  <?php echo Translate::sprint("Deal finished") ?>   &nbsp;&nbsp;</span>
                                                    </a>
                                                <?php endif; ?>


                                            <?php else: ?>
                                                <?= _lang("Disabled") ?>
                                            <?php endif; ?>
                                        </span>


                                            </td>
                                            <td align="right">

                                                <?php if (GroupAccess::isGranted('offer', MANAGE_OFFERS)) {


                                                    if ($offer['verified'] == 1) {
                                                        if ($offer['status'] == 1) {
                                                            echo ' <a href="' . site_url("ajax/offer/changeStatus?id=" . $offer['id_product']) . '" data-toggle="tooltip" title="' . _lang("When you disable this offer all related products will be lost") . '" class="linkAccess btn btn-default"><i class="fa fa-times" aria-hidden="true"></i></a>';
                                                        } else if ($offer['status'] == 0) {
                                                            echo ' <a href="' . site_url("ajax/offer/changeStatus?id=" . $offer['id_product']) . '" data-toggle="tooltip" title="Enable" class="linkAccess btn btn-default"><i class="fa fa-check" aria-hidden="true"></i></a> ';
                                                        }
                                                    } else {
                                                        echo ' <a href="' . site_url("ajax/product/verify?id=" . $offer['id_product']) . '&accept=1" class="linkAccess btn btn-default"><i class="text-white mdi mdi-thumb-up" aria-hidden="true"></i></a> ';
                                                        echo ' <a href="' . site_url("ajax/product/verify?id=" . $offer['id_product']) . '&accept=0" class="linkAccess btn btn-default"><i class="text-white fa fa-times" aria-hidden="true"></i></a> ';
                                                    }

                                                    ?>
                                                <?php } ?>


                                                <?php if ($offer['user_id'] == $this->mUserBrowser->getData("id_user")) { ?>
                                                    &nbsp;
                                                    <a href="<?= admin_url("offer/edit?id=" . $offer['id_product']) ?>"
                                                       title="<?= Translate::sprint("Edit") ?>"
                                                       class=" btn btn-default">
                                                        <span class="glyphicon glyphicon-edit"></span>

                                                    </a>
                                                <?php } else if (GroupAccess::isGranted('product', MANAGE_OFFERS)) { ?>
                                                    &nbsp;
                                                    <a href="<?= admin_url("offer/view?id=" . $offer['id_product']) ?>"
                                                       class=" btn btn-default"
                                                       title="<?= Translate::sprint("View") ?>">
                                                        <span class="glyphicon glyphicon-edit"></span>

                                                    </a>
                                                <?php } ?>



                                                <?php if (GroupAccess::isGranted('offer', DELETE_OFFER)): ?>
                                                    &nbsp;
                                                    <a href="#" class="remove btn btn-default"
                                                       data-id="<?= $offer['id_product'] ?>"
                                                       title="<?= Translate::sprint("Delete") ?>">
                                                        <span class="glyphicon glyphicon-trash"></span>
                                                    </a>
                                                <?php endif; ?>

                                            </td>
                                        </tr>

                                    <?php } ?>


                                <?php } else { ?>
                                    <tr>
                                        <td colspan="3"><?= Translate::sprint("No Offers", "") ?></td>
                                    </tr>
                                <?php } ?>

                                </tbody>
                            </table>

                            <div class="row">
                                <div class="col-sm-12 pull-right">
                                    <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">

                                        <?php

                                        echo $pagination->links(array(
                                            "search" => $this->input->get("search"),
                                            "status" => $this->input->get("status"),
                                        ), current_url());

                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /.box-body -->
                    </div>
                    <!-- /.box -->
                </div>


                <!-- /.col -->
            </div>
            <!-- /.row -->
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->

<?php


$script = $this->load->view('offer/backend/html/scripts/list-script', NULL, TRUE);
TemplateManager::addScript($script);






