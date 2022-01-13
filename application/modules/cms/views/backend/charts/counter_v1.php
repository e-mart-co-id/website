<div class="row">


    <div class="clearfix visible-sm-block"></div>
    <?php foreach ($chart_v1_home as $key => $chart_module): ?>
        <?php if (isset($chart_module['count_label'])): ?>
            <div class="col-md-4">
                <div class="small-box"
                     style="color: <?= (isset($chart_module['color']) ? $chart_module['color'] : '#ff7701') ?> !important; background-color: white;">
                    <div class="inner">
                        <h3><?= $chart_module["count"] ?></h3>
                        <p><?= (isset($chart_module['count_label']) ? Translate::sprint($chart_module['count_label']) : Translate::sprint($key)) ?></p>
                    </div>
                    <div class="icon"
                         style="color: <?= (isset($chart_module['color']) ? $chart_module['color'] : '#ff7701') ?> ">
                        <?= (isset($chart_module['icon_tag']) ? $chart_module['icon_tag'] : "<i class='mdi mdi-google-analytics'></i>") ?>
                    </div>
                    <a href="<?= (isset($chart_module['link']) ? $chart_module['link'] : "#") ?>"
                       class="small-box-footer hidden"><?= _lang("Show all") ?>&nbsp;&nbsp;<i
                                class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div><!-- /.col -->
        <?php endif; ?>
    <?php endforeach; ?>
</div>