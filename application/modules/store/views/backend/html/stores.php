<?php


$stores = $data[Tags::RESULT];
$pagination = $data['pagination'];


$category_id = intval($this->input->get("category_id"));

if ($category_id > 0)
    $categoryName = "&nbsp;&nbsp;&nbsp;<span class='badge bg-blue'>&nbsp;" . Translate::sprint(Text::output($this->mStoreModel->getCatName($category_id))) . "&nbsp;&nbsp;<a style='color:#fff !important;' href='" . $paginate_url . "'>x</a>&nbsp;</span>";
else
    $categoryName = "";


$owner_id = intval($this->input->get("owner_id"));

if ($owner_id > 0)
    $ownerName = "&nbsp;&nbsp;&nbsp;<span class='badge bg-red'>&nbsp;" . ucfirst(Text::output($this->mUserModel->getUserNameById($owner_id))) . "&nbsp;&nbsp;<a style='color:#fff !important;' href='" . $paginate_url . "'>x</a>&nbsp;</span>";
else
    $ownerName = "";


$status = intval($this->input->get("status"));

if ($status > 0)
    $statusName = "&nbsp;&nbsp;&nbsp;<span class='badge bg-green'>&nbsp;" . Translate::sprint("My stores") . "&nbsp;&nbsp;<a style='color:#fff !important;' href='" . $paginate_url . "'>x</a>&nbsp;</span>";
else
    $statusName = "";
?>
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

        <div class="row">
            <div class="col-xs-12">
                <div class="box box-solid">
                    <div class="box-header">

                        <div class="box-title" style="width : 100%;">

                            <div class=" row ">
                                <div class="pull-left col-md-8">
                                    <b><?= Translate::sprint($h1_title) ?></b> <?= $categoryName ?><?= $ownerName ?><?= $statusName ?>
                                </div>
                                <div class="pull-right col-md-4">
                                    <a href="<?= admin_url("store/create") ?>">
                                        <button type="button" data-toggle="tooltip"
                                                title="<?= Translate::sprint("Create new store", "") ?> "
                                                class="btn btn-primary btn-sm pull-right"><span
                                                    class="glyphicon glyphicon-plus"></span></button>
                                    </a>

                                    <form method="get" action="<?php echo isset($paginate_url) ? $paginate_url : admin_url("store/all_stores") ; ?>">

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
                                <!--  DENY ACCESS TO ROLE "GUEST" -->
                            </div>
                        </div>

                    </div>
                    <!-- /.box-header -->
                    <div class="box-body table-responsive">
                        <table id="example2" class="table table-bordered table-hover">
                            <thead>
                            <tr>
                                <th><?= Translate::sprint("Image") ?></th>
                                <th><?= Translate::sprint("Name") ?></th>
                                <th><?= Translate::sprint("Owner") ?></th>
                                <th><?= Translate::sprint("Category") ?></th>
                                <th><?= Translate::sprint("Status") ?></th>
                                <th hidden><?= Translate::sprint("Wishlist") ?></th>
                                <th><?= Translate::sprint("Rating") ?></th>
                                <th><?= Translate::sprint("Reviews") ?> </th>
                                <th><?= Translate::sprint("Action") ?></th>
                            </tr>
                            </thead>
                            <tbody>

                            <?php if (!empty($stores)) { ?>

                                <?php foreach ($stores AS $store) { ?>

                                    <?php


                                    $token = $this->mUserBrowser->setToken(Text::encrypt($store['id_store']));

                                    ?>
                                    <tr class="store_<?= $token ?>" role="row" class="odd">

                                        <td>
                                            <?php

                                            try {


                                                if (!is_array($store['images']))
                                                    $images = json_decode($store['images'], JSON_OBJECT_AS_ARRAY);
                                                else
                                                    $images = $store['images'];


                                                if (isset($images[0])) {
                                                    $images = $images[0];
                                                    if (isset($images['100_100']['url'])) {
                                                        echo '<img src="' . $images['100_100']['url'] . '"width="50" height="50" alt="Product Image">';
                                                    } else {
                                                        echo '<img src="' . base_url("views/skin/backend/images/def_logo.png") . '"width="50" height="50" alt="Product Image">';
                                                    }
                                                } else {
                                                    echo '<img src="' . base_url("views/skin/backend/images/def_logo.png") . '"width="50" height="50" alt="Product Image">';
                                                }

                                            } catch (Exception $e) {
                                                $e->getMessage();
                                                echo '<img src="' . base_url("views/skin/backend/images/def_logo.png") . '"width="50" height="50" alt="Product Image">';
                                            }

                                            ?>
                                        </td>

                                        <td>
                                            <span style="font-size: 14px"><?= Text::output($store['name']) ?></span>
                                            <?php if ($store['featured'] == 1): ?>
                                                &nbsp;&nbsp;<span class="badge bg-blue-active"
                                                                  style="font-size: 10px;text-transform: uppercase"><i
                                                            class="mdi mdi-check"></i>&nbsp;<?= Translate::sprint("Featured") ?></span>
                                            <?php endif; ?><br>
                                            <i class="mdi mdi-map-marker"></i>&nbsp;&nbsp;
                                            <span style="font-size: 11px"><?= Text::output($store['address']) ?></span>
                                        </td>

                                        <td>
                                            <a href="<?=empty($status)? admin_url("store/all_stores?owner_id=" . $store['user_id']) : admin_url("store/stores?owner_id=" . $store['user_id']) ?>"><u><?= ucfirst($this->mUserModel->getUserNameById($store['user_id'])) ?></u></a>

                                            <?php if(GroupAccess::isGranted("user",MANAGE_USERS)): ?>
                                                &nbsp;&nbsp;<a target="_blank" href="<?=admin_url("user/edit?id=".$store['user_id'])?>"><i class="mdi mdi-open-in-new"></i></a>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="<?=empty($status)? admin_url("store/all_stores?category_id=" . $store['category_id']) :  admin_url("store/stores?category_id=" . $store['category_id'])  ?>"><u><?= Translate::sprint(Text::output($this->mStoreModel->getCatName($store['category_id']))) ?></u></a>
                                        </td>
                                        <td>

                                            <?php

                                            if ($store['status'] == 1) {
                                                echo '<span class="badge bg-green">' . Translate::sprint("Enabled") . '</span>';
                                            } else if ($store['status'] == 0) {
                                                echo '<span class="badge bg-red">' . Translate::sprint("Disabled") . '</span>';
                                            } else if ($store['status'] == -1) {
                                                echo '<span class="badge bg-red">' . Translate::sprint("Blocked") . '</span>';
                                            }

                                            ?>


                                        </td>

                                        <td hidden >
                                            <span data-toggle="tooltip" title="<?=$store['wishlist']?> peoples have saved this store on bookmark" class="badge bg-red-active">
                                                <i class="mdi mdi-heart"></i>&nbsp;&nbsp; <?=$store['wishlist']?>
                                            </span>
                                        </td>


                                        <td>
                                            <span style="font-size: 12px"><?php if (!empty($store['votes'])) {
                                                    echo round($store['votes'], 2) . " /5";
                                                } else {
                                                    echo " 0 ";
                                                } ?> </span>

                                        </td>
                                        <td>
                                            <a href="<?= admin_url("store/reviews?id=" . $store['id_store']) ?>">
                                                <?= Translate::sprint("Reviews", "") ?>
                                            </a>
                                        </td>

                                        <!--  DENY ACCESS TO ROLE "GUEST" -->

                                        <td align="center" >

                                            <?php
                                            if (GroupAccess::isGranted('store', MANAGE_STORES)) {
                                                if ($store['verified'] == 1) {
                                                    if ($store['status'] == 1) {
                                                        echo ' <a href="' . site_url("ajax/store/status?id=" . $store['id_store']) . '" data-toggle="tooltip" title="Disable" class="linkAccess btn btn-default"><i class="fa fa-times" aria-hidden="true"></i></a>';
                                                    } else if ($store['status'] == 0) {
                                                        echo ' <a href="' . site_url("ajax/store/status?id=" . $store['id_store']) . '" data-toggle="tooltip" title="Enable" class="linkAccess btn btn-default"><i class="fa fa-check" aria-hidden="true"></i></a> ';
                                                    }
                                                } else {
                                                    echo ' <a href="' . site_url("ajax/store/verify?id=" . $store['id_store']) . '&accept=1" class="linkAccess btn btn-default"><i class="text-white mdi mdi-thumb-up" aria-hidden="true"></i></a> ';
                                                    echo ' <a href="' . site_url("ajax/store/verify?id=" . $store['id_store']) . '&accept=0" class="linkAccess btn btn-default"><i class="text-white fa fa-times" aria-hidden="true"></i></a> ';
                                                }
                                            }




                                            ?>


                                          <?php if ($store['user_id'] != $this->mUserBrowser->getData("id_user") && GroupAccess::isGranted('store', EDIT_STORE)) { ?>
                                                <a href="<?= admin_url("store/view?id=" . $store['id_store']) ?>">
                                                    <button type="button" data-toggle="tooltip" title="Detail"
                                                            class="btn btn-sm"><i
                                                                class="fa fa-eye"></i></button>
                                                </a>
                                            <?php } else if (GroupAccess::isGranted('store', EDIT_STORE)) { ?>
                                                <a href="<?= admin_url("store/edit?id=" . $store['id_store']) ?>">
                                                    <button type="button" data-toggle="tooltip" title="Update"
                                                            class="btn btn-sm"><span
                                                                class="glyphicon glyphicon-edit"></span></button>
                                                </a>
                                            <?php } ?>


                                            <?php if (GroupAccess::isGranted('store', DELETE_STORE)): ?>
                                                <a href="#" class="delete" data-id="<?=$store['id_store']?>">
                                                    <button type="button" class="btn btn-sm"><span
                                                                class="glyphicon glyphicon-trash"></span></button>
                                                </a>
                                            <?php endif; ?>

                                        </td>

                                        <!--  -->


                                    </tr>
                                <?php } ?>


                            <?php } else { ?>
                                <tr>
                                    <td colspan="8" align="center">
                                        <div
                                                style="text-align: center"><?= Translate::sprint("No data found", "") ?></div>
                                    </td>
                                </tr>

                            <?php } ?>
                            </tbody>
                        </table>

                        <div class="row">
                            <div class="col-sm-5">
                                <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">

                                </div>

                            </div>
                            <div class="col-sm-7">
                                <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">

                                    <?php

                                    echo $pagination->links(array(
                                        "status" => intval($this->input->get("status")),
                                        "search" => $this->input->get("search"),
                                        "category_id" => intval($this->input->get("category_id")),
                                        "owner_id" => intval($this->input->get("owner_id")),
                                    ), current_url());

                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->


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

$script = $this->load->view('store/backend/html/scripts/stores-script',NULL,TRUE);
TemplateManager::addScript($script);

?>
