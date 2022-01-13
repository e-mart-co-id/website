<?php


    $modules = url_get_content("https://droidev-tech.com/dealify-modules.json");
    $modules  = json_decode($modules,JSON_OBJECT_AS_ARRAY);

?>



<?php if(count($modules)>0):?>

<?php foreach ($modules as $module): ?>
    <div class="col-sm-12 col-md-6 module_item module_item_<?=$module['name']?>">
        <div class="box box-solid">
            <div class="box-body">
                <h4 class="uppercase" style="background-color:#f7f7f7; font-size: 18px; padding: 7px 10px; margin-top: 0;">
                    <?=$module['name']?>
                </h4>
                <div class="media">
                    <div class="media-left">
                        <a href="#" class="ad-click-event">
                            <img src="<?=$module['icon']?>" alt="<?=$module['name']?>" class="media-object" style="width: 150px;height: auto;border-radius: 4px;box-shadow: 0 1px rgba(0,0,0,.15);border: 1px solid #eeeeee;">
                        </a>
                    </div>
                    <div class="media-body">
                        <div class="clearfix">
                            <p><?=$module['description']?></p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="box-footer ">


                <?php if (!ModulesChecker::isRegistred($module['module'])): ?>
                    <a target="_blank" href="<?=$module['path']?>"><button data-button="<?=$module["module"]?>" id="m_install"
                            class="btn btn-flat uppercase cursor-pointer bg-blue btn-sm ad-click-event pull-right">
                        <?= Translate::sprint("Purchase") ?>
                    </button></a>
                <?php elseif (ModulesChecker::isRegistred($module['module'])): ?>
                    <a href="<?=admin_url("modules_manager/manage")?>"><button data-button="<?=$module["module"]?>" id="m_install"
                                                           class="btn btn-flat uppercase cursor-pointer bg-green btn-sm ad-click-event pull-right">
                            <?= Translate::sprint("Purchased") ?>
                        </button></a>
                <?php endif; ?>




            </div>
        </div>
    </div>
<?php endforeach; ?>
<?php endif; ?>



