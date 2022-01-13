<?php
$transactions = $result[Tags::RESULT];
$pagination = $result[Tags::PAGINATION];
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

            <div class="col-sm-12">
                <div class="callout callout-warning">
                    <p><i class="mdi mdi-information-outline"></i>&nbsp;&nbsp;<?php echo Translate::sprint("The delivery payout will be generated at the end of every month automatically"); ?> </p>
                </div>
            </div>

        </div>

        <div class="row transactions">
            <div class="col-xs-12">
                <div class="box box-solid">
                    <div class="box-header">
                        <div class="box-title" style="width : 100%;">
                            <div class="row">
                                <div class="pull-left col-md-8">
                                    <b><?= Translate::sprint("Payouts") ?></b>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body table-responsive">
                        <table id="payouts" class="table table-bordered table-hover">
                            <thead>
                            <tr>
                                <th width="5%"><strong>#</strong></th>
                                <th><?= Translate::sprint("Amount") ?></th>
                                <th><?= Translate::sprint("Delivery man") ?></th>
                                <th><?= Translate::sprint("Method", "") ?></th>
                                <th><?= Translate::sprint("Note") ?></th>
                                <th><?= Translate::sprint("Status") ?></th>
                                <th><?= Translate::sprint("Date") ?></th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if (count($transactions) > 0) { ?>

                                <?php foreach ($transactions as $value): ?>

                                    <tr>
                                        <td><strong><?= $value['id'] ?></strong></td>
                                        <td><?= Currency::parseCurrencyFormat($value['amount'], $value['currency']) ?></td>

                                        <td>
                                            <a href="<?= admin_url('user/edit?id=' . $value['user_id']) ?>"><?= $this->mUserModel->getUserNameById($value['user_id']) ?></a>
                                        </td>
                                        <td><?= $value['method'] ?></td>
                                        <td> <?= $value['note'] ?></td>

                                        <td>

                                            <?php

                                            if ($value['status'] == "processing")
                                                echo "<span class='badge bg-yellow'>" . Translate::sprint('Processing') . "</span>";
                                            else if ($value['status'] == "paid")
                                                echo "<span class='badge bg-green'>" . Translate::sprint('Paid') . "</span>";
                                            else if ($value['status'] == "cancel")
                                                echo "<span class='badge bg-red'>" . Translate::sprint('Canceled') . "</span>";
                                            else
                                                echo $value['status'];

                                            ?>

                                        </td>
                                        <td><?= $value['created_at'] ?></td>
                                        <td>
                                            <?php if (GroupAccess::isGranted('delivery',MANAGE_DELIVERY_PAYOUTS)): ?>
                                                <a href="<?= admin_url("delivery/editPayout?id=" . $value['id']) ?>">
                                                    <button type="button" data-toggle="tooltip" title=""
                                                            class="btn btn-sm" data-original-title="Update profile">
                                                        <span class="glyphicon glyphicon-edit"></span></button>
                                                </a>
                                                <a data-id="<?= $value['id'] ?>" href="#" class="deletePayout">
                                                    <button type="button" data-toggle="tooltip" title=""
                                                            class="btn btn-sm" data-original-title="Delete user"><span
                                                                class="glyphicon glyphicon-trash"></span></button>
                                                </a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>

                                <?php endforeach; ?>

                            <?php } ?>


                            </tbody>
                        </table>

                        <div class="row">
                            <div class="col-sm-12 pull-right">
                                <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">

                                    <?php

                                    echo $pagination->links(array(
                                        "page" => $this->input->get("page"),
                                        "status" => $this->input->get("status"),
                                    ), admin_url("delivery/payouts"));

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

$script = $this->load->view('delivery/backend/payouts/scripts/payout-script', NULL, TRUE);
TemplateManager::addScript($script);

?>
