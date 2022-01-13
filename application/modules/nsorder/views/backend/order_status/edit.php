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

        <div class="row" id="form">
            <div class="col-sm-6">
                <div class="box box-solid">
                    <div class="box-header">

                        <div class="box-title">
                            <b><?= Translate::sprint("Edit status") ?></b>
                        </div>

                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="row">
                            <div class="col-sm-12">

                                <div class="form-group col-md-12">
                                    <label><?=_lang("Label")?></label>
                                    <input class="form-control" type="text" id="label" placeholder="<?=_lang("Enter label...")?>" value="<?=$data['label']?>">
                                </div>

                                <?php

                                    $extras = json_decode($data['extras'],JSON_OBJECT_AS_ARRAY);

                                ?>

                                <div class="form-group col-md-12">
                                    <label><?=_lang("Color")?></label>
                                    <input class="form-control colorpicker" type="text" id="color" value="<?=isset($extras['color'])?$extras['color']:""?>" placeholder="<?=_lang("Enter color...")?>">
                                </div>

                            </div>

                            <div class="col-sm-6">

                            </div>
                        </div>
                    </div>
                    <!-- /.box-body -->
                    <div class="box-footer">
                        <button id="save" class="btn btn-primary btn-flat pull-right"><i class="mdi mdi-content-save-outline"></i>&nbsp;&nbsp;<?=_lang("Save changes")?></button>
                    </div>
                </div>
                <!-- /.box -->
            </div>

        </div>
        <!-- /.row -->
    </section>
    <!-- /.content -->
</div>
<?php

$data['status']= $data;
$script = $this->load->view('nsorder/backend/order_status/scripts/edit-script',$data,TRUE);
TemplateManager::addScript($script);

?>
