<?php

$uri_m = $this->uri->segment(2);
$uri_parent = $this->uri->segment(3);
$uri_child = $this->uri->segment(4);


$pending_users = $this->mDeliveryModel->get_pending_users();

?>


<?php if (GroupAccess::isGranted('delivery')) : ?>
<li class="treeview <?php if ($uri_m == "delivery") echo "active"; ?>">
    <a href="#"><i class="mdi mdi-package-variant-closed"></i> &nbsp;
        <span><?= Translate::sprint("Delivery") ?></span>
       <!-- <?php /*if ($pending_users > 0): */?>
            <small class="badge pull-right  bg-yellow " ><?/*= $pending_users */?></small>
        <?php /*else: */?>

            <span class="pull-right-container" style="right: 30px">
                <i class="fa fa-angle-left pull-right"></i>
            </span>
        --><?php /*endif; */?>

        <?php if(defined("DEMO") && DEMO == true): ?>
            &nbsp;&nbsp;<small class="badge   bg-yellow " >Plugin</small>
        <?php endif; ?>

        <span class="pull-right-container">
                      <i class="fa fa-angle-left pull-right"></i>
                    </span>

    </a>

    <ul class="treeview-menu">


        <?php if (GroupAccess::isGranted('user', MANAGE_USERS)) : ?>

            <li class="<?php if ($uri_parent == "delivery") echo "active"; ?>">
                <a href="<?= admin_url("delivery/users") ?>">
                    <i class="mdi mdi-account-multiple"></i>
                    &nbsp;<span> <?= Translate::sprint("Delivery users") ?></span>

                    <?php if ($pending_users > 0): ?>
                        <small class="badge pull-right  bg-yellow"><?= $pending_users ?></small>
                    <?php endif; ?>
                </a>
            </li>

        <?php endif; ?>

        <?php if (GroupAccess::isGranted('delivery', MANAGE_DELIVERY_PAYOUTS)) : ?>

            <li class="<?php if ($uri_parent == "delivery") echo "active"; ?>">
                <a href="<?= admin_url("delivery/payouts") ?>"><i class="mdi mdi-cash-100"></i>
                    &nbsp;<span> <?= Translate::sprint("Delivery payouts") ?></span></a>
            </li>

        <?php endif; ?>





        <?php if (GroupAccess::isGranted('delivery', MANAGE_DELIVERY_USERS)) : ?>
            <li class="<?php if ($uri_parent == "delivery") echo "active"; ?>">
                <a href="<?= admin_url("delivery/delivery_config") ?>"><i class="mdi mdi-cog-outline"></i>
                    &nbsp;<span> <?= Translate::sprint("Delivery config") ?></span></a>
            </li>
        <?php endif; ?>



        <?php if (DEMO): ?>
            <li class="" style="background-color: <?=DASHBOARD_COLOR?>;color: white !important;">
                <a style="color: white !important;" target="_blank" href="https://codecanyon.net/item/delivery-for-dealfly-delivery-for-dealfly-order-tracking-real-time-native-application/31825506">
                    <i class="mdi mdi-cart-outline"></i> &nbsp;<span> <?= Translate::sprint("Buy it now") ?></span>
                </a>
            </li>
            <!--https://apps.apple.com/us/app/nearbystores/id1422430308?ls=1-->
        <?php endif; ?>

    </ul>
    </li>
<?php endif; ?>
