<?php

$users = $data[Tags::RESULT];
$pagination = $data[Tags::PAGINATION];

$typeAuth = $this->mUserBrowser->getData("typeAuth");

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

                            <div class="row ">

                                <div class="pull-left col-md-8">
                                    <B><?= Translate::sprint("Users") ?></B>&nbsp;&nbsp;

                                    <?php
                                        $query_uri = $_SERVER['QUERY_STRING'];
                                    ?>

                                    <?php if($query_uri != ""): ?>
                                        <a href="<?=admin_url("delivery/users")?>"><span class="badge bg-red"><i class="mdi mdi-close"></i>&nbsp;&nbsp;<?=_lang("Clear filter")?></span></a>
                                    <?php endif; ?>
                                </div>

                                <div class="pull-right col-md-4">

                                    <form method="get" action="<?= admin_url("delivery/users") ?>">
                                        <div class="input-group input-group-sm">
                                            <input class="form-control" size="30" name="search"
                                                   placeholder="<?= Translate::sprint("Search") ?>" type="text"
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
                    <div class="box-body  table-responsive">

                        <div class="table-responsive">
                            <table id="example2" class="table table-bordered table-hover">
                                <thead>
                                <tr>
                                    <!-- <th>ID</th>-->
                                    <th><?= Translate::sprint("Photo", "") ?></th>
                                    <th><?= Translate::sprint("Name", "") ?></th>
                                    <th><?= Translate::sprint("Phone", "") ?></th>
                                    <th><?= Translate::sprint("Status", "") ?></th>
                                    <th><?= Translate::sprint("Delivered orders", "") ?></th>
                                    <th><?= Translate::sprint("Balance") ?></th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>

                                <?php if (!empty($users)) { ?>

                                    <?php foreach ($users AS $user) { ?>
                                        <tr>


                                            <td>

                                                <?php

                                                $image = "";

                                                if (isset($user['images'][0]['200_200']['url'])) {
                                                    $image = $user['images'][0]['200_200']['url'];
                                                } else {
                                                    $image = base_url("views/skin/backend/images/profile_placeholder.png");
                                                }


                                                ?>

                                                <div class="image-container-40"
                                                     style="background-image: url('<?= $image ?>');background-size: auto 100%;
                                                             background-position: center;">
                                                    <img class="direct-chat-img invisible" src="<?= $image ?>"
                                                         alt="Message User Image">
                                                </div>

                                            </td>
                                            <td>
                                                <span style="font-size: 13px"
                                                      id="trigger"><?= htmlspecialchars($user['name']) ?></span>
                                            </td>

                                            <td>
                                                <?= $user['telephone'] ?>
                                            </td>

                                            <td>
                                                <?php

                                                if ($user['confirmed'] == 0) {
                                                    echo ' <span class="badge bg-yellow">' . Translate::sprint("No-Confirmed", "") . '</span>';
                                                } else {
                                                    echo ' <span class="badge bg-green">' . Translate::sprint("Confirmed", "") . '</span>';
                                                }

                                                ?>


                                                <?php

                                                if ($user['phone_verified'] == 1) {
                                                    echo ' <span class="badge bg-green">' . Translate::sprint("Phone verified", "") . '</span>';
                                                }

                                                ?>
                                            </td>

                                            <td>
                                                <?=
                                                    Translate::sprintf("%s order(s)", array(
                                                        $this->mDeliveryModel->delivered_orders($user['id_user'])
                                                    ))
                                                ?>
                                            </td>

                                            <td>

                                                <?php

                                                $start = date("Y-m",time())."-01 00:00:00";
                                                $end = date("Y-m-t",time())." 23:59:59";

                                                $orders = $this->mDeliveryModel->getOrdersQuery(
                                                    $start,
                                                    $end,
                                                    $user['id_user']
                                                );

                                                $amount = 0;

                                                foreach ($orders as $order){
                                                    $amount = $order['delivery_commission'] + $amount;
                                                }

                                                ?>
                                                <?=
                                                    Currency::parseCurrencyFormat($amount, PAYMENT_CURRENCY);
                                                ?>
                                            </td>

                                            <td align="right">


                                                <?php

                                                if ($user['status'] == 1) {
                                                    echo '<span class="badge bg-green">' . Translate::sprint("Enabled") . '</span>&nbsp;&nbsp;';
                                                }else if ($user['status'] == -1) {
                                                    echo '<span class="badge bg-red">' . Translate::sprint("Disabled") . '</span>&nbsp;&nbsp;';
                                                }else if ($user['status'] == 0) {
                                                    echo '<span class="badge bg-orange">' . Translate::sprint("Pending") . '</span>&nbsp;&nbsp;';
                                                }

                                                ?>

                                                <?php

                                                if (GroupAccess::isGranted('user')) {
                                                    if ($user['status'] == 0) {
                                                        echo ' <a href="' . site_url("ajax/delivery/accept?id=" . $user['id_user']) . '"  data-toggle="tooltip" title="Disable" class="linkAccess btn btn-default bg-green"><i class="fa fa-times" aria-hidden="true"></i> '._lang("Accept").'</a>';
                                                        echo ' <a href="' . site_url("ajax/delivery/decline?id=" . $user['id_user']) . '"  data-toggle="tooltip" title="Disable" class="linkAccess btn btn-default bg-orange"><i class="fa fa-times" aria-hidden="true"></i> '._lang("Decline").'</a>';
                                                    }
                                                }

                                                ?>

                                                <?php

                                                if (GroupAccess::isGranted('user')) {
                                                    if ($user['status'] == 1) {
                                                        echo ' <a href="' . site_url("ajax/user/access?id=" . $user['id_user']) . '"  data-toggle="tooltip" title="Disable" class="linkAccess btn btn-default"><i class="fa fa-times" aria-hidden="true"></i></a>';
                                                    } else if ($user['status'] == -1) {
                                                        echo ' <a href="' . site_url("ajax/user/access?id=" . $user['id_user']) . '"  data-toggle="tooltip" title="Enable" class="linkAccess btn btn-default"><i class="fa fa-check" aria-hidden="true"></i></a>';
                                                    }
                                                }

                                                ?>


                                                <?php if (GroupAccess::isGranted('user', EDIT_USER)): ?>
                                                    &nbsp;
                                                    <a class="btn btn-default" data-toggle="tooltip"
                                                       data="<?= $user['id_user'] ?>"
                                                       href="<?= admin_url("user/edit?id=" . $user['id_user']) ?>"
                                                       title="<?= Translate::sprint("Update profile") ?>">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                <?php endif; ?>

                                                <?php if (GroupAccess::isGranted('user', DELETE_USERS)): ?>
                                                    &nbsp;
                                                    <a href="#" class="remove btn btn-default" data-id="<?=($user['id_user'] ) ?>">
                                                        <span class="glyphicon glyphicon-trash"></span>
                                                    </a>

                                                <?php endif; ?>

                                                <?php if (GroupAccess::isGranted('messenger')): ?>
                                                    &nbsp;
                                                    <a class="btn btn-default " data-toggle="tooltip"
                                                       data="<?= $user['id_user'] ?>"
                                                       href="<?= admin_url("messenger/messages?username=" . $user['username']) ?>"
                                                       title="<?= Translate::sprint("Inbox") ?>">
                                                        <i class="fa fa-inbox"></i>
                                                    </a>
                                                <?php endif; ?>

                                            </td>
                                        </tr>
                                    <?php } ?>
                                <?php } else { ?>
                                    <tr>
                                        <td colspan="7" align="center">
                                            <div style="text-align: center"> <?= Translate::sprint("No data found") ?></div>
                                        </td>
                                    </tr>
                                <?php } ?>
                                </tbody>

                            </table>
                        </div>

                        <div class="row">
                            <div class="col-sm-12">
                                <div class="dataTables_info  pull-right" id="example2_info" role="status"
                                     aria-live="polite">
                                    <?php

                                    echo $pagination->links(array(
                                        "search" => $this->input->get("search"),
                                        "filter" => $this->input->get("filter"),
                                    ), admin_url("delivery/users"));

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

<?php if (GroupAccess::isGranted('user')): ?>

    <div class="modal fade" id="modal-default">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"></h4>
                </div>
                <div class="modal-body">

                    <div class="row">

                        <div style="text-align: center">
                            <h3 class="text-red"><?= Translate::sprint("Are you sure?") ?></h3>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" id="_apply"
                            class="btn btn-flat btn-primary pull-right"><?= Translate::sprint("Yes") ?></button>
                    <button type="button" class="btn btn-flat btn-default pull-right"
                            data-dismiss="modal"><?= Translate::sprint("No") ?></button>
                </div>
            </div>

            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <div class="modal fade" id="switcher">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"></h4>
                </div>
                <div class="modal-body">

                    <div class="callout callout-info">
                        <p> <?= Translate::sprint("You should know that you can sign all stores, events and products to another owner by selecting the owner from the list above") ?></p>
                    </div>

                    <div class="form-group">
                        <label><?= Translate::sprint("Select owner") ?></label>
                        <select id="select_owner" name="select_owner" class="form-control select2">
                            <option selected="" value="0">---- <?= Translate::sprint("Select") ?></option>
                        </select>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left"
                            data-dismiss="modal"><?= Translate::sprint("Cancel", "Cancel") ?></button>
                    <button type="button" id="apply"
                            class="btn btn-flat btn-primary"><?= Translate::sprint("Apply and delete") ?></button>
                </div>
            </div>

            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>


    <?php

    $script = $this->load->view('user/backend/html/scripts/users-script', NULL, TRUE);
    TemplateManager::addScript($script);

    ?>


<?php endif; ?>



