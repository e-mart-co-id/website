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
                                    <b><?= Translate::sprint("Status") ?></b>
                                </div>
                                <!--  DENY ACCESS TO ROLE "GUEST" -->
                            </div>
                        </div>

                    </div>
                    <!-- /.box-header -->
                    <div class="box-body table-responsive">
                        <table id="" class="table table-bordered table-hover">
                            <thead>
                            <tr>
                                <th width="5%"><?=_lang("Order")?></th>
                                <th width="50%"><?= Translate::sprint("Label") ?></th>
                                <th width="25%"><?= Translate::sprint("Action") ?></th>
                            </tr>
                            </thead>
                            <tbody id="list">

                            <?php if (!empty($list)) : ?>

                                <?php foreach ($list as $key => $value): ?>

                                    <?php
                                        $extras = json_decode($value['extras'],JSON_OBJECT_AS_ARRAY);
                                    ?>

                                <tr class="line" data-id="<?=$value['id']?>">
                                    <td><span class="cursor-pointer" style="font-size: 22px"><i class="mdi mdi-menu text-gray"></i></span></td>
                                    <td><span class="badge" style="background: <?=isset($extras['color'])?$extras['color']:"gray"?>"><?=ucfirst(_lang($value['label']))?></span></td>
                                    <td>
                                        <a class="btn" href="<?=admin_url("nsorder/order_status_edit?id=".$value['id'])?>"><span class="glyphicon glyphicon-edit"></span></a>

                                    </td>
                                </tr>

                                <?php endforeach;?>

                            <?php else: ?>
                                <tr>
                                    <td colspan="3" align="center">
                                        <div
                                            style="text-align: center"><?= Translate::sprint("No data found", "") ?></div>
                                    </td>
                                </tr>

                            <?php endif; ?>
                            </tbody>
                        </table>

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

<div class="modal fade" id="alert">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">

                <h4 class="modal-title"><?=_lang("Alert!")?></h4>
            </div>
            <div class="modal-body">

                <p class="text-red"> <?= Translate::sprint("Are you sure to do this operation?") ?></p>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left"
                        data-dismiss="modal"><?= Translate::sprint("Cancel", "Cancel") ?></button>
                <button type="button" id="apply"
                        class="btn btn-flat btn-primary"><?= Translate::sprint("Confirm") ?></button>
            </div>
        </div>

        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>


<?php

$script = $this->load->view('nsorder/backend/order_status/scripts/list-script', NULL, TRUE);
TemplateManager::addScript($script);

?>
