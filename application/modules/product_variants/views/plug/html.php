<div class="row product-variants">
    <!-- text input -->
    <div class="col-sm-12 variants-list">



        <h3 class="box-title">
            <?php if(isset($title)): ?>
                <b><?= ($title) ?></b>
            <?php else :?>
                <b><?= Translate::sprint("Product Variants") ?></b>
            <?php endif; ?>
        </h3>

        <sup class="text-blue"><i class="mdi mdi-information-outline"></i>
            <?=_lang('Product variants allow businesses to determine how many variations of an individual product are available')?>
        </sup>
        <br>




        <button type="button" class="btn  btn-default create-new-grp-variant">
            <i class="mdi mdi-playlist-check"></i>
            <?=_lang("Create new group")?>
        </button>


        <div class="clearfix"></div><br/>


        <div class="row">
            <div class="col-md-6">
                <div class="row" id="grp-variants-container">

                    <?php

                    $groups = $this->mProduct_variants->laodVariants($id);

                    foreach ($groups as $grp){
                        $data['grp'] = $grp;
                        $this->load->view('product_variants/plug/options/group_row',$data);
                    }


                    ?>

                </div>
            </div>
        </div>



    </div>

</div>

<?php

$modal1 = $this->load->view("product_variants/plug/modal-create-grp",NULL,TRUE);
$modal2 = $this->load->view("product_variants/plug/modal-create-option",NULL,TRUE);
TemplateManager::addHtml($modal1);
TemplateManager::addHtml($modal2);
