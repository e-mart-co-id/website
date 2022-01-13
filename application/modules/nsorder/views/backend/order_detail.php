<?php

$invoice = $this->mOrderPayment->getInvoice($order['id']);

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
            <div class="col-xs-12">
                <div class="box box-solid">
                    <div class="box-header" style="width : 100%;">
                        <div class=" row ">
                            <div class="pull-left col-md-12 box-title">
                                <b><?= Translate::sprint("Order Detail") ?> #<?= $order['id'] ?></b>
                            </div>
                        </div>
                    </div>

                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="pull-right">
                                    <div class="btn-group">

                                        <?php if (ModulesChecker::isEnabled("delivery")): ?>
                                            <?php if ($order["status_id"] == 1): ?><!--RG : show confirm order button when status is pending-->
                                                <a class="btn btn-primary bg-green-gradient"
                                                   id="edit-status-confirm-order"
                                                   href="#"><i
                                                            class="mdi mdi-pencil-box-outline"></i>&nbsp;&nbsp;<?= _lang("Confirm  & deliver") ?>
                                                </a>
                                            <?php elseif ($order["status_id"] == 7): ?><!--RG : show unlock order button when status is reported-->
                                                <a class="btn btn-primary bg-blue-gradient"
                                                   id="edit-status-unlock-order"
                                                   href="#"><i
                                                            class="mdi mdi-pencil-box-outline"></i>&nbsp;&nbsp;<?= _lang("Unlock the order") ?>
                                                </a>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <?php if ($order["status_id"] == 1): ?><!--RG : show confirm order button when status is pending-->
                                                <a class="btn btn-primary bg-green-gradient"
                                                   id="edit-status-confirm-order"
                                                   href="#"><i
                                                            class="mdi mdi-pencil-box-outline"></i>&nbsp;&nbsp;<?= _lang("Confirm order") ?>
                                                </a>
                                            <?php elseif ($order["status_id"] == 7): ?><!--RG : show unlock order button when status is reported-->
                                                <a class="btn btn-primary bg-blue-gradient"
                                                   id="edit-status-unlock-order"
                                                   href="#"><i
                                                            class="mdi mdi-pencil-box-outline"></i>&nbsp;&nbsp;<?= _lang("Unlock the order") ?>
                                                </a>
                                            <?php endif; ?>
                                        <?php endif; ?>


                                        <?php if (isset($status) && !empty($status)): ?>
                                            <a class="btn btn-primary bg-gray" id="edit-status" href="#"><i
                                                        class="mdi mdi-pencil-box-outline"></i>&nbsp;&nbsp;<?= _lang("Edit") ?>
                                            </a>
                                        <?php endif; ?>

                                        <?php if (GroupAccess::isGranted("messenger")): ?>
                                            <a class="btn btn-primary bg-gray"
                                               href="<?= admin_url("messenger/messages/?username=" . $this->mUserModel->getFieldById("username", $order['user_id'])) ?>"><i
                                                        class="mdi mdi-email-outline"></i>&nbsp;&nbsp;<?= _lang("Inbox") ?>
                                            </a>
                                        <?php endif; ?>


                                        <a class="btn btn-primary" target="_blank"
                                           href="<?= admin_url("nsorder/print_order?id=" . $order['id']) ?>"><i
                                                    class="mdi mdi-printer"></i>&nbsp;&nbsp;<?= _lang("Print") ?></a>
                                    </div>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                        <div class="row">
                            <div class="col-sm-5">
                                <div class="box box-solid">
                                    <div class="box-header">
                                        <h4 style="font-family: 'Montserrat' !important"> <?= _lang("Order") ?>
                                            #<?= str_pad($order['id'], 6, 0, STR_PAD_LEFT) ?></h4>
                                        <?= _lang("Order date") ?>
                                        : <?= date("D M Y h:i:s A", strtotime($order['updated_at'])) ?><br>
                                    </div>
                                    <div class="box-body">

                                        <div class="ostatus row padding-bottom">
                                            <div class="col-sm-3">
                                                <strong><?= _lang("Status") ?></strong>
                                            </div>
                                            <div class="col-sm-9">
                                                <?php

                                                if (isset($order['status']) && $order['status'] != "") {
                                                    $statusParser = explode(";", $order['status']);
                                                    echo "<span class=badge style='background:" . $statusParser[1] . "'>" . $statusParser[0] . "</span>";
                                                }

                                                ?>
                                            </div>
                                        </div>


                                        <?php if (ModulesChecker::isEnabled("order_payment")): ?>
                                            <div class="ostatus row padding-bottom">
                                                <div class="col-sm-3">
                                                    <strong> <?= _lang("Payment") ?> <br></strong>
                                                </div>
                                                <div class="col-sm-9">
                                                    <?php

                                                    $pcode = $order['payment_status'];
                                                    $payments = Order_payment::PAYMENT_STATUS;
                                                    if (isset($payments[$pcode])) {
                                                        echo "<span class='badge' style='background-color: " . $payments[$pcode]['color'] . "'>" . ucfirst(_lang($payments[$pcode]['label'])) . "</span>";
                                                    } else if ($pcode == "cod_paid") {
                                                        echo "<span class='badge bg-green'>" . ucfirst(_lang("Paid on delivery")) . "</span>";
                                                    }

                                                    ?>

                                                    <a href="#" id="edit-payment-status"><i
                                                                class="mdi mdi-pencil-box-outline"></i>&nbsp;&nbsp;<?= _lang("Edit payment") ?>
                                                    </a>

                                                </div>
                                            </div>

                                        <?php endif; ?>

                                        <?php if (ModulesChecker::isEnabled("order_payment") && ModulesChecker::isEnabled("payment")
                                            && GroupAccess::isGranted("nsorder", MANAGE_ORDER_CONFIG_ADMIN)): ?>


                                            <div class="ostatus row padding-bottom margin-top15px">
                                                <div class="col-sm-3">

                                                    <strong>
                                                        <?php
                                                        $store = $this->mOrder->getStoreFromCart($order['id']);
                                                        echo _lang("Business");
                                                        ?>
                                                    </strong>

                                                </div>
                                                <div class="col-sm-9">
                                                    <a target="_blank"
                                                       href="<?= admin_url("user/edit?id=" . $store['user_id']) ?>"><?= $store['name'] ?>
                                                        (<?= ucfirst($this->mUserModel->getUserNameById($store['user_id'])) ?>
                                                        )
                                                        <i class="mdi mdi-open-in-new"></i></a>

                                                </div>

                                            </div>


                                            <?php if ($invoice != NULL && $invoice->transaction_id != "") { ?>
                                                <div class="ostatus row padding-bottom">
                                                    <div class="col-sm-4">
                                                        <strong>
                                                            <?php echo _lang("Transaction ID"); ?>
                                                        </strong>
                                                    </div>
                                                    <div class="col-sm-9">
                                                        <?php echo Translate::sprintf("%s <br> <strong> Payment: </strong>  <span class='badge bg-orange-active'>%s </span>", array($invoice->transaction_id, _lang($invoice->method))); ?>

                                                    </div>

                                                </div>

                                            <?php } ?>

                                        <?php endif; ?>


                                        <?php if (ModulesChecker::isEnabled("delivery")) {

                                            if ($order["delivery_id"] > 0) { ?>

                                                <div class="ostatus row padding-bottom">
                                                    <div class="col-sm-3">
                                                        <strong> <?php echo _lang("Delivery"); ?> </strong>
                                                    </div>

                                                    <div class="col-sm-9">
                                                        <?php

                                                        if ($order['delivery_status'] == 0) {

                                                        } else if ($order['delivery_status'] == 1) {
                                                            echo "<span class='text-blue'>" . _lang("Ongoing") . "</span> " . "- ";
                                                        } else if ($order['delivery_status'] == 2) {
                                                            echo "<span class='text-blue'>" . _lang("Picked up") . "</span> " . " - ";
                                                        } else if ($order['delivery_status'] == 3) {
                                                            echo "<span class='text-green'>" . _lang("Delivered") . "</span> " . " - ";
                                                        } else if ($order['delivery_status'] == 4) {
                                                            echo "<span class='text-red'>" . _lang("Reported by") . "</span> " . " - ";
                                                        }

                                                        ?>
                                                        <a target="_blank"
                                                           href="<?= admin_url("user/edit?id=" . $order["delivery_id"]) ?>"><?= ucfirst($this->mUserModel->getUserNameById($order["delivery_id"])) ?>
                                                            <i class="mdi mdi-open-in-new"></i></a>

                                                    </div>

                                                </div>

                                            <?php }
                                        } ?>


                                    </div>


                                </div>
                            </div>
                            <div class="col-sm-2"></div>
                            <div class="col-sm-5 pull-right">
                                <div class="box box-solid">
                                    <div class="box-header">
                                        <h4 style="font-family: 'Montserrat' !important"><?= _lang("Client Information") ?></h4>
                                    </div>
                                    <div class="box-body">

                                        <?php

                                        $cf_id = intval($order['req_cf_id']);
                                        $order['req_cf_data'] = json_decode($order['req_cf_data'], JSON_OBJECT_AS_ARRAY);
                                        if (isset($order['req_cf_data'])) {

                                            $cf_object = CFManagerHelper::getByID($cf_id);
                                            $fields = json_decode($cf_object['fields'], JSON_OBJECT_AS_ARRAY);

                                            foreach ($fields as $key => $field) {

                                                $data = $order['req_cf_data'][$field['label']];

                                                if ($data == "")
                                                    $data = "--";


                                                if ($field['type'] == "input.location") {

                                                    if ($key == "") {
                                                        echo "<span><strong>" . $field['label'] . "</strong>: -- </span><br>";
                                                    } else {

                                                        if (preg_match("#;#", $data)) {
                                                            $l = explode(";", $data);
                                                            echo "<span><strong>" . $field['label'] . "</strong>: <a class='loc-detail' href='#' data-address='$l[0]' data-lat='$l[1]' data-lng='$l[2]'><i class='mdi mdi-map-marker'></i>&nbsp;&nbsp;$l[0]</a> </span><br>";
                                                        } else {
                                                            echo "<span><strong>" . $field['label'] . "</strong>: $data </span><br>";
                                                        }

                                                    }
                                                } else
                                                    echo "<span><strong>" . $field['label'] . "</strong>: $data</span><br>";

                                            }
                                        }

                                        ?>

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12" style="margin-top: 20px">
                                <div class="col-xs-12 table-responsive">
                                    <table class="table table-striped">
                                        <tbody>
                                        <tr style="text-transform: uppercase">
                                            <th width="60%"><?= _lang("Item (s)") ?></th>
                                            <th align="right" width="20%"
                                                class="right-align"><?= _lang("Price per item") ?></th>
                                            <th align="right" width="20%"
                                                class="right-align"><?= _lang("Amount") ?></th>
                                        </tr>


                                        <?php
                                        $cart = json_decode($order['cart'], JSON_OBJECT_AS_ARRAY);

                                        $sub_total = 0;
                                        $currency = "USD";

                                        ?>

                                        <?php foreach ($cart as $item): ?>
                                            <tr>
                                                <td>
                                                    <?php


                                                    $callback = BookmarkLinkedModule::find($item['module'], 'getData');

                                                    if ($callback != NULL) {

                                                        $params = array(
                                                            'id' => $item['module_id']
                                                        );

                                                        $result = call_user_func($callback, $params);

                                                        echo $result['label'] . " x " . intval($item['qty']);


                                                        if (isset($item['variants']))
                                                            echo OrderHelper::variantsBuilderString($item['variants']);


                                                    }


                                                    ?>
                                                </td>
                                                <td align="right" valign="top">


                                                    <?php

                                                    if (!empty($result['currency']) && is_array($result['currency'])) {
                                                        echo Currency::parseCurrencyFormat($item['amount'], $result['currency']['code']);
                                                        $currency = $result['currency']['code'];
                                                    } else if (is_string($result['currency'])) {
                                                        $currency = $result['currency'];
                                                        echo Currency::parseCurrencyFormat($item['amount'], $result['currency']);
                                                    } else
                                                        echo Currency::parseCurrencyFormat($item['amount'], DEFAULT_CURRENCY);

                                                    ?>
                                                </td>
                                                <td align="right" valign="top">

                                                    <?php

                                                    if (!empty($result['currency']) && is_array($result['currency'])) {
                                                        echo Currency::parseCurrencyFormat($item['amount'] * $item['qty'], $result['currency']['code']);
                                                        $currency = $result['currency']['code'];
                                                    } else if (is_string($result['currency'])) {
                                                        $currency = $result['currency'];
                                                        echo Currency::parseCurrencyFormat($item['amount'] * $item['qty'], $result['currency']);
                                                    } else
                                                        echo Currency::parseCurrencyFormat($item['amount'] * $item['qty'], DEFAULT_CURRENCY);

                                                    $sub_total = $sub_total + $item['amount'] * $item['qty'];

                                                    ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>

                                <div class="clearfix" style="margin-bottom: 20px;"></div>

                                <div class="col-sm-4">
                                </div>


                                <div class="col-sm-4">
                                </div>


                                <div class="col-md-4">
                                    <table class="table table-hover">
                                        <tbody>
                                        <tr id="sub_amount">
                                            <th width="40%"><span class="margin"><?= _lang("SUBTOTAL") ?></span></th>
                                            <td width="60%" align="right">
                                                <strong id="amount_init" style="font-size: 17px;">
                                                    <?php
                                                    echo Currency::parseCurrencyFormat($sub_total, $currency);
                                                    ?>
                                                </strong>
                                            </td>
                                        </tr>


                                        <?php if (defined('DEFAULT_TAX') and DEFAULT_TAX > 0): ?>

                                            <?php

                                            $percent = 0;
                                            $tax = $this->mTaxModel->getTax(DEFAULT_TAX);
                                            if ($tax != NULL) {
                                                $percent = $tax['value'];
                                            }

                                            $tax_value = (($percent / 100) * $sub_total);
                                            $sub_total = $tax_value + $sub_total;

                                            ?>

                                            <tr>
                                                <td>
                                                    <span class="margin"><?= $tax['name'] ?>(<?= intval($percent) ?>%)</span>
                                                </td>
                                                <td align="right">
                                                    <b><?= Currency::parseCurrencyFormat($tax_value, $currency) ?></b>
                                                </td>
                                            </tr>

                                        <?php endif; ?>


                                        <?php


                                        if ($invoice->extras != NULL)
                                            $extras = json_decode($invoice->extras, JSON_OBJECT_AS_ARRAY);
                                        else
                                            $extras = array();

                                        ?>

                                        <?php $extras = json_decode($invoice->extras, JSON_OBJECT_AS_ARRAY); ?>

                                        <?php if (isset($extras) && empty($extras)) :
                                            foreach ($extras as $key => $value): ?>

                                                <?php
                                                $sub_total = $value + $sub_total;
                                                ?>
                                                <tr>
                                                    <td>
                                                        <span class="margin"><?= _lang($key) ?></span>
                                                    </td>
                                                    <td align="right">
                                                        <b><?= Currency::parseCurrencyFormat($value, $currency) ?></b>
                                                    </td>
                                                </tr>

                                            <?php endforeach;
                                        endif; ?>

                                        <tr>
                                            <th>
                                                <span class="margin"><?= _lang("TOTAL") ?></span>
                                                <?php if (isset($percent) && $percent > 0): ?>
                                                    <br/>
                                                    <span class="margin text-grey2"><i><?= _lang("Tax included") ?></i></span>
                                                <?php endif; ?>
                                            </th>
                                            <td align="right" id="currency">
                                                <strong id="amount_total" style="font-size: 17px;">
                                                    <?php
                                                    echo Currency::parseCurrencyFormat($sub_total, $currency);
                                                    ?>
                                                </strong>
                                            </td>
                                        </tr>

                                        </tbody>
                                    </table>
                                </div>


                            </div>
                        </div>

                    </div>

                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->

        </div>
    </section>
</div>


<div class="modal fade" id="modal-location-detail">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?= Translate::sprint("Location Detail") ?></h4>
            </div>
            <div class="modal-body">

                <div class="row">
                    <div id="loc-address" style="padding-bottom: 15px;padding-left: 15px;">
                        <strong><?= _lang("Address") ?></strong>: <span></span></div>
                    <div id="loc-maps" style="width:100%;height:300px;margin-bottom: 15px"></div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" data-dismiss="modal"
                        class="btn btn-flat btn-primary pull-right"><?= Translate::sprint("DONE") ?></button>
            </div>
        </div>

        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>


<div class="modal fade" id="modal-edit-status">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?= Translate::sprint("Edit Status") ?></h4>
            </div>
            <div class="modal-body">
                <?php if (isset($status) && !empty($status)): ?>
                    <div class="form-group">
                        <label><?= _lang("Select Order status") ?></label>
                        <select class="form-control select2" id="select2-order-status">
                            <?php foreach ($status as $s): ?>
                                <option value="<?= $s['id'] ?>" <?= $s['id'] == $order['status_id'] ? "selected" : "" ?>><?= $s['label'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                <?php endif; ?>


                <div class="form-group hidden message_container">
                    <label><?= _lang("Include a message to the client") ?></label>
                    <textarea class="form-control" id="c_message"
                              placeholder="<?= _lang("Enter message...") ?>"></textarea>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" data-dismiss="modal"
                        class="btn btn-flat btn-default pull-left"><?= Translate::sprint("CANCEL") ?></button>
                <button type="button" id="update-status"
                        class="btn btn-flat btn-primary pull-right"><?= Translate::sprint("SAVE") ?></button>
            </div>
        </div>

        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>


<?php if (ModulesChecker::isEnabled("order_payment") && ModulesChecker::isEnabled("payment")
    && GroupAccess::isGranted("nsorder", MANAGE_ORDER_CONFIG_ADMIN)): ?>

    <div class="modal fade" id="modal-edit--payment-status">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"><?= Translate::sprint("Edit Payment") ?></h4>
                </div>
                <div class="modal-body">

                    <div class="form-group">
                        <label><?= _lang("Select status") ?></label>
                        <select class="form-control select2" id="select2-payment-status">
                            <?php foreach (Order_payment::PAYMENT_STATUS as $k => $ps): ?>
                                <option value="<?= $k ?>" <?= $k == $order['payment_status'] ? "selected" : "" ?>><?= _lang($ps['label']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" data-dismiss="modal"
                            class="btn btn-flat btn-default pull-left"><?= Translate::sprint("CANCEL") ?></button>
                    <button type="button" id="update-payment-status"
                            class="btn btn-flat btn-primary pull-right"><?= Translate::sprint("SAVE") ?></button>
                </div>
            </div>

            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>

<?php endif; ?>



<?php

$data = array();
$data["order_id"] = $order['id'];
$script = $this->load->view('nsorder/backend/scripts/order-detail-script', $data, TRUE);
TemplateManager::addScript($script);

?>
