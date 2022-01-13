<?php

$subscription = $this->stripe_recurring_model->getSubscription(
    SessionManager::getData("id_user")
);

?>
<div class="col-md-6">
    <div class="box box-solid">
        <div class="box-header with-border">
            <h3 class="box-title"><strong><?= Translate::sprint("Subscription & Automatic Payments") ?></strong>
            </h3>
        </div>
        <!-- /.box-header -->

        <div class="box-body margin">
            <div class="form-group">

                <span class="text-blue">
                    <?php


                    $expired_date = $this->mUserBrowser->getData('will_expired');
                    $days = MyDateUtils::getDays($expired_date);

                    $grp_access_id = $this->mUserBrowser->getData('grp_access_id');
                    $pack_id = $this->mUserBrowser->getData('pack_id');


                    $pack = $this->mPack->getPack($pack_id);


                    $trial_period_date = $this->mUserBrowser->getData('trial_period_date');

                    if ($trial_period_date != NULL)
                        $trial_period = MyDateUtils::getDays($trial_period_date);
                    else
                        $trial_period = 0;


                    ?>

                    <?php if ($pack_id > 0): ?>

                        <?php if ($subscription[0]->is_trial == 0): ?>

                            <?php if ($days > 7): ?>
                                <i class="mdi mdi-account font-size18px"></i> <?= Translate::sprintf("Your <u>%s</u>  plan will expire after %s", array($pack->name, PackHelper::getValidDur($expired_date))) ?>.
                            <?php elseif ($days < 7 && $days > 0): ?>
                                <i class="fa fa-warning text-yellow font-size18px"></i> <?= Translate::sprintf("Your <u>%s</u>  plan will expire after %s", array($pack->name, PackHelper::getValidDur($expired_date))) ?>.
                                <u class="text-blue cursor-pointer"
                                   onclick="location.href = '<?= admin_url("pack/renew") ?>';"><?= Translate::sprint("Renew") ?></u>
                            <?php else: ?>
                                <i class="fa fa-warning text-red font-size18px"></i> <?= Translate::sprintf("Your <u>%s</u> plan has been expired", array($pack->name)) ?>.
                                <u class="text-blue cursor-pointer"
                                   onclick="location.href = '<?= admin_url("pack/renew") ?>';"><?= Translate::sprint("Renew") ?></u>
                            <?php endif; ?>

                        <?php else: ?>

                             <?php if ($days > 0): ?>
                                <i class="mdi mdi-account font-size18px"></i>  <?= Translate::sprintf("Your <u>%s</u>  plan will expire after %s", array("Trial", PackHelper::getValidDur($expired_date))) ?>.
                            <?php else: ?>
                                <i class="mdi mdi-account font-size18px"></i>  <?= Translate::sprint("Your <u>%s</u> plan has been expired") ?>.
                            <?php endif; ?>

                        <?php endif; ?>


                    <?php else: ?>

                    <?php endif; ?>

                    <br><br>
                    <a class="text-red cursor-pointer" id="cancelSubscription" href="<?=site_url("stripe_recurring/ajax/cancelSubscription")?>"><i class="mdi mdi-exit-to-app"></i>&nbsp;&nbsp;<?= Translate::sprint("Cancel your subscription") ?></a>


                </span>
            </div>
        </div>

    </div>
    <!-- /.box-body -->
</div>

<div class="col-md-6">
    <div class="box box-solid">
        <div class="box-header with-border">
            <h3 class="box-title"><strong><?= Translate::sprint("Billing") ?></strong>
            </h3>
        </div>
        <!-- /.box-header -->

        <div class="box-body margin">

            <div>
                <label><?= Translate::sprint("Next Payment") ?>:</label> <?=date("Y-m-d h:i A",strtotime($subscription[0]->next_billing))?>
            </div>


            <?php

                $billingInfo = $this->mPaymentModel->getBillingInfo(
                    intval($this->mUserBrowser->getData('id_user'))
                );

            ?>


            <div>
                <label><?= Translate::sprint("Last Transaction") ?>:</label><br>
                <?php

                if ($billingInfo['transaction'] != NULL) {

                    $transaction = $billingInfo['transaction']->transaction_id;
                    $method = $billingInfo['invoice']->method;

                    echo "ID: ".$transaction." - (".$method.") " ;


                }else{
                    echo Translate::sprint("No transaction");
                }


                if ($billingInfo['invoice'] != NULL) {
                    if ($billingInfo['invoice']->status == 1)
                        echo "<br/>" . Translate::sprint("Date") . ": " . date("Y-m-d h:i A",strtotime($billingInfo['invoice']->updated_at));
                }


                ?>
            </div>

        </div>

    </div>
    <!-- /.box-body -->
</div>

<?php

$script = $this->load->view("stripe_recurring/plug/script",NULL,TRUE);
TemplateManager::addScript($script);

