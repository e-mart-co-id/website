<?php

$timezones = DateTimeZone::listIdentifiers(DateTimeZone::ALL);
$languages = Translate::getLangsCodes();

?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">

    <section class="content">

        <div class="row">

            <div class="col-sm-12">


                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs">


                        <li class="active">
                            <a href="#payments_methods" class="title uppercase" data-toggle="tab"
                               aria-expanded="false"><?= (Translate::sprint("Payments methods")) ?></a>
                        </li>

                        <?php if (PaymentsProvider::isEnabled("paypal")): ?>
                        <li>
                            <a href="#paypal_config" class="title uppercase" data-toggle="tab"
                               aria-expanded="false"><?= (Translate::sprint("PayPal config")) ?></a>
                        </li>
                        <?php endif; ?>

                        <?php if (PaymentsProvider::isEnabled("stripe")): ?>
                        <li>
                            <a href="#stripe_config" class="title uppercase" data-toggle="tab"
                               aria-expanded="false"><?= (Translate::sprint("Stripe config")) ?></a>
                        </li>
                        <?php endif; ?>

                        <?php if (PaymentsProvider::isEnabled("razorpay")): ?>
                        <li>
                            <a href="#razorpay_config" class="title uppercase" data-toggle="tab"
                               aria-expanded="false"><?= (Translate::sprint("Razorpay config")) ?></a>
                        </li>
                        <?php endif; ?>

                        <?php if (PaymentsProvider::isEnabled("flutterwave")): ?>
                            <li>
                                <a href="#flutterwave_config" class="title uppercase" data-toggle="tab"
                                   aria-expanded="false"><?= (Translate::sprint("Flutterwave config")) ?></a>
                            </li>
                        <?php endif; ?>


                    </ul>


                    <div class="tab-content">
                        <div class="tab-pane active" id="payments_methods">

                            <div class="box-body">
                                <div class="col-sm-12 payment_methods">
                                    <?php foreach (PaymentsProvider::getModules() as $payment): ?>
                                        <div><label><input type="checkbox" class="payment_method"
                                                           value="<?= $payment['id'] ?>" <?= PaymentsProvider::isEnabled($payment['id']) ? "checked" : "" ?>/>&nbsp;&nbsp;<?= _lang($payment['payment']) ?>
                                            </label></div>
                                    <?php endforeach; ?>
                                </div>

                            </div>

                            <div class="box-footer">
                                <button type="button" class="btn  btn-primary" id="btnSave"><span
                                            class="glyphicon glyphicon-check"></span> <?php echo Translate::sprint("Save"); ?>
                                </button>
                            </div>


                        </div>

                        <?php if (PaymentsProvider::isEnabled("paypal")): ?>
                            <div class="tab-pane" id="paypal_config">
                                <div class="box-body">
                                    <strong class="uppercase"><?=_lang("PayPal Config")?></strong>

                                    <br>
                                    <sup class="text-blue"><i class="mdi mdi-information-outline"></i>
                                        <?=_lang('How to get the Client key and secret key ')?>  ? <a href="https://www.angelleye.com/how-to-create-paypal-app"> documentation </a>
                                    </sup>

                                    <form id="form" role="form">

                                        <div class="row">

                                            <div class="col-sm-6 margin">

                                                <div class="form-group ">
                                                    <label><?= Translate::sprint("Mode") ?></label>
                                                    <select id="PAYPAL_CONFIG_DEV_MODE"
                                                            name="PAYPAL_CONFIG_DEV_MODE"
                                                            class="form-control select2 PAYPAL_CONFIG_DEV_MODE">
                                                        <?php
                                                        if (PAYPAL_CONFIG_DEV_MODE == TRUE) {
                                                            echo '<option value="true" selected>Dev</option>';
                                                            echo '<option value="false" >Prod</option>';
                                                        } else {
                                                            echo '<option value="true"  >Dev</option>';
                                                            echo '<option value="false"  selected>Prod</option>';
                                                        }
                                                        ?>
                                                    </select>
                                                </div>

                                                <div class="form-group">
                                                    <label><?= Translate::sprint("Client ID") ?></label>
                                                    <input type="text" class="form-control"
                                                           placeholder="<?= Translate::sprint("Enter") ?> ..."
                                                           name="PAYPAL_CONFIG_CLIENT_ID"
                                                           id="PAYPAL_CONFIG_CLIENT_ID"
                                                           value="<?= PAYPAL_CONFIG_CLIENT_ID ?>">
                                                </div>

                                                <div class="form-group">
                                                    <label><?= Translate::sprint("Secret ID") ?></label>
                                                    <input type="text" class="form-control"
                                                           placeholder="<?= Translate::sprint("Enter") ?> ..."
                                                           name="PAYPAL_CONFIG_SECRET_ID"
                                                           id="PAYPAL_CONFIG_SECRET_ID"
                                                           value="<?= PAYPAL_CONFIG_SECRET_ID ?>">
                                                </div>

                                            </div>

                                        </div>


                                    </form>
                                </div>
                                <!-- /.box-body -->
                                <div class="box-footer">
                                    <button type="button" class="btn  btn-primary" id="btnSave"><span
                                                class="glyphicon glyphicon-check"></span> <?php echo Translate::sprint("Save"); ?>
                                    </button>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if (PaymentsProvider::isEnabled("stripe")): ?>
                            <div class="tab-pane" id="stripe_config">
                                <div class="box-body">

                                    <strong class="uppercase"><?=_lang("Stripe Config")?></strong>
                                    <br>
                                    <sup class="text-blue"><i class="mdi mdi-information-outline"></i>
                                        <?=_lang('How to get the Public key and secret key ')?>  ? <a href="https://stripe.com/docs/keys"> documentation </a>
                                    </sup>

                                    <form id="form" role="form">

                                        <div class="row">

                                            <div class="col-sm-6 margin">

                                                <div class="form-group ">
                                                    <label><?= Translate::sprint("Mode") ?></label>
                                                    <select id="STRIPE_CONFIG_DEV_MODE"
                                                            name="STRIPE_CONFIG_DEV_MODE"
                                                            class="form-control select2 STRIPE_CONFIG_DEV_MODE">
                                                        <?php
                                                        if (STRIPE_CONFIG_DEV_MODE == TRUE) {
                                                            echo '<option value="true" selected>Test</option>';
                                                            echo '<option value="false" >Live</option>';
                                                        } else {
                                                            echo '<option value="true"  >Test</option>';
                                                            echo '<option value="false"  selected>Live</option>';
                                                        }
                                                        ?>
                                                    </select>
                                                </div>

                                                <div class="form-group">
                                                    <label><?= Translate::sprint("Publishable key") ?></label>
                                                    <input type="text" class="form-control"
                                                           placeholder="<?= Translate::sprint("Enter") ?> ..."
                                                           name="STRIPE_PUBLISHABLE_KEY"
                                                           id="STRIPE_PUBLISHABLE_KEY"
                                                           value="<?= STRIPE_PUBLISHABLE_KEY ?>">
                                                </div>

                                                <div class="form-group">
                                                    <label><?= Translate::sprint("Secret key") ?></label>
                                                    <input type="text" class="form-control"
                                                           placeholder="<?= Translate::sprint("Enter") ?> ..."
                                                           name="STRIPE_SECRET_KEY"
                                                           id="STRIPE_SECRET_KEY"
                                                           value="<?= STRIPE_SECRET_KEY ?>">
                                                </div>

                                            </div>

                                        </div>


                                    </form>
                                </div>
                                <!-- /.box-body -->
                                <div class="box-footer">
                                    <button type="button" class="btn  btn-primary" id="btnSave"><span
                                                class="glyphicon glyphicon-check"></span> <?php echo Translate::sprint("Save"); ?>
                                    </button>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if (PaymentsProvider::isEnabled("razorpay")): ?>
                            <div class="tab-pane" id="razorpay_config">
                                <div class="box-body">
                                    <strong class="uppercase"><?=_lang("Razorpay Config")?></strong>
                                    <br>
                                    <sup class="text-blue"><i class="mdi mdi-information-outline"></i>
                                        <?=_lang('How to get the Publishable key and secret key ')?>  ? <a href="https://razorpay.com/docs/payment-gateway/dashboard-guide/settings/api-keys"> documentation </a>
                                    </sup>


                                    <form id="form" role="form">

                                        <div class="row">

                                            <div class="col-sm-6 margin">

                                                <div class="form-group ">
                                                    <label><?= Translate::sprint("Mode") ?></label>
                                                    <select id="RAZORPAY_CONFIG_DEV_MODE"
                                                            name="RAZORPAY_CONFIG_DEV_MODE"
                                                            class="form-control select2 RAZORPAY_CONFIG_DEV_MODE">
                                                        <?php
                                                        if (RAZORPAY_CONFIG_DEV_MODE == TRUE) {
                                                            echo '<option value="true" selected>Test</option>';
                                                            echo '<option value="false" >Live</option>';
                                                        } else {
                                                            echo '<option value="true"  >Test</option>';
                                                            echo '<option value="false"  selected>Live</option>';
                                                        }
                                                        ?>
                                                    </select>
                                                </div>

                                                <div class="form-group">
                                                    <label><?= Translate::sprint("Key ID") ?></label>
                                                    <input type="text" class="form-control"
                                                           placeholder="<?= Translate::sprint("Enter") ?> ..."
                                                           name="RAZORPAY_KEY_ID"
                                                           id="RAZORPAY_KEY_ID"
                                                           value="<?= RAZORPAY_KEY_ID ?>">
                                                </div>

                                                <div class="form-group">
                                                    <label><?= Translate::sprint("Secret key") ?></label>
                                                    <input type="text" class="form-control"
                                                           placeholder="<?= Translate::sprint("Enter") ?> ..."
                                                           name="RAZORPAY_SECRET_KEY"
                                                           id="RAZORPAY_SECRET_KEY"
                                                           value="<?= RAZORPAY_SECRET_KEY ?>">
                                                </div>

                                            </div>

                                        </div>


                                    </form>
                                </div>
                                <!-- /.box-body -->
                                <div class="box-footer">
                                    <button type="button" class="btn  btn-primary" id="btnSave"><span
                                                class="glyphicon glyphicon-check"></span> <?php echo Translate::sprint("Save"); ?>
                                    </button>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if (PaymentsProvider::isEnabled("flutterwave")): ?>
                            <div class="tab-pane" id="flutterwave_config">
                                <div class="box-body">
                                    <strong class="uppercase"><?=_lang("flutterwave Config")?></strong>
                                    <br>
                                    <sup class="text-blue"><i class="mdi mdi-information-outline"></i>
                                        <?=_lang('How to get the Publishable key and secret key ')?>  ? <a href="https://razorpay.com/docs/payment-gateway/dashboard-guide/settings/api-keys"> documentation </a>
                                    </sup>


                                    <form id="form" role="form">

                                        <div class="row">

                                            <div class="col-sm-6 margin">

                                                <div class="form-group ">
                                                    <label><?= Translate::sprint("Mode") ?></label>
                                                    <select id="FLUTTERWAVE_CONFIG_DEV_MODE"
                                                            name="FLUTTERWAVE_CONFIG_DEV_MODE"
                                                            class="form-control select2 FLUTTERWAVE_CONFIG_DEV_MODE">
                                                        <?php
                                                        if (FLUTTERWAVE_CONFIG_DEV_MODE == TRUE) {
                                                            echo '<option value="true" selected>Test</option>';
                                                            echo '<option value="false" >Live</option>';
                                                        } else {
                                                            echo '<option value="true"  >Test</option>';
                                                            echo '<option value="false"  selected>Live</option>';
                                                        }
                                                        ?>
                                                    </select>
                                                </div>

                                                <div class="form-group">
                                                    <label><?= Translate::sprint("Key ID") ?></label>
                                                    <input type="text" class="form-control"
                                                           placeholder="<?= Translate::sprint("Enter") ?> ..."
                                                           name="FLUTTERWAVE_KEY_ID"
                                                           id="FLUTTERWAVE_KEY_ID"
                                                           value="<?= FLUTTERWAVE_KEY_ID ?>">
                                                </div>

                                                <div class="form-group">
                                                    <label><?= Translate::sprint("Secret key") ?></label>
                                                    <input type="text" class="form-control"
                                                           placeholder="<?= Translate::sprint("Enter") ?> ..."
                                                           name="FLUTTERWAVE_SECRET_KEY"
                                                           id="FLUTTERWAVE_SECRET_KEY"
                                                           value="<?= FLUTTERWAVE_SECRET_KEY ?>">
                                                </div>

                                            </div>

                                        </div>


                                    </form>
                                </div>

                                <div class="box-footer">
                                    <button type="button" class="btn  btn-primary" id="btnSave"><span
                                                class="glyphicon glyphicon-check"></span> <?php echo Translate::sprint("Save"); ?>
                                    </button>
                                </div>

                            </div>
                        <?php endif; ?>

                    </div>

                </div>

            </div>

            <div class="col-sm-6">
                <div class="box box-solid hidden">
                    <div class="box-header with-border">
                        <h3 class="box-title"><b> <?php echo Translate::sprint("Payment Config"); ?> </b></h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="col-sm-12">
                            <form id="form" role="form">

                                <div class="form-group">
                                    <label><?= Translate::sprint("Payment Currency") ?></label>
                                    <select id="PAYMENT_CURRENCY" name="PAYMENT_CURRENCY"
                                            class="form-control select2 PAYMENT_CURRENCY" disabled>
                                        <?php

                                        if (defined('PAYMENT_CURRENCY'))
                                            $def_key = PAYMENT_CURRENCY;
                                        else
                                            $def_key = DEFAULT_CURRENCY;

                                        foreach ($currencies as $key => $c) {
                                            if ($def_key == $c['code']) {
                                                echo '<option value="' . $c['code'] . '" selected>' . $c['name'] . ', ' . $c['code'] . '</option>';
                                            } else {
                                                echo '<option value="' . $c['code'] . '">' . $c['name'] . ', ' . $c['code'] . '</option>';
                                            }

                                        }

                                        ?>
                                    </select>
                                    <sub><i class="mdi mdi-information-outline"></i>
                                        <?= Translate::sprint("You should select default currency and supported for the PayPal or other methods") ?>
                                        <br>
                                        <a target="_blank"
                                           href="https://developer.paypal.com/docs/classic/api/currency_codes/">https://developer.paypal.com/docs/classic/api/currency_codes</a>
                                    </sub>
                                </div>


                            </form>
                        </div>
                    </div>
                    <!-- /.box-body -->
                    <div class="box-footer">
                        <button type="button" class="btn  btn-primary" id="btnSave"><span
                                    class="glyphicon glyphicon-check"></span> <?php echo Translate::sprint("Save"); ?>
                        </button>
                    </div>
                </div>
            </div>


        </div>

    </section>

</div>


<?php

$script = $this->load->view('backend/html/scripts/payment-setting-script', NULL, TRUE);
TemplateManager::addScript($script);

?>






