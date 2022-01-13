
<?php


$categories = $data['cats'];

///echo "<pre>"; print_r($categories); die();

foreach ($categories as $key => $job){

    $categories[$key]['name'] = Text::output($categories[$key]['name']);
    $categories[$key]['name'] = Translate::sprint($categories[$key]['name'],$categories[$key]['name']);

}

usort($categories,function($first, $second){
    return strtolower($first['name']) > strtolower($second['name']);
});


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

            <?php if (GroupAccess::isGranted('nsorder',MANAGE_ORDER_CONFIG_ADMIN)): ?>
                <div class="col-sm-6">
                    <div class="box  box-solid">
                        <div class="box-header">
                            <div class="box-title"><b><?= Translate::sprint("Order Categories") ?></b></div>
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body  table-bordered ">
                            <div class="table-responsive">
                                <table id="example2" class="table table-bordered table-hover">
                                    <thead>
                                    <tr>
                                        <th><?= Translate::sprint("Category") ?></th>
                                        <th><?= Translate::sprint("Custom Fields") ?></th>
                                        <th></th>
                                    </tr>
                                    </thead>
                                    <tbody>

                                    <?php if (!empty($categories)) { ?>

                                        <?php foreach ($categories AS $category) { ?>
                                            <tr>
                                                <td>
                                                  <?= Translate::sprint(Text::echo_output($category["name"])) ?>
                                                </td>
                                                <td>
                                                    <?php

                                                        if(isset($category['cf_id']) && $category['cf_id']>0){
                                                            $cf = $this->mCFManager->getCF($category['cf_id']);

                                                            if($cf != NULL)
                                                                echo $cf['label'];
                                                        }

                                                    ?>
                                                </td>
                                                <td align="right">
                                                    <?php if (GroupAccess::isGranted('nsorder')): ?>
                                                        <a href="<?= admin_url("nsorder/order_categories_edit?id=" . $category["id_category"]) ?>">
                                                            <button type="button" title="Update"
                                                                    class="btn btn-sm"><span
                                                                        class="glyphicon glyphicon-edit"></span>
                                                            </button>
                                                        </a>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    <?php } else { ?>
                                        <tr>
                                            <td colspan="4">
                                                <div style="text-align: center"> <?= Translate::sprint("No data found", "") ?></div>
                                            </td>
                                        </tr>

                                    <?php } ?>
                                    </tbody>
                                </table>
                            </div>

                        </div>
                        <!-- /.box-body -->
                    </div>
                    <!-- /.box -->
                </div>
                <!-- /.col -->
            <?php endif; ?>

        </div>
        <!-- /.row -->
    </section>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->


