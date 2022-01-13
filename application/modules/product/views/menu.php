<?php

$uri_m = $this->uri->segment(2);
$uri_parent = $this->uri->segment(3);
$uri_child = $this->uri->segment(4);


?>
<?php if (GroupAccess::isGranted('product')) : ?>
    <li class="treeview <?php if ($uri_m == "product") echo "active"; ?>">
        <a href="<?= admin_url("product/products") ?>"><i class="mdi mdi-basket "></i> &nbsp;
            <span><?= Translate::sprint("Manage Products") ?></span>
            <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
        </span>
        </a>

        <ul class="treeview-menu">
            <?php if (GroupAccess::isGranted('product', MANAGE_PRODUCTS)) : ?>
                <li class="<?php if ($uri_m == "product" && $uri_parent == "all_products") echo "active"; ?>">
                    <a href="<?= admin_url("product/all_products") ?>"><i class="mdi mdi-format-list-bulleted"></i>
                        &nbsp;<?= Translate::sprint("All Products") ?>

                        <?php
                        $c = $this->mProductModel->getUnverifiedProductsCount();
                        if ($c > 0): ?>
                            <small class="badge pull-right bg-yellow"><?= $c ?></small>
                        <?php endif; ?>

                    </a></li>
            <?php endif; ?>

            <li class="<?php if ($uri_m == "product" && $uri_parent == "my_products") echo "active"; ?>">
                <a href="<?= admin_url("product/my_products") ?>"><i class="mdi mdi-format-list-bulleted"></i>
                    &nbsp;<?= Translate::sprint("My Products") ?></a></li>

            <?php if (GroupAccess::isGranted('product', ADD_PRODUCT)): ?>
                <li class="<?php if ($uri_m == "product" && $uri_parent == "add") echo "active"; ?>">
                    <a href="<?= admin_url("product/add") ?>"><i class="mdi mdi-plus-box  "></i>
                        &nbsp;<?= Translate::sprint("Add product") ?></a></li>
            <?php endif; ?>

        </ul>
    </li>
<?php endif; ?>
