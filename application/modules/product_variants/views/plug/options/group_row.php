<div class="col-md-12 group-<?=$grp['id']?> group" data-id="<?=$grp['id']?>">
    <table class="table">
        <thead>
        <tr class="bg-gray">
            <th colspan="2"><i class="mdi mdi-menu cursor-pointer"></i>&nbsp;&nbsp;<?=$grp['label']?>: <i><?=$grp['option_type']?> <?=_lang("option")?></i>
                <a href="#" data-id="<?=$grp['id']?>" class="pull-right add-option"><i class="mdi mdi-plus-box"></i> <?=_lang("Add option")?></a>

                &nbsp;&nbsp;&nbsp;<a href="#" data-id="<?=$grp['id']?>" class="remove-grp"><i class="mdi mdi-delete text-red"></i>&nbsp;&nbsp;</a>
            </th>
        </tr>
        </thead>

        <tbody>

        <?php
            $options = $this->mProduct_variants->laodVariants($grp['product_id'],$grp['id']);
            if(!empty($options))
            foreach ($options as $opt){
                $data['opt'] = $opt;
                $this->load->view('product_variants/plug/options/option_row',$data);
            }
        ?>

        </tbody>
    </table>
</div>
<div class="clearfix"></div>