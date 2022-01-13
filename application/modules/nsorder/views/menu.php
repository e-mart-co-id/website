<?php

$uri_m = $this->uri->segment(2);
$uri_parent = $this->uri->segment(3);
$uri_child = $this->uri->segment(4);

$this->load->model("nsorder/nsorder_model", "mOrder");
$pendingOrdersCountOwner = $this->mOrder->countPendingOrders(TRUE);
$pendingOrdersCountOwner = isset($pendingOrdersCountOwner[Tags::COUNT]) ? $pendingOrdersCountOwner[Tags::COUNT] : 0;

$pendingOrdersCountAdmin = $this->mOrder->countPendingOrders();
$pendingOrdersCountAdmin = isset($pendingOrdersCountAdmin[Tags::COUNT]) ? $pendingOrdersCountAdmin[Tags::COUNT] : 0;

?>

<?php if (GroupAccess::isGranted('nsorder')) : ?>
    <li class="treeview <?php if ($uri_m == "nsorder") echo "active"; ?>">
        <a href="<?= admin_url("nsorder/orders") ?>"><i class="mdi mdi-cart-outline"></i> &nbsp;
            <span><?= Translate::sprint("Orders") ?></span>

            <span class="pull-right-container">
                      <i class="fa fa-angle-left pull-right"></i>
            </span>


        </a>

        <ul class="treeview-menu">

            <?php if (GroupAccess::isGranted('nsorder', MANAGE_ORDER_CONFIG_ADMIN)): ?>
                <li class="<?php if ($uri_m == "nsorder" && $uri_parent == "all_orders") echo "active"; ?>">
                    <a href="<?= admin_url("nsorder/all_orders") ?>"><i class="mdi mdi-cart-outline"></i>
                        &nbsp;<?= Translate::sprint("All orders") ?>
                        <?php if ($pendingOrdersCountAdmin > 0): ?>
                            <small class="badge pull-right bg-yellow"><?= $pendingOrdersCountAdmin ?></small>
                        <?php endif; ?>
                    </a>
                </li>
            <?php endif; ?>

            <li class="<?php if ($uri_m == "nsorder" && $uri_parent == "my_orders") echo "active"; ?>">
                <a href="<?= admin_url("nsorder/my_orders") ?>"><i class="mdi mdi-cart-outline"></i>
                    &nbsp;<?= Translate::sprint("My Orders") ?>
                    <?php if ($pendingOrdersCountOwner > 0): ?>
                        <small class="badge pull-right bg-yellow"><?= $pendingOrdersCountOwner ?></small>
                    <?php endif; ?>
                </a></li>

            <?php if (GroupAccess::isGranted('nsorder', MANAGE_ORDER_CONFIG_ADMIN)): ?>
                <li class="<?php if ($uri_m == "nsorder" && $uri_parent == "commission") echo "active"; ?>">
                    <a href="<?= admin_url("nsorder/commission") ?>"><i class="mdi mdi-percent"></i>
                        &nbsp;<?= Translate::sprint("Commission") ?>
                    </a></li>
            <?php endif; ?>

            <?php if (GroupAccess::isGranted('nsorder')): ?>
                <li class="<?php if ($uri_parent == "payout") echo "active"; ?>">
                    <a href="<?= admin_url("nsorder/payouts") ?>"><i class="mdi mdi-cash-100"></i>
                        &nbsp;<span> <?= Translate::sprint("Payouts") ?></span></a>
                </li>
            <?php endif; ?>


            <?php if (GroupAccess::isGranted('nsorder', MANAGE_ORDER_STATUS_LIST_ADMIN)): ?>

                <li class="<?php if ($uri_m == "nsorder" && $uri_parent == "order_status") echo "active"; ?>">
                    <a href="<?= admin_url("nsorder/order_status") ?>"><i class="mdi mdi-format-list-bulleted"></i>
                        &nbsp;<?= Translate::sprint("Order status") ?></a></li>
            <?php endif; ?>




        </ul>
    </li>
<?php endif; ?>
