<?php


$image = $category['image'];
$icon = $category['icon'];


?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">


    <!-- Main content -->
    <section class="content">
        <div class="row">
            <!-- Message Error -->
            <div class="col-sm-12">
                <?php $this->load->view("backend/include/messages");?>
            </div>

        </div>

        <div class="row">

            <div class="col-sm-6">
                <div class="box box-solid">
                    <div class="box-header">
                        <div class="box-title"><b><?=Translate::sprint("Link category with Custom Fields")?></b></div>
                    </div>

                    <div class="box-body">


                        <?php if(ModulesChecker::isEnabled("cf_manager")): ?>
                            <?php
                            $cf_list = $this->mCFManager->getList(
                                SessionManager::getData("id_user")
                            )
                            ?>

                            <div class="form-group">
                                <label><?=_lang("Custom Fields")?></label>
                                <select id="cf_id" class="select2">
                                    <option value="0">-- <?= Translate::sprint('Select Custom Fields') ?></option>
                                    <?php foreach ($cf_list as $cf): ?>
                                        <option value="<?=$cf['id']?>" <?=$category['cf_id']==$cf['id']?"selected":""?>><?=$cf['label']?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                        <?php endif; ?>


                    </div>

                    <div class="box-footer">
                        <button type="submit" id="btnEdit" class="btn btn-primary btn-flat"><?=Translate::sprint("edit")?></button>
                    </div>
                </div>


            </div>


            <!-- /.col -->
        </div>
        <!-- /.row -->
    </section>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->


<?php

$data['category'] = $category;

$script = $this->load->view('nsorder/backend/order_category/html/scripts/edit-script',$data,TRUE);
TemplateManager::addScript($script);

?>

