<?php


$list = $products[Tags::RESULT];
$pagination = $products["pagination"];

// this fields serve to filter products by status
$status = $this->input->get("status");
$filterBy = $this->input->get("filterBy");

/*if ($status == 1)
    $statusName = "&nbsp;&nbsp;&nbsp;<span class='badge bg-green'>&nbsp;" . Translate::sprint("My Products") . "&nbsp;&nbsp;<a style='color:#fff !important;' href='" . admin_url("product/products") . "'>x</a>&nbsp;</span>";
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
                                        <b><?= Translate::sprint("Products") ?></b> <?= $filerN ?>
                                    </div>
                                    <div class="pull-right col-md-4">
                                        <?php if (GroupAccess::isGranted('product', ADD_PRODUCT)) : ?>
                                            <a href="<?= admin_url("product/add") ?>">
                                                <button type="button" data-toggle="tooltip"
                                                        title="<?= Translate::sprint("Create new product", "") ?> "
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
                                    <th width="40%"><?= Translate::sprint("Name", "") ?></th>
                                    <th><?= Translate::sprint("Owner", "") ?></th>
                                    <th><?= Translate::sprint("Status", "") ?></th>
                                    <th hidden><?= Translate::sprint("Views", "") ?></th>
                                    <th hidden><?= Translate::sprint("Downloads", "") ?></th>
                                    <th><?= Translate::sprint("Price", "") ?></th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>

                                <?php if (count($list)) { ?>
                                    <?php foreach ($list as $product) { ?>


                                        <tr>
                                            <td>
                                                <?php

                                                try {

                                                    if (!is_array($product['images']))
                                                        $images = json_decode($product['images'], JSON_OBJECT_AS_ARRAY);
                                                    else
                                                        $images = $product['images'];

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
                                                <span style="font-size: 14px"><?= Text::output($product['name']) ?></span>
                                                <?php if ($product['featured'] == 1): ?>
                                                    &nbsp;&nbsp;<span class="badge bg-blue-active"
                                                                      style="font-size: 10px;text-transform: uppercase"><i
                                                                class="mdi mdi-check"></i>&nbsp;<?= Translate::sprint("Featured") ?></span>
                                                <?php endif; ?><br>
                                                <span style="font-size: 12px;">
                                                <?php
                                                echo '<i class="mdi mdi-map-marker"></i>&nbsp;<a href="' . admin_url("store/view?id=" . $product['store_id']) . '"> ' . $this->mStoreModel->getStoreName($product['store_id']) . '</a>';
                                                ?>
                                            </span>
                                            </td>

                                            <td>
                                                <?php if (GroupAccess::isGranted('product', EDIT_PRODUCT)): ?>
                                                    <a style="font-size: 11px"
                                                       href="<?= admin_url("user/edit?id=" . $product['user_id']) ?>"><u><?= ucfirst($this->mUserModel->getUserNameById($product['user_id'])) ?></u></a>
                                                <?php endif; ?>
                                            </td>
                                            <td>

                                                <?php if ($product['status'] == 0) : ?>
                                                    <a href="<?php echo current_url() . "?status=" . $product['status'] . "&filterBy=Unpublished"; ?>">
                                                        <span class="badge bg-yellow" data-toggle="tooltip"
                                                              title="<?= _lang("Must be approved by the admin") ?>"><iclass="mdi mdi-history"></i>
                                                            &nbsp; <?php echo Translate::sprint("Unpublished") ?>  &nbsp;&nbsp;</span>
                                                    </a>
                                                <?php elseif ($product['status'] == 1): ?>

                                                    <a href="<?php echo current_url() . "?status=" . $product['status'] . "&filterBy=Published"; ?>">
                                                    <span class="badge bg-green"><i
                                                                class="mdi mdi-history"></i> &nbsp;  <?php echo Translate::sprint("Published") ?> &nbsp;&nbsp;</span>
                                                    </a>

                                                <?php endif; ?>

                                            </td>

                                            <td hidden>
                                                <span data-toggle="tooltip" title="<?=$product['views']?> peoples have watched this item" class="badge bg-light-blue"> <i class="mdi mdi-eye"></i>&nbsp;&nbsp; <?=$product['views']?> </span>
                                            </td>

                                            <td hidden>
                                                <span data-toggle="tooltip" title="<?=$product['downloads']?> peoples have downloaded pictures from this item" class="badge bg-yellow-active"> <i class="mdi mdi-download"></i>&nbsp;&nbsp; <?=$product['downloads']?> </span>
                                            </td>

                                            <td>

                                                <?php

                                                if (is_array($product['currency']))
                                                    $product['currency'] = $product['currency']['code'];

                                                if ($product['product_type'] == 'price') {


                                                    if ($product['product_value'] < $product['original_value']) {
                                                        echo '<strong class="text-red">&nbsp;' . Currency::parseCurrencyFormat($product['product_value'], $product['currency']) . '&nbsp;&nbsp;</strong><br/>';
                                                        echo '<span class="text-grey2" style="text-decoration: line-through">' . Currency::parseCurrencyFormat($product['original_value'], $product['currency']) . '</span>';
                                                    } else
                                                        echo '<strong class="text-red">&nbsp;' . Currency::parseCurrencyFormat($product['product_value'], $product['currency']) . '&nbsp;&nbsp;</strong>';

                                                } else if ($product['product_type'] == 'percent') {
                                                    echo '<strong class="text-red">&nbsp;' . intval($product['product_value']) . '% &nbsp;&nbsp;</strong>';
                                                } else {
                                                    echo '<strong class="text-red">&nbsp;' . Translate::sprint("Promotion") . '&nbsp;&nbsp;</strong>';
                                                }

                                                ?>


                                            </td>

                                            <td align="right">
                                                <?php if (GroupAccess::isGranted('product', MANAGE_PRODUCTS)) {

                                                    if ($product['verified'] == 1) {
                                                        if ($product['status'] == 1) {
                                                            echo ' <a href="' . site_url("ajax/product/changeStatus?id=" . $product['id_product']) . '" data-toggle="tooltip" title="Disable" class="linkAccess btn btn-default"><i class="fa fa-times" aria-hidden="true"></i></a>';
                                                        } else if ($product['status'] == 0) {
                                                            echo ' <a href="' . site_url("ajax/product/changeStatus?id=" . $product['id_product']) . '" data-toggle="tooltip" title="Enable" class="linkAccess btn btn-default"><i class="fa fa-check" aria-hidden="true"></i></a> ';
                                                        }
                                                    } else {
                                                        echo ' <a href="' . site_url("ajax/product/verify?id=" . $product['id_product']) . '&accept=1" class="linkAccess btn btn-default"><i class="text-white mdi mdi-thumb-up" aria-hidden="true"></i></a> ';
                                                        echo ' <a href="' . site_url("ajax/product/verify?id=" . $product['id_product']) . '&accept=0" class="linkAccess btn btn-default"><i class="text-white fa fa-times" aria-hidden="true"></i></a> ';
                                                    }


                                                    ?>

                                                <?php } ?>


                                                <?php if ($product['user_id'] == $this->mUserBrowser->getData("id_user")) : ?>
                                                    &nbsp;
                                                    <a href="<?= admin_url("product/edit?id=" . $product['id_product']) ?>"
                                                       title="<?= Translate::sprint("Edit") ?>"
                                                       class=" btn btn-default">
                                                        <span class="glyphicon glyphicon-edit"></span>

                                                    </a>
                                                <?php else : ?>
                                                    &nbsp;
                                                    <a href="<?= admin_url("product/view?id=" . $product['id_product']) ?>"
                                                       class=" btn btn-default"
                                                       title="<?= Translate::sprint("View") ?>">
                                                        <span class="glyphicon glyphicon-eye-open"></span>

                                                    </a>
                                                <?php endif; ?>

                                                <?php if ($this->mUserBrowser->getData("id_user") == $product['user_id']) { ?>
                                                    <a href="<?= site_url('ajax/product/duplicate?id=' . $product['id_product']) ?>"
                                                       class="linkAccess btn btn-default hidden"
                                                       title="<?= Translate::sprint("Duplicate") ?>"
                                                       onclick="return false;">
                                                        <span class="glyphicon glyphicon-duplicate"></span>
                                                    </a>
                                                <?php } ?>


                                                <?php if (GroupAccess::isGranted('product', DELETE_PRODUCT)): ?>
                                                    &nbsp;
                                                    <a href="#" class="remove btn btn-default"
                                                       data-id="<?= $product['id_product'] ?>"
                                                       title="<?= Translate::sprint("Delete") ?>">
                                                        <span class="glyphicon glyphicon-trash"></span>
                                                    </a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>

                                    <?php } ?>




                                <?php } else { ?>
                                    <tr>
                                        <td colspan="3"><?= Translate::sprint("No Products", "") ?></td>
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


$script = $this->load->view('product/backend/html/scripts/list-script');
TemplateManager::addScript($script);





